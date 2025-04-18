<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexSubdomainRequest extends FormRequest
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
                'in:subdomain,url,name,db_host,db_port,db_name,db_user,db_password,notification,state_id,is_mirror,sso,acs_id,active'
            ],
            'order' => ['in:asc,desc'],
            'perPage' => ['numeric'],
            // előkészítés kereső/filter mezőkhöz
            'name' => ['nullable', 'exists:subdomains,name'],
            'subdomain' => ['nullable', 'exists:subdomains,subdomain'],
            'active' => ['nullable', 'boolean'],
        ];
    }
}
