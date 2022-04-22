<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ProfileService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ResendVerificationEmailRequest;

class VerificationController extends Controller
{
    protected $profileService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProfileService $profileService) {
        $this->middleware('throttle:6,1')->only('verify', 'resend');

        $this->profileService = $profileService;
    }
    
    public function verify($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(["message" =>  __('messages.emails.invalid_token')], Response::HTTP_FORBIDDEN);
        }

        $user = $this->profileService->findById($user_id);

        if (! $user) {
            return response()->json(['message' => __('messages.emails.user_not_found')], Response::HTTP_NOT_FOUND);
        }

        if($user->hasVerifiedEmail()) {
            return response()->json(['message' => __('messages.emails.already_verified')], Response::HTTP_BAD_REQUEST);
        }

        $user->markEmailAsVerified();
        
        return response()->json(['message' => __('messages.emails.verify_success')], Response::HTTP_OK);
    }

    public function resend(ResendVerificationEmailRequest $request) 
    {
        $user = $this->profileService->findByEmail($request->email);

        if (! $user) {
            return response()->json(['message' => __('messages.emails.user_not_found')], Response::HTTP_NOT_FOUND);
        }

        if($user->hasVerifiedEmail()) {
            return response()->json(['message' => __('messages.emails.already_verified')], Response::HTTP_BAD_REQUEST);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => __('messages.emails.resend_success')], Response::HTTP_OK);
    }
}
