<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Functions;

class BaseRequest extends FormRequest
{
    use Functions;

    public $validationRules = [];

    public function __construct()
    {
        //$this->validationRules = json_decode(file_get_contents(resource_path('js/Validation/ValidationRules.json')), true);
        $this->validationRules = $this->getValidationRules();
    }
}