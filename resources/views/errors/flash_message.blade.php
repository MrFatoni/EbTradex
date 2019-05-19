<?php
$class = '';
if(session('success')){
    $class = ' flash-success';
}
elseif(session('error') || (isset($errors) && $errors->any())){
    $class = ' flash-error';
}
elseif(session('warning')){
    $class = ' flash-warning';
}
?>
<div class="flash-message{{session('success') || session('error') || session('warning') || (isset($errors) && $errors->any()) ? ' flash-message-active flash-message-window' : ''}}">
    <div class="centralize-wrapper">
        <div class="centralize-inner">
            <div class="centralize-content{{$class}}">
                <div class="flash-removable">
                    <button type="button" class="close flash-close" aria-hidden="true">×</button>
                    <div class="flash-icon"></div>
                    <p>
                        @if(session('success'))
                            {{ session('success') }}
                        @elseif(session('error'))
                            {{ session('error') }}
                        @elseif(session('warning'))
                            {{ session('warning') }}
                        @elseif((isset($errors) && $errors->any()))
                            {{ __('Invalid data in field(s)') }}
                        @endif
                    </p>
                    <a class="flash-confirm hidden-flash-item btn btn-sm btn-info btn-flat" href="javascript:;">{{ __('Confirm') }}</a>
                    <a class="flash-close hidden-flash-item btn btn-sm btn-warning btn-flat" href="javascript:;">{{ __('Cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>