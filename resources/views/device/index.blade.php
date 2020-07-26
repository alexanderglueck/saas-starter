@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('ui.devices.title') }}</div>

                <div class="card-body">
                    <ol>
                        @foreach ($devices as $device)
                            <li>
                                user agent {{ $device->user_agent }}<br>
                                ip {{ $device->ip_address }}<br>
                                last active {{ date('Y-m-d H:i:s', $device->last_activity) }}<br>

                                @if(session()->getId() == $device->id)
                                    {{ __('ui.sessions.current_session') }}
                                @else
                                    <form action="{{ route('session.destroy') }}" method="post">
                                        @method('DELETE')
                                        @csrf

                                        <input type="hidden" name="session" value="{{ $device->id }}">

                                        <button type="submit" class="btn btn-outline-danger">{{ __('ui.logout') }}</button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ol>

                    @if (count($devices) > 1)
                        <form action="{{ route('device.destroy') }}" method="post">
                            @method('DELETE')
                            @csrf

                            <button type="submit" class="btn btn-outline-danger">{{ __('ui.devices.remove_all') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
