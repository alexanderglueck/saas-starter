@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('ui.settings.two_factor_authentication.title') }}</div>

                    <div class="card-body">
                        <p>{{ __('ui.two_factor_authentication.backup_tokens') }}</p>
                        <ol>
                            @foreach($backupCodes as $backupCode)
                                <li><pre>{{ $backupCode->code }}</pre></li>
                            @endforeach
                        </ol>

                        <form action="{{ route('user_settings.two-factor.destroy') }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">{{ __('ui.settings.two_factor_authentication.disable') }}</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header">
                        {{ __('ui.settings.two_factor_authentication.devices') }}
                    </div>
                    <div class="card-body">
                        <ol>
                            @foreach($devices as $device)
                                <li>
                                    <pre>{{ $device->name }}</pre>
                                    @if($device->token == request()->cookie('2fa_remember'))
                                        Current browser
                                    @endif

                                    <form action="{{ route('device.destroy') }}" method="post">
                                        @method('DELETE')
                                        @csrf

                                        <input type="hidden" name="device" value="{{ $device->id }}">

                                        <button type="submit" class="btn btn-outline-danger">{{ __('ui.settings.two_factor_authentication.remove') }}</button>
                                    </form>
                                </li>
                            @endforeach
                        </ol>

                        @if (count($devices) > 1)
                            <form action="{{ route('device.destroy') }}" method="post">
                                @method('DELETE')
                                @csrf

                                <button type="submit" class="btn btn-outline-danger">{{ __('ui.settings.two_factor_authentication.remove_all') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
