<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'current_password'  => 'required',
            'password'          => 'required|confirmed|min:8',
            
        ];
    }

    /**
 * Configure the validator instance.
 *
 * @param  \Illuminate\Validation\Validator  $validator
 * @return void
 */
public function withValidator($validator)
{
    $validator->after(function ($validator) {
        if ( !Hash::check($this->current_password, $this->user()->password) ) {
            $validator->errors()->add('current_password', __('messages.profile.incorrect_password'));
        }

        if ( $this->current_password === $this->password) {
            $validator->errors()->add('current_password', __('messages.profile.same_password'));
        }
    });
    return;
 }
}
