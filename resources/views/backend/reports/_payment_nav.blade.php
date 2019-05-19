@php $parameters = ['paymentTransactionType' => null] @endphp
@if(isset($walletId) && !empty($walletId))
    @php $parameters['id'] = $walletId @endphp
@endif
<ul class="nav nav-tabs">
    <li class="{{ is_current_route($routeName, 'active', ['paymentTransactionType' => null]) }}">
        <a href="{{ route($routeName, $parameters) }}">{{ __('All') }}</a>
    </li>

    @foreach(config('commonconfig.payment_slug') as $key => $value)
        @php $parameters['paymentTransactionType'] = $key @endphp
        <li class="{{ is_current_route($routeName, 'active', ['paymentTransactionType' => $key]) }}">
            <a href="{{ route($routeName, $parameters) }}">{{ payment_status($value) }}</a>
        </li>
    @endforeach
</ul>