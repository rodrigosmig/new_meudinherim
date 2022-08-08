<?php

namespace App\Http\Requests\Api;

use App\Models\Account;
use App\Models\Card;
use App\Models\Category;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PartialPaymentInvoiceRequest extends FormRequest
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
            'account_id'    => [
                'required',
                Rule::exists(Account::class, 'id')->where(function($query) {
                    $query->where('id', $this->account_id)
                        ->where('user_id', auth()->user()->id);
                })
            ],
            'card_id'    => [
                'required',
                Rule::exists(Card::class, 'id')->where(function($query) {
                    $query->where('id', $this->card_id)
                        ->where('user_id', auth()->user()->id);
                })
            ],
            'income_category_id' => [
                'required',
                Rule::exists(Category::class, 'id')->where(function($query) {
                    $query->where('id', $this->income_category_id)
                        ->where('user_id', auth()->user()->id)
                        ->where('type', Category::INCOME);
                })
            ],            
            'expense_category_id' => [
                'required',
                Rule::exists(Category::class, 'id')->where(function($query) {
                    $query->where('id', $this->expense_category_id)
                        ->where('user_id', auth()->user()->id)
                        ->where('type', Category::EXPENSE);
                })
            ]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
