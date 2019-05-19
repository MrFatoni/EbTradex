@extends('backend.layouts.main_layout')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-borderless">
                <div class="box-body">
                    <div class="caption font-dark" style="width: 100%;">
                        <i class="icon-layers font-dark"></i>
                        <span class="caption-subject bold uppercase">Log files</span>
                    </div>
                    <br>
                    <div>
                        @foreach($files as $file)
                            <div class="btn {{($current_file == $file) ? 'btn-primary' : 'btn-success'}}"
                                 style="margin: 5px 0">
                                <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}"
                                   style="text-decoration: none !important;color:#fff">
                                    {{$file}}
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <hr>
                    @if($current_file)
                        <div>
                            <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}"><span
                                        class="fa fa-download"></span>
                                Download file</a>
                            -
                            <a id="delete-log"
                               href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}"><span
                                        class="fa fa-trash"></span> Delete current file</a>
                            @if(count($files) > 1)
                                -
                                <a id="delete-all-log" href="?delall=true"><span class="fa fa-trash"></span> Delete all
                                    files</a>
                            @endif
                        </div>
                        <br>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-borderless">
                <div class="box-body">
                    @if ($logs === null)
                        <div>
                            Log file >50M, please download it.
                        </div>
                    @else
                        <table id="table-log" class="table datatable dt-responsive display nowrap dc-table"
                               style="width:100% !important;">
                            <thead>
                            <tr>
                                <th class="all">SL</th>
                                <th class="all">Level</th>
                                <th class="min-phone-l">Context</th>
                                <th class="min-phone-l">Date</th>
                                <th class="min-phone-l">Content</th>
                                <th class="none">Details</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $key => $log)
                                <tr data-display="stack{{{$key}}}">
                                    <td>sl</td>
                                    <td class="text-{{{$log['level_class']}}}"><span
                                                class="fa fa-{{{$log['level_img']}}}"
                                                aria-hidden="true"></span> &nbsp;{{$log['level']}}</td>
                                    <td class="text">{{$log['context']}}</td>
                                    <td class="date">{{{$log['date']}}}</td>
                                    <td class="text">
                                        <code style="display:block;background:rgba(0,0,0,0.3)">
                                            {{{substr($log['text'],0,150)}}} ...
                                        </code>
                                    </td>
                                    <td>
                                        <code style="display:block;background:rgba(0,0,0,0.3)">
                                            {{{$log['text']}}}
                                            @if (isset($log['in_file']))
                                                <br/>{{{$log['in_file']}}}
                                            @endif
                                        </code>
                                        {{--@if ($log['stack'])--}}
                                        {{--<br>--}}
                                        {{--<code class="stack" id="stack{{{$key}}}"--}}
                                        {{--style="white-space: pre-wrap; color:#668899;background:rgba(0,0,0,0.3); display:block">{{{ trim($log['stack']) }}}--}}
                                        {{--</code>--}}
                                        {{--@endif--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- for datatable and date picker -->
    <script src="{{asset('common/vendors/datatable_responsive/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/plugins/bootstrap/datatables.bootstrap.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.dc-table').dataTable({
                'paging': true,
                'searching': true,
                'bInfo': false,
                "language": {
                    "aria": {
                        "sortAscending": ": {{ __('activate to sort column ascending') }}",
                        "sortDescending": ": {{ __('activate to sort column descending') }}"
                    },
                    "emptyTable": "{{ __('No data available in table') }}",
                    "info": "{{ __('Showing :start to :end of _TOTAL_ entries',['start'=>'_START_','end'=>'_END_']) }}",
                    "infoEmpty": "{{ __('No entries found') }}",
                    "infoFiltered": "{{ __('(filtered1 from :max total entries)',['max'=>'_MAX_']) }}",
                    "lengthMenu": "{{ __(':menu entries',['menu'=>'_MENU_']) }}",
                    "search": "{{ __('Search') }}:",
                    "zeroRecords": "{{ __('No matching records found') }}"
                },
                buttons: [
                    // { extend: 'print', className: 'btn dark btn-outline' },
                    // { extend: 'pdf', className: 'btn green btn-outline' },
                    // { extend: 'csv', className: 'btn purple btn-outline ' }
                ],

                responsive: {
                    details: {}
                }
            });

            if ($('#log-table')) {
                $('#log-table').dataTable({
                    "stateSave": true,
                    "stateSaveCallback": function (settings, data) {
                        window.localStorage.setItem("datatable", JSON.stringify(data));
                    },
                    "stateLoadCallback": function (settings) {
                        var data = JSON.parse(window.localStorage.getItem("datatable"));
                        if (data) data.start = 0;
                        return data;
                    }
                })
            }
        })
        $(document).ready(function () {
            $('#delete-log, #delete-all-log').click(function () {
                return confirm('Are you sure?');
            });
        });
    </script>
@endsection
