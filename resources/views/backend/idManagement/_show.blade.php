<div class="images" v-viewer>
    <div class="row">
        <div class="col-md-{{ $user->id_type == ID_PASSPORT ? '12' : '6' }}">
            <h4 class="id-header-title">{{ __('ID Card') }} {{ $user->id_type == ID_PASSPORT ? '' : __('Front') }}</h4>
            <img src="{{ get_id_image($user->id_card_front) }}" alt="{{ __('ID Card Back') }}" class="img-responsive cm-center id-image">
        </div>
        @if($user->id_type != ID_PASSPORT)
            <div class="col-md-6">
                <h4 class="id-header-title">{{ __('ID Card Back ') }}</h4>
                <img src="{{ get_id_image($user->id_card_back) }}" alt="{{ __('ID Card Back') }}" class="img-responsive cm-center id-image">
            </div>
        @endif
    </div>
</div>