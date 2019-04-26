<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterComponent extends FormRequest
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
            'inner_name' => 'required|max:45',
            'inner_desc' => 'required|max:45',
            'name' => 'required|max:45',
            'desc' => 'max:45',
            'app_id' => 'required|max:32|unique:component',
            'app_secret' => 'required|max:32',
            'verify_token' => 'required|max:45',
            'aes_key' => 'required|max:43',
            'validate.filename' => 'required',
            'validate.content' => 'required',
        ];
    }

    public function a()
    {

    }
}
