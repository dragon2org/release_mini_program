<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComponetConfigSync extends FormRequest
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
            'category' => ['required', 'string', function($attribute, $value, $closure){
                if(!in_array($value, [
                    'domain', 'web_view_domain', 'tester', 'visit_status', 'support_version', 'all'
                ])){
                    $closure($attribute . ' invalid');
                }
            }]
        ];
    }
}
