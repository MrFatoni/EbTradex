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
                    <div class="box-body">
                        @if($user->userInfo->is_id_verified == ID_STATUS_UNVERIFIED)
                            @include('backend.uploadID._create_form')
                        @else
                            @include('backend.uploadID._show')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('before-style')
    <link rel="stylesheet" href="{{ asset('common/vendors/bootstrap-fileinput/css/jasny-bootstrap.css') }}">
    <style>
        .thumbnail {
            width: 150px; height: 150px; padding: 30px 0 !important;
        }
        .custom-box-footer {
            background: rgba(0,0,0,0.1);
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 5px;
        }
        .fade-enter-active, .fade-leave-active {
            transition: all .3s;
        }

        .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
            opacity: 0;
        }
        .box-clickable {
            cursor: pointer;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script src="{{ asset('common/vendors/bootstrap-fileinput/js/jasny-bootstrap.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate({});
        });

        new Vue({
            el: "#app",
            data: {
                step: 1,
                idType: false
            },
            methods : {
                nextStep : function(id) {
                    this.step = 2;
                    this.idType = id;
                },
                previousStep : function () {
                    this.step = 1;
                    this.idType = false;
                }
            }
        });
    </script>
@endsection