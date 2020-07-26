@foreach (['danger', 'warning', 'success', 'info'] as $alertType)
    @if (Session::has('alert-' . $alertType))
        <div class="alert alert-{{ $alertType }} " role="alert">
            <div class="container">
                {{ Session::get('alert-' . $alertType) }}
            </div>
        </div>
    @endif
@endforeach

@if (Session::has('status' ))
    <div class="alert alert-success" role="alert">
        <div class="container">
            {{ Session::get('status') }}
        </div>
    </div>
@endif
