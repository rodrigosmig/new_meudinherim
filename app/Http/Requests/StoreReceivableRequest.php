<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReceivableRequest extends FormRequest
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
            'due_date'      => 'required|date_format:Y-m-d',
            'description'   => 'required|min:3',
            'value'         => 'required|numeric',
            'monthly'       => 'nullable',
            'installment'           => 'nullable',
            'installments_number'   => 'nullable|numeric',
            'installment_value'     => 'nullable|numeric',
            'category_id' => [
                'required',
                Rule::exists(Category::class, 'id')->where(function($query) {
                    $query->where('id', $this->category_id)
                        ->where('user_id', auth()->user()->id)
                        ->where('type', Category::INCOME);
                })
            ],
        ];
    }
}
