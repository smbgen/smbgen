<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SocialPlatform;
use App\Enums\SocialPostStatus;
use App\Http\Controllers\Controller;
use App\Jobs\PublishSocialPostJob;
use App\Models\SocialPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SignalController extends Controller
{
    public function index(Request $request): View
    {
        $query = SocialPost::with('client')->latest();

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(50);
        $platforms = SocialPlatform::cases();
        $statuses = SocialPostStatus::cases();

        $stats = [
            'total' => SocialPost::count(),
            'scheduled' => SocialPost::where('status', SocialPostStatus::Scheduled)->count(),
            'published' => SocialPost::where('status', SocialPostStatus::Published)->count(),
        ];

        return view('admin.signal.index', compact('posts', 'platforms', 'statuses', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => ['required', 'in:'.implode(',', array_column(SocialPlatform::cases(), 'value'))],
            'content' => ['required', 'string', 'max:3000'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ]);

        $post = SocialPost::create([
            ...$validated,
            'status' => ($validated['scheduled_at'] ?? null) ? SocialPostStatus::Scheduled : SocialPostStatus::Draft,
            'ai_generated' => false,
        ]);

        if ($post->status === SocialPostStatus::Scheduled) {
            PublishSocialPostJob::dispatch($post)->delay($post->scheduled_at);
        }

        return back()->with('success', 'Post created.');
    }

    public function destroy(SocialPost $post): RedirectResponse
    {
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }
}
