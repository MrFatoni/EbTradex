<ul class="nav nav-tabs">
    <li class="{{ is_current_route($routeName, 'active', ['categoryType' => null]) }}">
        <a href="{{ route($routeName, ['categoryType' => null]) }}">{{ __('All') }}</a>
    </li>

    @foreach(config('commonconfig.category_slug') as $key => $value)
        <li class="{{ is_current_route($routeName, 'active', ['categoryType' => $key]) }}">
            <a href="{{ route($routeName, ['categoryType' => $key]) }}">{{ category_type($value) }}</a>
        </li>
    @endforeach
</ul>