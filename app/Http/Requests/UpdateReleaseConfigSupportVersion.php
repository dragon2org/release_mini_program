<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReleaseConfigSupportVersion extends FormRequest
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
            'support_version' => ['required', 'string', function($attribute, $value, $closure){
                $minVersion = '1.0.0';
                $maxVersion = '2.7.1';
                if( $value < $minVersion){
                    $closure($attribute . ' invalid');
                }
            }],
        ];
    }
}
