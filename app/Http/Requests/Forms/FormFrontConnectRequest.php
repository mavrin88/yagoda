<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class FormFrontConnectRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'firm'  => 'required',
            'inn'   => 'required',
            'name'  => 'required',
            'phone' => 'required',
            'city'  => 'required',
            'crm'   => 'required',
        ];
    }
}
