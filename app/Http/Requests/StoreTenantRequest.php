<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subdomain' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', 'unique:tenants,subdomain'],
            'custom_domain' => ['nullable', 'string', 'max:255', 'unique:tenants,custom_domain'],
            'plan' => ['required', 'string', 'in:trial,starter,professional,enterprise'],
            'deployment_mode' => ['required', 'string', 'in:shared,dedicated'],
            'trial_ends_at' => ['nullable', 'date', 'after:today'],
            'is_active' => ['nullable', 'boolean'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'subdomain.regex' => 'The subdomain may only contain lowercase letters, numbers, and hyphens.',
            'subdomain.unique' => 'This subdomain is already taken.',
            'custom_domain.unique' => 'This custom domain is already in use.',
            'plan.in' => 'The selected plan is invalid.',
        ];
    }
}
