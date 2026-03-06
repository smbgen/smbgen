<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdministrator();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert content_blocks from JSON string to array if needed
        if ($this->has('content_blocks') && is_string($this->content_blocks)) {
            $this->merge([
                'content_blocks' => json_decode($this->content_blocks, true) ?? [],
            ]);
        }

        // If slug is empty, generate from title
        if (! $this->filled('slug') && $this->filled('title')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the post ID from the route parameter (named 'post' by resource route)
        $post = $this->route('post');
        $postId = $post instanceof \App\Models\BlogPost ? $post->id : $post;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($postId)],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'content_blocks' => ['nullable', 'array'],
            'content_blocks.*.type' => ['required_with:content_blocks', 'string'],
            'content_blocks.*.content' => ['nullable'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'string', 'in:draft,scheduled,published,archived'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:500'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:blog_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:blog_tags,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The blog post title is required.',
            'slug.unique' => 'This slug is already in use. Please choose a different one.',
            'status.required' => 'Please select a post status.',
            'status.in' => 'Invalid status selected.',
            'content_blocks.*.type.required_with' => 'Each content block must have a type.',
            'content_blocks.*.type.in' => 'Invalid content block type.',
            'categories.*.exists' => 'One or more selected categories do not exist.',
            'tags.*.exists' => 'One or more selected tags do not exist.',
        ];
    }
}
