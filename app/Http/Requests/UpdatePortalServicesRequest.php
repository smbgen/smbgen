<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortalServicesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_tier' => ['required', 'string', 'in:free,free_consulting,smb_core,smb_pro,smb_max'],
            'enabled_services' => ['nullable', 'array'],
            'enabled_services.*' => ['string', 'in:free_open_source,consulting,smb_core,smb_pro,smb_max'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'account_tier.in' => 'Please select a valid smbgen account tier.',
            'enabled_services.*.in' => 'One or more selected services are invalid.',
        ];
    }
}
