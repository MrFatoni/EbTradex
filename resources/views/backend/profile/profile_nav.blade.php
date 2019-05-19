<ul class="nav nav-tabs">
    <li class="{{ is_current_route(['profile.index','profile.edit']) }}"><a
                href="{{ route('profile.index') }}">{{ __('Profile') }}</a></li>
    <li class="{{ is_current_route('profile.change-password') }}"><a
                href="{{ route('profile.change-password') }}">{{ __('Change Password') }}</a></li>
    <li class="{{ is_current_route('profile.avatar.edit') }}"><a
                href="{{ route('profile.avatar.edit') }}">{{ __('Change Avatar') }}</a></li>
    <li class="{{ is_current_route('trader.upload-id.index') }}"><a
                href="{{ route('trader.upload-id.index') }}">{{ __('Upload ID') }}</a></li>
    <li class="{{ is_current_route('profile.google-2fa.create') }}"><a
                href="{{ route('profile.google-2fa.create') }}">{{ __('Google Authentication') }}</a></li>
    @if(admin_settings('referral'))
        <li class="{{ is_current_route('profile.referral') }}"><a
                    href="{{ route('profile.referral') }}">{{ __('Referral Link') }}</a></li>
    @endif
</ul>