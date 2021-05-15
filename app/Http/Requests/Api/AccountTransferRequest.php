<?php

namespace App\Http\Requests\Api;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AccountTransferRequest extends FormRequest
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
            "description"               => 'required|min:3',
            "date"                      => 'required|date_format:Y-m-d',
            "value"                     => 'required|numeric|gt:0',
            'source_account_id'         => 'required',
            'destination_account_id'    => 'required',
            'source_category_id' => [
                'required',
                Rule::exists(Category::class, 'id')->where(function($query) {
                    $query->where('id', $this->source_category_id)
                        ->where('user_id', auth()->user()->id)
                        ->where('type', Category::EXPENSE);
                })
            ],            
            'destination_category_id' => [
                'required',
                Rule::exists(Category::class, 'id')->where(function($query) {
                    $query->where('id', $this->destination_category_id)
                        ->where('user_id', auth()->user()->id)
                        ->where('type', Category::INCOME);
                })
            ]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
