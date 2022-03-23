<?php

namespace App\Http\Requests\Api;

use App\Rules\ReCaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserStoreRequest extends FormRequest
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
            'name'                  => 'bail|required|string|max:2048|min:3',
            'email'                 => 'bail|required|email|max:255|unique:users',
            'password'              => 'bail|required|min:8|confirmed',
            'enable_notification'   => 'nullable',
            'reCaptchaToken'        => ['bail', 'required', new ReCaptcha]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
