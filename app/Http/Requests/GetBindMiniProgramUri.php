<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetBindMiniProgramUri extends FormRequest
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
            'inner_desc' => 'max:45',
            'company_id' => 'required|int',
            'type' => 'required|in:pc,mobile',
        ];
    }
}
