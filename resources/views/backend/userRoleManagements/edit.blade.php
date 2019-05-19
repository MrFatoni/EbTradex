@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Edit User Role: {{ $userRoleManagement->role_name }}</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('user-role-managements.index') }}" class="btn btn-primary back-button">Back</a>
            </div>
        </div>
        <div class="box-body">
            {!! Form::open(['route'=>['user-role-managements.update',$userRoleManagement->id], 'method'=>'PUT','class'=> 'user-role-management-form']) !!}
            @include('backend.userRoleManagements._form',['buttonText'=>__('Update')])
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script src="{{ asset('backend/assets/js/role_manager.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(document).on('ifChecked', '.module', function () {
                $('.module_action_' + $(this).attr('data-id')).iCheck('check');
            });
            $(document).on('ifUnchecked', '.module', function () {
                $('.module_action_' + $(this).attr('data-id')).iCheck('uncheck');
            });

            $(document).on('ifChecked', '.task', function () {
                $('.task_action_' + $(this).attr('data-id')).iCheck('check');
            });

            $(document).on('ifUnchecked', '.task', function () {
                $('.task_action_' + $(this).attr('data-id')).iCheck('uncheck');
            });

            $(document).ready(function () {
                $('.user-role-management-form').cValidate({});
            });
        });
    </script>
@endsection