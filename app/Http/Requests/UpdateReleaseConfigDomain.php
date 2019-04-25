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
            'requestdomain.*' => 'url',
            'wsrequestdomain.*' => 'url',
            'uploaddomain.*' => 'url',
            'downloaddomain.*' => 'url',
        ];
    }
}
