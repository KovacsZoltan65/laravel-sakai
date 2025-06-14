<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexEntityRequest extends FormRequest
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
            'field' => [
                'nullable',
                'in:name,email,start_date,end_date,last_export,user_id,company_id,active,created_at,updated_at',
            ],
            'order' => ['nullable','in:asc,desc',],
            'perPage' => ['nullable','integer','min:1',],
            // előkészítés kereső/filter mezőkhöz
            'company_id' => ['nullable','integer','exists:companies,id',],
            'user_id' => ['nullable','integer','exists:users,id',],
            'active' => ['nullable','boolean',],
        ];
    }
}
