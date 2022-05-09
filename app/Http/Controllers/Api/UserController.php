<?php

namespace App\Http\Controllers\Api;

use App\Services\ProfileService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Requests\Api\UserUpdateAvatarRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    protected $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return new UserResource($this->service->getApiUser());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request)
    {
        $data = $request->validated();

        $this->service->updateProfile($data);

        $user = $this->service->getApiUser();

        /* if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        } */

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(UserUpdateAvatarRequest $request)
    {
        $data = $request->validated();

        $this->service->updateAvatar($data);

        return response()->json([
            'message' => __('messages.profile.avatar_updated'),
            'avatar'  => url("storage/" . auth()->user()->avatar)
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();

        $this->service->updatePassword($data);

        return response()->json([
            'message' => __('messages.profile.password_updated'),
        ]);
    }
}
