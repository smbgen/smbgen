<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdministrator();
    }

    protected function prepareForValidation(): void
    {
        // Parse scheduled_at from a datetime-local input (HTML5 returns no seconds)
        if ($this->has('scheduled_at') && $this->scheduled_at) {
            $this->merge(['scheduled_at' => $this->scheduled_at]);
        }
    }

    public function rules(): array
    {
        return [
            'caption' => ['required', 'string', 'max:63206'],
            'account_ids' => ['required', 'array', 'min:1'],
            'account_ids.*' => ['integer', 'exists:social_accounts,id'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'requires_approval' => ['nullable', 'boolean'],
            'source_type' => ['nullable', 'string'],
            'source_id' => ['nullable', 'integer'],

            // Optional media references (CmsImage IDs)
            'cms_image_ids' => ['nullable', 'array'],
            'cms_image_ids.*' => ['integer', 'exists:cms_images,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'caption.required' => 'A caption is required.',
            'account_ids.required' => 'Please select at least one connected account.',
            'account_ids.min' => 'Please select at least one connected account.',
            'scheduled_at.after' => 'The scheduled time must be in the future.',
        ];
    }
}
