@extends('backend.layouts.main_layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('testpost') }}" method="post" class="validation" data-ajax-submission="y">
                @csrf
                <input type="hidden" name="base_key" value="{{base_key()}}">
                <label for="name">Name</label>
                <input type="text" name="{{ fake_field('first_name') }}">
                <span class="cval-error" data-cval-error="{{ fake_field('first_name') }}">{{ $errors->first('first_name') }}</span>
                <button class="form-submission-button" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection
@section('script')
<script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.validation').cValidate();
        });
    </script>
@endsection