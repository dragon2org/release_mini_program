<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReleaseConfigDomain extends FormRequest
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
            'requestdomain' => 'required|array',
            'requestdomain.*' => ['url', 'distinct', function($attribute, $value, $closure){
                if(strpos($value, 'https://') !== 0){
                    $closure($attribute . ' invalid');
                }
            }],
            'wsrequestdomain' => 'required|array',
            'wsrequestdomain.*' => ['url', function($attribute, $value, $closure){
                if(strpos($value, 'wss://') !== 0){
                    $closure($attribute . ' invalid');
                }
            }],
            'uploaddomain' => 'required|array',
            'uploaddomain.*' => ['url', function($attribute, $value, $closure){
                if(strpos($value, 'https://') !== 0){
                    $closure($attribute . ' invalid');
                }
            }],
            'downloaddomain' => 'required|array',
            'downloaddomain.*' => ['url', function($attribute, $value, $closure){
                if(strpos($value, 'https://') !== 0){
                    $closure($attribute . ' invalid');
                }
            }],
        ];
    }
}
