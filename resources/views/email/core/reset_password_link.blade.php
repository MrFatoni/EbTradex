@component('mail::message')
# Hello, {{ $user->userInfo->full_name }}

{{ __("Please click on the following link to reset password -" ) }}

@component('mail::button', ['url' => url()->temporarySignedRoute('reset-password.index', now()->addMinutes(30), ['id' => $user->id])])
{{ __('Reset Password') }}
@endcomponent

{{ __('Thanks a lot for being with us,') }}<br>
{{ config('app.name') }}
@endcomponent
