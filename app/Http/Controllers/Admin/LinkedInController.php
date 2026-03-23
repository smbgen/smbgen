<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Services\LinkedIn\LinkedInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class LinkedInController extends Controller
{
    public function __construct(
        private readonly LinkedInService $linkedIn
    ) {}

    /**
     * LinkedIn module dashboard — connected accounts overview.
     */
    public function index()
    {
        $accounts = auth()->user()->socialAccounts()
            ->linkedIn()
            ->withCount(['socialPosts', 'socialPosts as scheduled_posts_count' => fn ($q) => $q->scheduled(), 'socialPosts as published_posts_count' => fn ($q) => $q->published()])
            ->get();

        return view('admin.linkedin.index', compact('accounts'));
    }

    /**
     * Redirect to LinkedIn OAuth.
     */
    public function redirectToLinkedIn()
    {
        $state = Str::random(40);
        session(['linkedin_oauth_state' => $state]);

        return redirect($this->linkedIn->getAuthorizationUrl($state));
    }

    /**
     * Handle LinkedIn OAuth callback.
     */
    public function handleCallback(Request $request)
    {
        if ($request->has('error')) {
            return Redirect::route('admin.linkedin.index')
                ->with('error', 'LinkedIn connection denied: ' . $request->get('error_description', 'Unknown error'));
        }

        if ($request->get('state') !== session('linkedin_oauth_state')) {
            return Redirect::route('admin.linkedin.index')
                ->with('error', 'Invalid OAuth state. Please try again.');
        }

        try {
            $tokenData = $this->linkedIn->exchangeCodeForToken($request->get('code'));
            $accessToken = $tokenData['access_token'];

            $profile = $this->linkedIn->getMemberProfile($accessToken);

            $account = SocialAccount::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'platform' => 'linkedin',
                    'account_url' => $profile['sub'] ?? null,
                ],
                [
                    'account_name' => $profile['name'] ?? ($profile['given_name'] . ' ' . $profile['family_name']),
                    'credentials' => [
                        'access_token' => $accessToken,
                        'refresh_token' => $tokenData['refresh_token'] ?? null,
                    ],
                    'access_token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
                    'active' => true,
                ]
            );

            // Fetch org pages the user administers
            $organizations = $this->linkedIn->getOrganizations($account);
            if (! empty($organizations)) {
                // Auto-select first org page; user can switch later
                $org = $organizations[0];
                $orgData = $org['organization~'] ?? [];
                $account->update([
                    'page_id' => $orgData['id'] ?? null,
                    'page_name' => $orgData['localizedName'] ?? null,
                ]);
            }

            session()->forget('linkedin_oauth_state');

            return Redirect::route('admin.linkedin.index')
                ->with('status', 'LinkedIn account connected successfully!');

        } catch (\Exception $e) {
            Log::error('LinkedIn OAuth callback failed', ['error' => $e->getMessage()]);

            return Redirect::route('admin.linkedin.index')
                ->with('error', 'LinkedIn connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect a LinkedIn account.
     */
    public function disconnect(SocialAccount $account)
    {
        $this->authorizeAccount($account);
        $account->delete();

        return Redirect::route('admin.linkedin.index')
            ->with('status', 'LinkedIn account disconnected.');
    }

    // ─── Posts ────────────────────────────────────────────────────────────────

    /**
     * List posts for a given account.
     */
    public function postsIndex(Request $request)
    {
        $accounts = auth()->user()->socialAccounts()->linkedIn()->active()->get();

        $accountId = $request->get('account_id', $accounts->first()?->id);
        $status = $request->get('status', 'all');

        $postsQuery = SocialPost::query()
            ->whereIn('social_account_id', $accounts->pluck('id'))
            ->with('socialAccount')
            ->latest();

        if ($status !== 'all') {
            $postsQuery->where('status', $status);
        }

        $posts = $postsQuery->paginate(20)->withQueryString();

        $counts = [
            'all' => SocialPost::whereIn('social_account_id', $accounts->pluck('id'))->count(),
            'draft' => SocialPost::whereIn('social_account_id', $accounts->pluck('id'))->draft()->count(),
            'scheduled' => SocialPost::whereIn('social_account_id', $accounts->pluck('id'))->scheduled()->count(),
            'published' => SocialPost::whereIn('social_account_id', $accounts->pluck('id'))->published()->count(),
            'failed' => SocialPost::whereIn('social_account_id', $accounts->pluck('id'))->failed()->count(),
        ];

        return view('admin.linkedin.posts.index', compact('posts', 'accounts', 'status', 'counts'));
    }

    /**
     * Show the post creation editor.
     */
    public function postsCreate()
    {
        $accounts = auth()->user()->socialAccounts()->linkedIn()->active()->get();

        if ($accounts->isEmpty()) {
            return Redirect::route('admin.linkedin.index')
                ->with('error', 'Please connect a LinkedIn account before creating posts.');
        }

        return view('admin.linkedin.posts.create', ['accounts' => $accounts->toArray()]);
    }

    /**
     * Store a new post (draft or scheduled).
     */
    public function postsStore(Request $request)
    {
        $data = $request->validate([
            'social_account_id' => 'required|exists:social_accounts,id',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:3000',
            'status' => 'required|in:draft,scheduled',
            'scheduled_at' => 'required_if:status,scheduled|nullable|date|after:now',
            'media_paths' => 'nullable|array',
            'media_paths.*' => 'string',
        ]);

        $this->authorizeAccount(SocialAccount::findOrFail($data['social_account_id']));

        $post = SocialPost::create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        $message = $post->isScheduled()
            ? 'Post scheduled for ' . $post->scheduled_at->format('M j, Y g:i A') . '.'
            : 'Post saved as draft.';

        return Redirect::route('admin.linkedin.posts.index')
            ->with('status', $message);
    }

    /**
     * Edit an existing post.
     */
    public function postsEdit(SocialPost $post)
    {
        $this->authorizePost($post);
        $accounts = auth()->user()->socialAccounts()->linkedIn()->active()->get();

        return view('admin.linkedin.posts.edit', ['post' => $post, 'accounts' => $accounts->toArray()]);
    }

    /**
     * Update a post.
     */
    public function postsUpdate(Request $request, SocialPost $post)
    {
        $this->authorizePost($post);

        if ($post->isPublished()) {
            return back()->with('error', 'Published posts cannot be edited.');
        }

        $data = $request->validate([
            'social_account_id' => 'required|exists:social_accounts,id',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:3000',
            'status' => 'required|in:draft,scheduled',
            'scheduled_at' => 'required_if:status,scheduled|nullable|date|after:now',
            'media_paths' => 'nullable|array',
            'media_paths.*' => 'string',
        ]);

        $post->update($data);

        return Redirect::route('admin.linkedin.posts.index')
            ->with('status', 'Post updated successfully.');
    }

    /**
     * Delete a post.
     */
    public function postsDestroy(SocialPost $post)
    {
        $this->authorizePost($post);
        $post->delete();

        return Redirect::route('admin.linkedin.posts.index')
            ->with('status', 'Post deleted.');
    }

    /**
     * Publish a post immediately.
     */
    public function postsPublish(SocialPost $post)
    {
        $this->authorizePost($post);

        if ($post->isPublished()) {
            return back()->with('error', 'Post is already published.');
        }

        try {
            $linkedinPostId = $this->linkedIn->createPost($post);

            $post->update([
                'status' => SocialPost::STATUS_PUBLISHED,
                'published_at' => now(),
                'linkedin_post_id' => $linkedinPostId,
                'error_message' => null,
            ]);

            return Redirect::route('admin.linkedin.posts.index')
                ->with('status', 'Post published to LinkedIn!');

        } catch (\Exception $e) {
            Log::error('LinkedIn immediate publish failed', ['post_id' => $post->id, 'error' => $e->getMessage()]);

            $post->update([
                'status' => SocialPost::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to publish: ' . $e->getMessage());
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeAccount(SocialAccount $account): void
    {
        abort_unless($account->user_id === auth()->id(), 403);
    }

    private function authorizePost(SocialPost $post): void
    {
        abort_unless($post->user_id === auth()->id(), 403);
    }
}
