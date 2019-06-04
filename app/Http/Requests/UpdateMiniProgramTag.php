<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMiniProgramTag extends FormRequest
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
            'tag' => ['required', 'string', function($attribute, $value, $closure){
                if(preg_match("/^[a-zA-Z]+[a-zA-Z0-9\-\_]{1,45}$/", $value) === 0){
                    $closure($attribute . ' invalid');
                }
            }]
        ];
    }
}
