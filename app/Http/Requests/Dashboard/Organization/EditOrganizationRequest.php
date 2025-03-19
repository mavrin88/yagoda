<?php

namespace App\Http\Requests\Dashboard\Organization;

use Illuminate\Foundation\Http\FormRequest;

class EditOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
//    public function authorize(): bool
//    {
//        return false;
//    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organizationId'  => 'required|numeric',
            'status'  => 'nullable|string',
            'comment'  => 'nullable|string',
            'agency_agreement_number'  => 'nullable|numeric',
            'agency_agreement_date'  => 'nullable|string',
        ];
    }
}
