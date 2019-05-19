@component('mail::message')
# Hello, {{ $userInfo->full_name }}


{{ __('Welcome to :companyName. Please use the following credentials to log in your account on our platform :', ['companyName' => company_name()]) }}

<ul style="list-style: none">
<li>{{ __('Email') }} : {{ $userInfo->user->email }}</li>
<li>{{ __('Username') }} : {{ $userInfo->user->username }}</li>
<li>{{ __('Password') }} : {{ $userInfo->user->created_by_admin }}</li>
</ul>

{{ __('The password has been generated automatically. We are recommending to change your password after login your account.') }}

{{ __('Click the following link to verify your account.') }}


@component('mail::button', ['url' => url()->temporarySignedRoute('account.verification',now()->addMinutes(30),['user_id'=>$userInfo->user_id])])
{{ __('Verify') }}
@endcomponent

{{ __('Thanks a lot for being with us,') }}<br>
{{ config('app.name') }}
@endcomponent