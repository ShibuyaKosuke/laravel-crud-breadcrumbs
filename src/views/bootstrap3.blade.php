@if ($breadcrumbs->count())
    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url() && $loop->remaining)
                <li><a href="{{ $breadcrumb->url() }}">{{ $breadcrumb->title() }}</a></li>
            @else
                <li class="active">{{ $breadcrumb->title() }}</li>
            @endif
        @endforeach
    </ol>
@endif
