<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'super_admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subdomain' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', 'unique:tenants,subdomain'],
            'custom_domain' => ['nullable', 'string', 'max:255', 'unique:tenants,custom_domain'],
            'plan' => ['required', 'string', 'in:trial,basic,pro,enterprise'],
            'trial_ends_at' => ['nullable', 'date', 'after:today'],
            'is_active' => ['nullable', 'boolean'],
            
            // Admin user fields
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'subdomain.regex' => 'The subdomain may only contain lowercase letters, numbers, and hyphens.',
            'subdomain.unique' => 'This subdomain is already taken.',
            'custom_domain.unique' => 'This custom domain is already in use.',
            'plan.in' => 'The selected plan is invalid.',
            'trial_ends_at.after' => 'Trial end date must be in the future.',
            'admin_email.unique' => 'This email address is already registered.',
        ];
    }
}
