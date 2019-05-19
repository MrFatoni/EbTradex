<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\LoginRequest;
use App\Http\Requests\Core\NewPasswordRequest;
use App\Http\Requests\Core\PasswordResetRequest;
use App\Http\Requests\Core\RegisterRequest;
use App\Services\Guest\AuthService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    protected $service;

    /**
     * AuthController constructor.
     * @param AuthService $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function loginForm()
    {
        return view('backend.login');
    }

    /*
     * login user
     */

    public function login(LoginRequest $request)
    {
        $response = $this->service->login($request);

        if ($response[SERVICE_RESPONSE_STATUS]) {
            return redirect()->route(REDIRECT_ROUTE_TO_USER_AFTER_LOGIN)->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE]);
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-08-13 5:12 PM
     * @description:
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $redirectRoute = app('router')->getRoutes()->match(app('request')->create(session('_previous')['url']))->getName();

        if($redirectRoute != 'exchange.index'){
            $redirectRoute = 'login';
        }

        Auth::logout();
        session()->flush();
        return redirect()->route($redirectRoute)->with(SERVICE_RESPONSE_SUCCESS, __('You have been logged out successfully.'));
    }

    public function register()
    {
        return view('backend.register');
    }

    public function storeUser(RegisterRequest $request)
    {
        $parameters = $request->only(['first_name', 'last_name', 'email', 'username', 'password', 'referral_code']);

        if (app(UserService::class)->generate($parameters)) {
            return redirect()->route('login')->with(SERVICE_RESPONSE_SUCCESS, __('Registration successful. Please check your email to verify your account.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Registration failed. Please try after sometime.'));
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-08-13 5:13 PM
     * @description:
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function forgetPassword()
    {
        return view('backend.forget_password');
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-08-13 5:13 PM
     * @description:
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendPasswordResetMail(PasswordResetRequest $request)
    {
        $response = $this->service->sendPasswordResetMail($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-08-13 5:13 PM
     * @description:
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resetPassword(Request $request, $id)
    {
        $data = $this->service->resetPassword($request, $id);

        return view('backend.reset_password', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-08-13 5:13 PM
     * @description:
     * @param NewPasswordRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(NewPasswordRequest $request, $id)
    {
        $response = $this->service->updatePassword($request, $id);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('login')->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
