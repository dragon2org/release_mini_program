<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RetryRelease extends FormRequest
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
            'release_item_id' => 'required|integer',
            'config' => ['array', function($attribute, $value, $closure){
                if(!is_array($value)){
                    $closure($attribute . ' invalid');
                }
            }]
        ];
    }
}
