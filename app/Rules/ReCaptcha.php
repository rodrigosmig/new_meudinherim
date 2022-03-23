<?php

namespace App\Rules;

use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\ImplicitRule;

class ReCaptcha implements ImplicitRule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = new Client();

        $response = $client->post(config('auth.google_recaptcha_url'),
            ['form_params'=>
                [
                    'secret'    => config('auth.google_recaptcha_secret'),
                    'response'  => $value
                 ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        return $body->success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('messages.recaptcha.invalid_token');
    }
}
