<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReleaseExtJson extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ext_json' => ['required', 'string', function($attribute, $value, $closure){
                $value = json_decode($value, true);
                if(json_last_error() !== JSON_ERROR_NONE){
                    $closure(json_last_error_msg());
                }
            }]
        ];
    }
}
