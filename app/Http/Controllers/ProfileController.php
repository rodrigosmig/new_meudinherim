<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProfileService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;

class ProfileController extends Controller
{
    protected $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
        $this->title = __('global.profile');
    }

    public function profile()
    {
        $data = [
            'title' => $this->title,
            'user' => auth()->user()
        ];

        return view('profiles.index', $data);
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        
        $data = $request->validated();
        $this->service->updatePassword($data);

        Alert::success(__('global.success'), __('messages.profile.password_updated'));

        return redirect()->route('profile.index');
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        $this->service->updateProfile($data);

        Alert::success(__('global.success'), __('messages.profile.profile_updated'));

        return redirect()->route('profile.index');
    }
}
