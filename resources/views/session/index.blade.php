@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('ui.sessions.title') }}</div>

                <div class="card-body">
                    <ol>
                        @foreach ($sessions as $session)
                            <li>
                                user agent {{ $session->user_agent }}<br>
                                ip {{ $session->ip_address }}<br>
                                last active {{ date('Y-m-d H:i:s', $session->last_activity) }}<br>

                                @if(session()->getId() == $session->id)
                                    {{ __('ui.sessions.current_session') }}
                                @else
                                    <form action="{{ route('session.destroy') }}" method="post">
                                        @method('DELETE')
                                        @csrf

                                        <input type="hidden" name="session" value="{{ $session->id }}">

                                        <button type="submit" class="btn btn-outline-danger">{{ __('ui.logout') }}</button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ol>

                    @if (count($sessions) > 1)
                        <form action="{{ route('session.destroy') }}" method="post">
                            @method('DELETE')
                            @csrf

                            <button type="submit" class="btn btn-outline-danger">{{ __('ui.sessions.logout_everywhere') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
