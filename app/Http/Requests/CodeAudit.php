<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CodeAudit extends FormRequest
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
            'item_list' => 'required|array',
            'item_list.*.address' => 'required|string',
            'item_list.*.tag' => 'required|string',
            'item_list.*.first_class' => 'required|string',
            'item_list.*.second_class' => 'required|string',
            'item_list.*.first_id' => 'required|integer',
            'item_list.*.second_id' => 'required|integer',
            'item_list.*.title' => 'required|string',
        ];
    }
}
