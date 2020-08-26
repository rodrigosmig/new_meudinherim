<?php

namespace App\Http\Requests;

use App\Models\Card;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceEntryRequest extends FormRequest
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
            'card_id' => [
                'required',
                Rule::exists(Card::class, 'id')->where(function($query) {
                    $query->where('id', $this->card_id)
                        ->where('user_id', auth()->user()->id);
                })
            ],
            'category_id' => [
                'required',
                Rule::exists(Category::class, 'id')->where(function($query) {
                    $query->where('id', $this->category_id)
                        ->where('user_id', auth()->user()->id);
                })
            ],
            'date'                  => 'required|date_format:Y-m-d',
            'description'           => 'required|min:3',
            'value'                 => 'required|numeric',
            'installment'           => 'nullable',
            'installments_number'   => 'nullable|numeric',
            'installment_value'     => 'nullable|numeric'
        ];
    }
}
