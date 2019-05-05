<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadValidateFile extends FormRequest
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
            'validate' => 'required',
            'validate.filename' => ['required', 'string', function($attribute, $value, $closure){
                if(preg_match("/.txt$/", $value) < 1){
                    $closure($attribute . ' invalid');
                }
            }],
            'validate.content' => 'required|string',
        ];
    }
}
