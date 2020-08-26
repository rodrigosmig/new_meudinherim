<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateCardRequest extends FormRequest
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
            'name'          => 'required|min:3|max:191',
            'pay_day'       => 'required|numeric|min:1|max:31',
            'closing_day'   => 'required|numeric|min:1|max:31',
            'credit_limit'  => 'required|numeric|'
        ];
    }
}
