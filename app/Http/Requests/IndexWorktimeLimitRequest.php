<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexWorktimeLimitRequest extends FormRequest
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
                'in:name,company_id,start_date,end_date',
            ],
            'order' => ['in:asc,desc'],
            'perPage' => ['numeric'],
        ];
    }
}
