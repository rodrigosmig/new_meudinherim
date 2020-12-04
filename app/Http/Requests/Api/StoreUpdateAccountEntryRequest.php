<?php

namespace App\Http\Requests\Api;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUpdateAccountEntryRequest extends FormRequest
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
            'category_id' => [
                'required',
                Rule::exists(Category::class, 'id')->where(function($query) {
                    $query->where('id', $this->category_id)
                        ->where('user_id', auth()->user()->id);
                })
            ],
            'date'          => 'required|date_format:Y-m-d',
            'description'   => 'required|min:3',
            'value'         => 'required|numeric',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
