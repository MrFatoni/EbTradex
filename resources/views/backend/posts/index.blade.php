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
                            <th>{{ __('Analyst') }}</th>
                            <th>{{ __('Created Date') }}</th>
                            <th class="text-center">{{ __('Publish Status') }}</th>
                            <th class="text-center all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $post)
                            <tr>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->full_name }}</td>
                                <td>{{ $post->created_at }}</td>
                                <td class="text-center">{{ $post->is_published ? __('Yes') : __('No') }}</td>

                                <td class="cm-action">
                                    <div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle"
                                                data-toggle="dropdown">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            @if($post->is_published == ACTIVE_STATUS_ACTIVE)
                                                <li>
                                                    <a target="_blank" href="{{ route('trading-views.show', $post->id) }}"><i
                                                                class="fa fa-eye"></i> {{ __('Show') }}</a>
                                                </li>
                                            @endif
                                            @if(has_permission('trade-analyst.posts.edit'))
                                                <li>
                                                    <a href="{{ route('trade-analyst.posts.edit', $post->id) }}"><i
                                                                class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                                </li>
                                            @endif

                                            @if(has_permission('trade-analyst.posts.toggle-status'))
                                                <li>
                                                    <a data-form-id="update-{{ $post->id }}" data-form-method="PUT"
                                                       href="{{ route('trade-analyst.posts.toggle-status', $post->id) }}" class="confirmation"
                                                       data-alert="{{__('Do you want to change the publish status?')}}"><i
                                                                class="fa fa-edit"></i> {{ __('Change Status') }}</a>
                                                </li>
                                            @endif

                                            @if(has_permission('trade-analyst.posts.destroy'))
                                                <li>
                                                    <a data-form-id="delete-{{ $post->id }}" data-form-method="DELETE"
                                                       href="{{ route('trade-analyst.posts.destroy', $post->id) }}" class="confirmation"
                                                       data-alert="{{__('Do you want to delete this trade analysis?')}}"><i
                                                                class="fa fa-trash-o"></i> {{ __('Delete') }}</a>
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