<ol class="breadcrumb">
    <li><a href="{{ route('home')}}"><i class="fa fa-home"></i> Home</a></li>
    @foreach(get_breadcrumbs() as $breadcrumb)
        @if($loop->last || !$breadcrumb['display_url'])
            <li class="active">{{ $breadcrumb['name'] }}</li>
        @else
            <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
        @endif
    @endforeach
</ol>