<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ReceivementRequest extends FormRequest
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
            'paid_date'     => 'required|date_format:Y-m-d',
            'parcelable_id' => 'nullable|numeric',
            'account_id'    => [
                'required',
                Rule::exists(Account::class, 'id')->where(function($query) {
                    $query->where('id', $this->account_id)
                        ->where('user_id', auth()->user()->id);
                })
            ],
        ];
    }
}
