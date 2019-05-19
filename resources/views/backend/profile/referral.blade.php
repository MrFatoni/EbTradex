@extends('backend.layouts.main_layout')

@section('title', $title)

@section('content')
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            @include('backend.profile.avatar', ['profileRouteInfo' => profileRoutes('user', $user->id)])
        </div>
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                @include('backend.profile.profile_nav')

                <div class="box box-solid">
                    <div class="box-body text-center" style="min-height: 300px">
                        <div class="row" style="margin-top: 30px">
                            <div class="col-md-8 col-md-offset-2">
                                @if($user->referral_code)
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="link" readonly value="{{ route('register.index',['ref' => $user->referral_code ]) }}">
                                        <span class="input-group-btn">
                                     <button class="btn btn-default" title="{{ __('Copy Link') }}" data-toggle="tooltip" type="button" onclick="copyLink()"><i class="fa fa-clipboard text-aqua"></i></button>
                                </span>
                                    </div>
                                @else
                                    <a class="btn btn-primary"
                                       href="{{ route('profile.referral.generate') }}">{{ __('Generate Referral Link') }}</a>

                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function copyLink() {
            var copyText = document.getElementById("link");
            copyText.select();
            document.execCommand("copy");
        }
    </script>
@endsection