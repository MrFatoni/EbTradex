<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\PasswordUpdateRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\UserSettingRequest;
use App\Http\Requests\User\UserAvatarRequest;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Interfaces\UserSettingInterface;
use App\Services\User\ProfileService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    private $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->profile();
        $data['title'] = __('Profile');

        return view('backend.profile.index', $data);
    }

    public function edit()
    {
        $data = $this->service->profile();
        $data['title'] = __('Edit Profile');

        return view('backend.profile.edit', $data);
    }

    public function update(UserRequest $request, UserInfoInterface $userInfo)
    {
        $parameters = $request->only(['first_name', 'last_name', 'address']);

        if ($userInfo->update($parameters, Auth::id(), 'user_id')) {
            return redirect()->route('profile.edit')->with(SERVICE_RESPONSE_SUCCESS, __('Profile has been updated successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to update profile.'));
    }

    public function changePassword()
    {
        $data = $this->service->profile();
        $data['title'] = __('Change Password');

        return view('backend.profile.change_password', $data);
    }

    public function updatePassword(PasswordUpdateRequest $request)
    {
        $response = $this->service->updatePassword($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    public function setting()
    {
        $data = $this->service->profile();
        $data['title'] = __('Setting');

        return view('backend.profile.setting', $data);
    }

    public function settingEdit()
    {
        $data = $this->service->profile();
        $data['title'] = __('Edit Setting');

        return view('backend.profile.setting_edit_form', $data);
    }

    public function settingUpdate(UserSettingRequest $request, UserSettingInterface $userSetting)
    {
        $parameters = [
            'language' => $request->get('language', config('app.locale')),
            'timezone' => $request->get('timezone', config('app.timezone')),
        ];

        if ($userSetting->update($parameters, Auth::id(), 'user_id')) {
            return redirect()->route('profile.setting.edit')->with(SERVICE_RESPONSE_SUCCESS, __('User setting has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_SUCCESS, __('User setting has been updated successfully.'));
    }

    public function avatarEdit()
    {
        $data = $this->service->profile();
        $data['title'] = __('Change Avatar');

        return view('backend.profile.avatar_edit_form', $data);
    }

    public function avatarUpdate(UserAvatarRequest $request)
    {
        $response = $this->service->avatarUpload($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    public function referral()
    {
        $data['title'] = __('Referral');
        $data['user'] = Auth::user();
        return view('backend.profile.referral', $data);
    }

    public function generateReferralLink()
    {
        $user = Auth::user();
        if (empty($user->referral_code)) {
            $attributes = ['referral_code' => $user->id . random_string(8)];
            app(UserInterface::class)->update($attributes, $user->id);
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('Referral link has been generated successfully.'));
        }

        return redirect()->back();
    }
}