<?php

namespace App\Livewire\Admin;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class SocialPostEditor extends Component
{
    use WithFileUploads;

    const MAX_CHARACTERS = 3000;

    // Form fields
    public ?int $postId = null;
    public int $socialAccountId;
    public string $title = '';
    public string $content = '';
    public string $status = 'draft';
    public string $scheduleDate = '';
    public string $scheduleTime = '';
    public array $existingMediaPaths = [];
    public $mediaFiles = [];

    // UI state
    public bool $previewMode = false;
    public bool $saved = false;
    public string $savedMessage = '';
    public string $errorMessage = '';

    public array $accounts = [];

    public function mount(?SocialPost $post = null, $accounts = [])
    {
        $this->accounts = $accounts instanceof \Illuminate\Support\Collection
            ? $accounts->toArray()
            : $accounts;

        if ($post && $post->exists) {
            $this->postId = $post->id;
            $this->socialAccountId = $post->social_account_id;
            $this->title = $post->title ?? '';
            $this->content = $post->content ?? '';
            $this->status = $post->status;
            $this->existingMediaPaths = $post->media_paths ?? [];

            if ($post->scheduled_at) {
                $this->scheduleDate = $post->scheduled_at->format('Y-m-d');
                $this->scheduleTime = $post->scheduled_at->format('H:i');
            }
        } elseif (! empty($this->accounts)) {
            $this->socialAccountId = $this->accounts[0]['id'];
        }
    }

    #[Computed]
    public function characterCount(): int
    {
        return mb_strlen($this->content);
    }

    #[Computed]
    public function charactersRemaining(): int
    {
        return self::MAX_CHARACTERS - $this->characterCount;
    }

    #[Computed]
    public function isOverLimit(): bool
    {
        return $this->characterCount > self::MAX_CHARACTERS;
    }

    public function updatedContent(): void
    {
        $this->saved = false;
    }

    public function togglePreview(): void
    {
        $this->previewMode = ! $this->previewMode;
    }

    public function saveDraft(): void
    {
        $this->status = 'draft';
        $this->save();
    }

    public function schedulePost(): void
    {
        $this->validate([
            'scheduleDate' => 'required|date',
            'scheduleTime' => 'required',
        ], [
            'scheduleDate.required' => 'Please select a schedule date.',
            'scheduleTime.required' => 'Please select a schedule time.',
        ]);

        $scheduledAt = \Carbon\Carbon::parse($this->scheduleDate . ' ' . $this->scheduleTime);

        if ($scheduledAt->isPast()) {
            $this->errorMessage = 'Scheduled time must be in the future.';
            return;
        }

        $this->status = 'scheduled';
        $this->save($scheduledAt);
    }

    private function save(?\Carbon\Carbon $scheduledAt = null): void
    {
        $this->errorMessage = '';

        $this->validate([
            'socialAccountId' => 'required|integer',
            'content' => 'required|string|max:' . self::MAX_CHARACTERS,
        ], [
            'content.required' => 'Post content cannot be empty.',
            'content.max' => 'Post content cannot exceed ' . self::MAX_CHARACTERS . ' characters.',
        ]);

        // Upload any new media files
        $mediaPaths = $this->existingMediaPaths;
        foreach ($this->mediaFiles as $file) {
            $path = $file->store('social-media', 'public');
            $mediaPaths[] = $path;
        }

        $data = [
            'social_account_id' => $this->socialAccountId,
            'user_id' => auth()->id(),
            'title' => $this->title ?: null,
            'content' => $this->content,
            'media_paths' => $mediaPaths ?: null,
            'status' => $this->status,
            'scheduled_at' => $scheduledAt,
        ];

        if ($this->postId) {
            $post = SocialPost::findOrFail($this->postId);
            $post->update($data);
        } else {
            $post = SocialPost::create($data);
            $this->postId = $post->id;
        }

        $this->mediaFiles = [];
        $this->existingMediaPaths = $post->fresh()->media_paths ?? [];
        $this->saved = true;
        $this->savedMessage = $this->status === 'scheduled'
            ? 'Post scheduled for ' . ($scheduledAt?->format('M j, Y g:i A') ?? '') . '.'
            : 'Draft saved.';

        $this->dispatch('post-saved', postId: $post->id, status: $this->status);
    }

    public function removeMedia(string $path): void
    {
        $this->existingMediaPaths = array_values(
            array_filter($this->existingMediaPaths, fn ($p) => $p !== $path)
        );
        $this->saved = false;
    }

    public function render()
    {
        return view('livewire.admin.social-post-editor');
    }
}
