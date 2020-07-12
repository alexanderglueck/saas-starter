@extends('layouts.app')

@section('content')
    <ol>
        @foreach ($sessions as $session)
            <li>
                user agent {{ $session->user_agent }}<br>
                ip {{ $session->ip_address }}<br>
                last active {{ date('Y-m-d H:i:s', $session->last_activity) }}<br>

                @if(session()->getId() == $session->id)
                    Your current session
                @else
                    <form action="{{ route('session.destroy') }}" method="post">
                        @method('DELETE')
                        @csrf

                        <input type="hidden" name="session" value="{{ $session->id }}">

                        <button type="submit" class="btn btn-outline-danger">Logout</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ol>

    @if(count($sessions)>1)
        <form action="{{ route('session.destroy') }}" method="post">
            @method('DELETE')
            @csrf

            <button type="submit" class="btn btn-outline-danger">Logout everywhere</button>
        </form>
    @endif

@endsection
