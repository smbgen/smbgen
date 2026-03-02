<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInspectionReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdministrator();
    }

    public function rules(): array
    {
        return [
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string|max:500',
            'consult_date' => 'nullable|date',
            'summary_title' => 'required|string|max:255',
            'body_explanation' => 'nullable|string',
            'body_suggested_actions' => 'nullable|string',
            'send_email' => 'nullable|boolean',
        ];
    }
}
