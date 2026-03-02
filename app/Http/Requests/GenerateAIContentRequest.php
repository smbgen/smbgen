<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAIContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdministrator();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'min:10', 'max:5000'],
            'content_type' => ['required', 'string', 'in:blog_post,landing_page,home_page,brand_positioning,seo_metadata,content_improvement,industry_variant'],
            'existing_content' => ['nullable', 'string', 'max:50000'],
            'industry' => ['required_if:content_type,industry_variant', 'string', 'max:255'],
            'custom_system_prompt' => ['nullable', 'string', 'max:2000'],
            'max_tokens' => ['nullable', 'integer', 'min:100', 'max:8000'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'prompt.required' => 'Please provide a prompt describing what content you want to generate.',
            'prompt.min' => 'Your prompt is too short. Please provide more details (at least 10 characters).',
            'prompt.max' => 'Your prompt is too long. Please keep it under 5000 characters.',
            'content_type.required' => 'Please specify the type of content to generate.',
            'content_type.in' => 'Invalid content type. Must be one of: blog_post, landing_page, home_page, brand_positioning, seo_metadata, content_improvement, industry_variant.',
            'industry.required_if' => 'Please specify the target industry when generating industry variants.',
            'max_tokens.min' => 'Maximum tokens must be at least 100.',
            'max_tokens.max' => 'Maximum tokens cannot exceed 8000.',
            'temperature.min' => 'Temperature must be between 0 and 1.',
            'temperature.max' => 'Temperature must be between 0 and 1.',
        ];
    }
}
