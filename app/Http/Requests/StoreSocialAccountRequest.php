<?php

namespace App\Http\Requests;

use App\Models\SocialAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSocialAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdministrator();
    }

    public function rules(): array
    {
        return [
            'platform' => [
                'required',
                'string',
                Rule::in([
                    SocialAccount::PLATFORM_FACEBOOK,
                    SocialAccount::PLATFORM_INSTAGRAM,
                    SocialAccount::PLATFORM_LINKEDIN,
                ]),
            ],
            'account_name' => ['required', 'string', 'max:255'],
            'account_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'platform.required' => 'Please select a social media platform.',
            'platform.in' => 'The selected platform is not supported.',
            'account_name.required' => 'Please enter an account or page name.',
        ];
    }
}
