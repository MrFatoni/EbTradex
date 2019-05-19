<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\PasswordResetRequest;
use App\Services\Core\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $response = app(VerificationService::class)->verifyUserEmail($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;
        $route = Auth::check() ? REDIRECT_ROUTE_TO_USER_AFTER_LOGIN : REDIRECT_ROUTE_TO_LOGIN;

        return redirect()->route($route)->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    public function resendForm()
    {
        return view('backend.email_verify');
    }

    public function send(PasswordResetRequest $request)
    {
        $response = app(VerificationService::class)->sendVerificationLink($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}