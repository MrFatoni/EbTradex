<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\Google2faRequest;
use App\Repositories\User\Interfaces\UserInterface;
use App\Services\User\ProfileService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2faController extends Controller
{
    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-23 12:02 AM
     * @description:
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = app(ProfileService::class)->profile();
        $data['title'] = __('Google Two Factor Authentication');

        if (empty(Auth::user()->google2fa_secret)) {
            $google2fa = new Google2FA();
            $data['secretKey'] = $google2fa->generateSecretKey(16);
            $data['inlineUrl'] = $google2fa->getQRCodeInline(company_name(), Auth::user()->email, $data['secretKey']);
        }

        return view('backend.google2fa.create', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-23 12:02 AM
     * @description:
     * @param Google2faRequest $request
     * @param $googleCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Google2faRequest $request, $googleCode)
    {
        $google2fa = new Google2FA();

        try {
            if($google2fa->verifyKey($googleCode, $request->google_app_code))
            {
                if(app(UserInterface::class)->update(['google2fa_secret' => $googleCode], Auth::id())) {

                    $authenticator = app(Authenticator::class)->boot($request);
                    $authenticator->login();

                    return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('Google Authentication has been enabled successfully.'));
                }
            }

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to enable google authentication.'));
        }
        catch (\Exception $exception){
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to enable google authentication.'));
        }

    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-23 12:02 AM
     * @description:
     * @param Google2faRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Google2faRequest $request)
    {
        $google2fa = new Google2FA();

        try {
            if($google2fa->verifyKey(Auth::user()->google2fa_secret, $request->google_app_code))
            {
                $authenticator = app(Authenticator::class)->boot($request);
                $authenticator->login();

                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __("The One Time Password was correct."));
            }

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to verify google authentication.'));
        }
        catch (\Exception $exception){
            dd($exception->getMessage());
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to verify google authentication.'));
        }
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-23 12:03 AM
     * @description:
     * @param Google2faRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Google2faRequest $request)
    {
        $google2fa = new Google2FA();

        try {
            if($google2fa->verifyKey(Auth::user()->google2fa_secret, $request->google_app_code))
            {
                if(app(UserInterface::class)->update(['google2fa_secret' => null], Auth::id())) {
                    return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('Google Authentication has been disabled successfully.'));
                }
            }

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to disabled google authentication.'));
        }
        catch (\Exception $exception){
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to disabled google authentication.'));
        }
    }
}
