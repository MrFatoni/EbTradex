@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <h3 class="page-header">{{ $title }}</h3>
    {!! $list['filters'] !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <table class="table datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            @if(!isset($userId))
                                <th class="all">{{ __('Email') }}</th>
                                <th class="none">{{ __('First Name') }}</th>
                                <th class="none">{{ __('Last Name') }}</th>
                            @endif
                            <th class="all">{{ __('Stock Item') }}</th>
                            <th class="all">{{ __('Transaction Type') }}</th>
                            @if(!$journalType)
                                <th class="all">{{ __('Journal') }}</th>
                            @endif
                            <th class="all">{{ __('Amount') }}</th>
                            <th class="min-desktop">{{ __('Date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $transaction)
                            <tr>
                                @if(!isset($userId))
                                    <td>
                                        @if(has_permission('users.show'))
                                            <a href="{{ route('users.show', $transaction->user_id) }}">{{ $transaction->email }}</a>
                                        @else
                                            {{ $transaction->email }}
                                        @endif
                                    </td>
                                    <td>{{ $transaction->first_name }}</td>
                                    <td>{{ $transaction->last_name }}</td>
                                @endif
                                <td>{{ $transaction->item }}</td>
                                <td>{{ get_transaction_type($transaction->transaction_type) }}</td>
                                @if(!$journalType)
                                    @php
                                        $journal = array_flip(config('commonconfig.journal_type'))[$transaction->journal];
                                    @endphp
                                    <td>
                                        <span>{{ title_case(str_replace('-',' ',$journal)) }}</span>
                                    </td>
                                @endif
                                <td>{{ $transaction->amount }}</td>
                                <td>{{ $transaction->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    @php
                        $journal = array_flip(config('commonconfig.journal_type'));
                    @endphp
                    <div class="row">
                    @forelse($summary->groupBy(['item','journal']) as $coin => $coinSummary)
                        <div class="col-md-4 col-sm-6">
                        <table class="table table-striped table-bordered" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th class="text-center bg-aqua-active" colspan="2">{{ __('Summary (:coin)',['coin'=>$coin]) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($coinSummary as $transactionType => $transaction)
                                <tr>
                                    <td><strong>{{ title_case(str_replace('-',' ',$journal[$transactionType])) }}</strong></td>
                                    <td class="text-right">{{ $transaction->first()->amount }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        </div>
                    @empty
                        <p class="text-center">{{ __("No summary found.") }}</p>
                    @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! $list['pagination'] !!}
    @include('backend.transactions._transaction_nav', ['routeName' => request()->route()->getName()])
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

        $('a').tooltip();
    </script>
@endsection