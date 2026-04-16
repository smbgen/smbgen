<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSocialPostRequest;
use App\Models\CmsImage;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\SocialPostTarget;
use App\Services\ActivityLogger;
use App\Services\Social\SocialMediaService;
use Illuminate\Http\Request;

class SocialPostController extends Controller
{
    public function __construct(private readonly SocialMediaService $service) {}

    /**
     * Post queue / status dashboard.
     */
    public function index(Request $request)
    {
        $query = SocialPost::with(['user', 'targets.socialAccount', 'media'])
            ->where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->orderByDesc('created_at')->paginate(20);
        $statuses = SocialPost::ALL_STATUSES;
        $metrics = $this->service->getMetrics(auth()->id());

        return view('admin.social.posts.index', compact('posts', 'statuses', 'metrics'));
    }

    /**
     * Post composer form.
     */
    public function create()
    {
        $accounts = SocialAccount::where('active', true)
            ->where('connection_status', SocialAccount::STATUS_CONNECTED)
            ->orderBy('platform')
            ->get();

        $recentImages = CmsImage::orderByDesc('created_at')->take(12)->get();

        return view('admin.social.posts.create', compact('accounts', 'recentImages'));
    }

    /**
     * Store a new post (draft or scheduled).
     */
    public function store(StoreSocialPostRequest $request)
    {
        $post = $this->service->createPost(auth()->id(), [
            'caption' => $request->caption,
            'account_ids' => $request->account_ids,
            'scheduled_at' => $request->scheduled_at ? new \DateTime($request->scheduled_at) : null,
            'requires_approval' => (bool) $request->requires_approval,
            'source_type' => $request->source_type,
            'source_id' => $request->source_id,
        ]);

        // Attach selected CMS images as media
        if ($request->filled('cms_image_ids')) {
            foreach ($request->cms_image_ids as $imageId) {
                $image = CmsImage::find($imageId);
                if ($image) {
                    $this->service->attachCmsImage($post, $image);
                }
            }
        }

        // If a schedule time was provided, schedule the post
        if ($request->filled('scheduled_at')) {
            $this->service->schedule($post, new \DateTime($request->scheduled_at));
        }

        ActivityLogger::log('social_post_created', "Created social post #{$post->id}", $post);

        $statusLabel = $post->fresh()->status === SocialPost::STATUS_SCHEDULED ? 'scheduled' : 'saved as draft';

        return redirect()->route('admin.social.posts.index')
            ->with('success', "Post {$statusLabel} successfully.");
    }

    /**
     * Show a post and its publish history.
     */
    public function show(SocialPost $socialPost)
    {
        $socialPost->load(['targets.socialAccount', 'targets.attempts', 'media.mediable', 'user', 'approvedBy']);

        return view('admin.social.posts.show', ['post' => $socialPost]);
    }

    /**
     * Edit a draft or failed post.
     */
    public function edit(SocialPost $socialPost)
    {
        abort_if(
            ! in_array($socialPost->status, [SocialPost::STATUS_DRAFT, SocialPost::STATUS_FAILED]),
            403,
            'Only draft or failed posts can be edited.'
        );

        $socialPost->load(['targets', 'media']);

        $accounts = SocialAccount::where('active', true)
            ->where('connection_status', SocialAccount::STATUS_CONNECTED)
            ->orderBy('platform')
            ->get();

        $recentImages = CmsImage::orderByDesc('created_at')->take(12)->get();

        // Pre-selected CMS image IDs for the Alpine.js media picker
        $selectedImageIds = $socialPost->media
            ->where('mediable_type', CmsImage::class)
            ->pluck('mediable_id')
            ->filter()
            ->values()
            ->toArray();

        return view('admin.social.posts.edit', [
            'post' => $socialPost,
            'accounts' => $accounts,
            'recentImages' => $recentImages,
            'selectedImageIds' => $selectedImageIds,
        ]);
    }

    /**
     * Update a draft or failed post.
     */
    public function update(StoreSocialPostRequest $request, SocialPost $socialPost)
    {
        abort_if(
            ! in_array($socialPost->status, [SocialPost::STATUS_DRAFT, SocialPost::STATUS_FAILED]),
            403,
            'Only draft or failed posts can be edited.'
        );

        $socialPost->update(['caption' => $request->caption]);

        // Re-sync targets
        $socialPost->targets()->delete();
        foreach ($request->account_ids as $accountId) {
            SocialPostTarget::create([
                'social_post_id' => $socialPost->id,
                'social_account_id' => $accountId,
                'status' => SocialPostTarget::STATUS_PENDING,
            ]);
        }

        // Re-sync media
        $socialPost->media()->delete();
        if ($request->filled('cms_image_ids')) {
            foreach ($request->cms_image_ids as $imageId) {
                $image = CmsImage::find($imageId);
                if ($image) {
                    $this->service->attachCmsImage($socialPost, $image);
                }
            }
        }

        // Update schedule
        if ($request->filled('scheduled_at')) {
            $this->service->schedule($socialPost, new \DateTime($request->scheduled_at));
        } else {
            $socialPost->update(['status' => SocialPost::STATUS_DRAFT, 'scheduled_at' => null]);
        }

        ActivityLogger::log('social_post_updated', "Updated social post #{$socialPost->id}", $socialPost);

        return redirect()->route('admin.social.posts.show', $socialPost)
            ->with('success', 'Post updated.');
    }

    /**
     * Cancel a scheduled post.
     */
    public function cancel(SocialPost $socialPost)
    {
        $this->service->cancel($socialPost);

        ActivityLogger::log('social_post_cancelled', "Cancelled social post #{$socialPost->id}", $socialPost);

        return back()->with('success', 'Post cancelled.');
    }

    /**
     * Approve a post that requires approval.
     */
    public function approve(SocialPost $socialPost)
    {
        abort_if($socialPost->approved_at !== null, 403, 'Post is already approved.');

        $this->service->approve($socialPost, auth()->id());

        ActivityLogger::log('social_post_approved', "Approved social post #{$socialPost->id}", $socialPost);

        return back()->with('success', 'Post approved and will publish at the scheduled time.');
    }

    /**
     * Retry a failed target.
     */
    public function retryTarget(SocialPostTarget $target)
    {
        abort_if(! $target->canRetry(), 403, 'This target cannot be retried.');

        $this->service->retryTarget($target);

        ActivityLogger::log('social_post_target_retried', "Retried target #{$target->id}", $target);

        return back()->with('success', 'Retry queued.');
    }

    /**
     * Delete a draft post.
     */
    public function destroy(SocialPost $socialPost)
    {
        abort_if($socialPost->status === SocialPost::STATUS_PUBLISHING, 403, 'Cannot delete a post that is currently publishing.');

        $socialPost->delete();

        ActivityLogger::log('social_post_deleted', "Deleted social post #{$socialPost->id}");

        return redirect()->route('admin.social.posts.index')
            ->with('success', 'Post deleted.');
    }
}
