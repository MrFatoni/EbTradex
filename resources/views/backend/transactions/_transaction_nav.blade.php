@php($parameters = ['journalType' => null])

@if(isset($userId))
    @php($parameters['id'] = $userId)
@endif

<div class="row dc-clear">
    <div class="col-md-3 col-sm-6 cm-mb-5">
        <a class="btn btn-block btn-default {{ is_current_route($routeName, 'active', ['journalType' => null]) }}" href="{{ route($routeName, $parameters) }}">{{ __('All') }}</a>
    </div>

    @foreach(config('commonconfig.journal_type') as $key => $value)
        @php($parameters['journalType'] = $key)
        <div class="col-md-3 col-sm-6 cm-mb-5">
            <a data-title="{{ title_case(str_replace('-',' ', $key)) }}" data-toggle="tooltip" class="btn btn-block btn-default {{ is_current_route($routeName, 'active', ['journalType' => $key]) }}" href="{{ route($routeName, $parameters) }}" style="text-overflow: ellipsis; overflow: hidden">{{ title_case(str_replace('-',' ', $key)) }}</a>
        </div>
    @endforeach
</div>