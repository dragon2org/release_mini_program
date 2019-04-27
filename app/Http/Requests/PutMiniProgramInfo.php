<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PutMiniProgramInfo extends FormRequest
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
            'nick_name'  => 'required|max:45',
            'head_img'   => 'required|max:255',
            'principal_name' => 'required|max:45',
            'qrcode_url' => 'required|max:255',
            'desc' => 'required|max:255'
        ];
    }
}
