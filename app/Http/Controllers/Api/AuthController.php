<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AccountService;
use App\Services\ProfileService;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\UserStoreRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_NOT_FOUND);
        }

        $token = $user->createToken($credentials['device'])->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([], 204);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(UserStoreRequest $request)
    {
        $userService        = app(ProfileService::class);
        $categoryService    = app(CategoryService::class);

        $data = $request->validated();

        $user = $userService->createUser($data);

        auth()->login($user);

        $categoryService->createDefaultCategories();
        
        Account::create([
            'name'      => __('global.money'),
            'type'      => Account::MONEY,
        ]);

        return new UserResource($user);
    }
}
