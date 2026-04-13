<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $tenantId = $this->route('tenant');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subdomain' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('tenants', 'subdomain')->ignore($tenantId),
            ],
            'custom_domain' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenants', 'custom_domain')->ignore($tenantId),
            ],
            'plan' => ['required', 'string', 'in:trial,starter,professional,enterprise'],
            'deployment_mode' => ['required', 'string', 'in:shared,dedicated'],
            'trial_ends_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
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
