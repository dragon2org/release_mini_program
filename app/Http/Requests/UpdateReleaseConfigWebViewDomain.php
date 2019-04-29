<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReleaseConfigWebViewDomain extends FormRequest
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
            'action' => ['required', function($attribute, $value, $closure){
                if($value !== 'set') $closure('action参数仅支持: set');
            }],
            'webviewdomain' => 'required|array',
            'webviewdomain.*' => ['url', function($attribute, $value, $closure){
                if(strpos($value, 'https://') !== 0){
                    $closure($attribute . ' invalid');
                }
            }],
        ];
    }
}
