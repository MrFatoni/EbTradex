@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary box-borderless">
                @if($wallet->stockItem->deposit_status == ACTIVE_STATUS_ACTIVE)
                    <div class="box-header text-center with-border">
                        <h3 class="box-title font-weight-bold">
                            {{ __('Your :stockItem Deposit Address', ['stockItem' => $wallet->stockItem->item]) }}
                        </h3>
                    </div>
                    <div class="box-body text-center">
                        <div id="qrcode"></div>
                        <h4 class="bg-gray-light py-5 font-weight-bold" data-toggle="tooltip" data-placement="top" title="{{ __('Click to copy') }}">
                            <strong>{{ $walletAddress }}</strong>
                        </h4>
                    </div>
                    <div class="box-footer text-center font-weight-bold">
                        <p>
                            {{ __('Only send :stockItemName (:stockItem) to this address. Sending any other digital asset will result in permanent loss.', ['stockItemName' => $wallet->stockItem->item_name, 'stockItem' => $wallet->stockItem->item]) }}
                        </p>
                        <p>
                            {{ __('After making a deposit, you can track its progress on the Deposit & Withdrawal History page.') }}
                        </p>
                    </div>
                @else
                    <div class="box-body text-center">
                        <h4 class="bg-gray-light py-5 font-weight-bold" data-toggle="tooltip" data-placement="top" title="{{ __('Click to copy') }}">
                            <strong>{{ $walletAddress }}</strong>
                        </h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/qrcode/jquery.qrcode.js') }}"></script>
    <script src="{{ asset('common/vendors/qrcode/qrcode.js') }}"></script>
    <script>
        $(function () {
            $('#qrcode').qrcode({width: 200, height: 200, text: "{{ $walletAddress }}"});
        });
    </script>
@endsection