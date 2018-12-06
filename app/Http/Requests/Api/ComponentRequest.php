<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ComponentRequest extends FormRequest
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
            'inner_name' => 'required|string',
            'inner_desc' => 'required|string',
            'inner_key' => 'required|string',
            'name' => 'required|string',
            'desc' => 'required|string',
            'app_id' => 'required|string',
            'app_secret' => 'required|string',
            'verify_token' => 'required|string',
            'aes_key' => 'required|string',
            'validate' => 'required|array',
        ];
    }

}
