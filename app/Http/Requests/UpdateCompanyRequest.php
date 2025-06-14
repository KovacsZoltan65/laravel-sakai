<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['request','string', 'max:255'],
            'email' => ['request','string', 'max:255', 
                Rule::unique('companies', 'name')->ignore($this->company->id),
            ],
            'address' => ['request','string', 'max:255'],
            'phone' => ['request','string', 'max:255'],
        ];
    }
}
