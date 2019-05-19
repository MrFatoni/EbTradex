<section class="content-header">
    <div class="page-title-bar">
        @include('backend.layouts.includes._breadcrumb')
    </div>
</section>


<section class="system-notices">
    <div class="row">
        @foreach(get_system_notices() as $notice)
            <div class="col-lg-12">
                <div class="alert alert-{{ $notice->type }} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4>{{ $notice->title }}</h4>
                    {{ $notice->description }}
                </div>
            </div>
        @endforeach
    </div>
</section>
