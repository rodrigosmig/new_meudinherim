<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ProfileService;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\UserStoreRequest;

class AuthController extends Controller
{
    protected $profileService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProfileService $profileService){
        $this->profileService = $profileService;
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        
        if (!$this->profileService->validateRecaptcha($data['reCaptchaToken'])) {
            return response()->json(['error' => __('messages.recaptcha.invalid_token')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $credentials = request(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid Credentials'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->profileService->findByEmail($credentials['email']);

        $token = $user->createToken($data['device'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user)
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(UserStoreRequest $request)
    {
        $categoryService = app(CategoryService::class);

        $data = $request->validated();

        if (!$this->profileService->validateRecaptcha($data['reCaptchaToken'])) {
            return response()->json(['error' => __('messages.recaptcha.invalid_token')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->profileService->createUser($data);

        $user->sendEmailVerificationNotification();

        auth()->login($user);

        $categoryService->createDefaultCategories();

        $this->profileService->createDefaultUserAccount();

        auth()->logout();

        return response()->json(new UserResource($user), Response::HTTP_CREATED);
    }

    public function profile()
    {
        return new UserResource(auth()->user());
    }
}
