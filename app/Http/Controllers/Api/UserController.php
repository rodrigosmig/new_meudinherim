<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ProfileService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Requests\Api\UserUpdateAvatarRequest;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /* public function store(UserStoreRequest $request)
    {
        $data = $request->validated();

        $user = $this->service->createUser($data);

        return response()->json($user, Response::HTTP_CREATED);
    } */

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()->json($this->service->getApiUser());
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

        return response()->json($this->service->getApiUser());
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
}
