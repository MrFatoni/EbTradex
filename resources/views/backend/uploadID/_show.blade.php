<h4 class="text-center text-uppercase">{{ __('ID Type : :idType', ['idType' => id_type($user->userInfo->id_type)]) }}</h4>
<p class="text-center text-uppercase"><span class="label label-{{ config('commonconfig.id_status.' . $user->userInfo->is_id_verified . '.color_class') }}">{{ id_status($user->userInfo->is_id_verified) }}</span></p>

@if($user->userInfo->is_id_verified == ID_STATUS_PENDING)
<p class="text-center help-block margin-bottom">{{ __('Your ID verification request is being reviewed. It will take maximum 3 business day to approve / decline your request.') }}</p>
@endif

@include('backend.idManagement._show', ['user' => $user->userInfo])
