@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    {!! $list['filters'] !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-borderless">
                <div class="box-body">
                    <table class="table datatable dt-responsive display nowrap dc-table" style="width: 100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Title') }}</th>
                            <th class="none">{{ __('Content') }}</th>
                            <th>{{ __('Questioned By') }}</th>
                            <th>{{ __('Created Date') }}</th>
                            <th class="text-center all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $question)
                            <tr>
                                <td>{{ $question->title }}</td>
                                <td>{!!  $question->content !!}</td>
                                <td>{{ $question->full_name }}</td>
                                <td>{{ $question->created_at }}</td>

                                <td class="cm-action">
                                    <div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle"
                                                data-toggle="dropdown">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li>
                                                <a href="{{ route('faq.show', $question->id) }}"><i
                                                            class="fa fa-commenting"></i> {{ __('Show') }}</a>
                                            </li>
                                            @if(has_permission('trade-analyst.questions.answer'))
                                                <li>
                                                    <a href="{{ route('trade-analyst.questions.answer', $question->id) }}"><i
                                                                class="fa fa-commenting"></i> {{ __('Answer') }}</a>
                                                </li>
                                            @endif
                                            @if(has_permission('trade-analyst.questions.destroy'))
                                                <li>
                                                    <a data-form-id="delete-{{ $question->id }}" data-form-method="DELETE" href="{{ route('trade-analyst.questions.destroy', $question->id) }}" class="confirmation" data-alert="{{__('Do you want to delete this question?')}}"><i class="fa fa-trash-o"></i> {{ __('Delete') }}</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {!! $list['pagination'] !!}
@endsection

@section('script')
    <!-- for datatable and date picker -->
    <script src="{{ asset('common/vendors/datepicker/datepicker.js') }}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/plugins/bootstrap/datatables.bootstrap.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/table-datatables-responsive.js')}}"></script>
    <script type="text/javascript">
        //Init jquery Date Picker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom',
            todayHighlight: true,
        });
    </script>
@endsection