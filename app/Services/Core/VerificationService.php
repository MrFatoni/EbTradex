<?php

namespace App\Services\Core;

use App\Http\Requests\Core\PasswordResetRequest;
use App\Mail\User\Registered;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VerificationService
{
    public function verifyUserEmail(Request $request)
    {
        if (!$request->hasValidSignature() || (Auth::check() && Auth::id() != $request->user_id)) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('Expired verification link.'),
            ];
        }

        $conditions = ['id' => $request->user_id, 'is_email_verified' => EMAIL_VERIFICATION_STATUS_INACTIVE];
        $update = ['is_email_verified' => EMAIL_VERIFICATION_STATUS_ACTIVE];

        if (app(UserInterface::class)->updateByConditions($update, $conditions)) {
            $notification = ['user_id' => $request->user_id, 'data' => __("Your account has been verified successfully.")];
            app(NotificationInterface::class)->create($notification);

            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __('Your account has been verified successfully.'),
            ];
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Invalid verification link or already verified.'),
        ];
    }

    public function sendVerificationLink(PasswordResetRequest $request)
    {
        if (Auth::user()) {
            if (Auth::user()->is_email_verified == EMAIL_VERIFICATION_STATUS_INACTIVE) {
                $user = Auth::user();
            } else {
                $user = false;
            }
        } else {
            $user = app(UserInterface::class)->getFirstByConditions(['email' => $request->email, 'is_email_verified' => EMAIL_VERIFICATION_STATUS_INACTIVE]);
        }

        if (!$user) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('The given email address is already verified.')
            ];
        }

        // send email address.
        $this->_sendEmailVerificationLink($user);

        return [
            SERVICE_RESPONSE_STATUS => true,
            SERVICE_RESPONSE_MESSAGE => __('Email verification link is sent successfully.')
        ];
    }

    public function _sendEmailVerificationLink($user)
    {
        return Mail::to($user->email)->send(new Registered($user->userInfo));
    }
}