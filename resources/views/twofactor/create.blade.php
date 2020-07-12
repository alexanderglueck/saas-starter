@extends('layouts.app')

@section('content')
    {!!  $as_string  !!}
    {!! $as_uri !!}
    {!! $as_qr_code  !!}

    <form action="{{ route('two-factor.store') }}" method="post">
        @csrf
        <input type="text" name="token">
        <button type="submit" class="btn btn-primary">Enable</button>
    </form>

@endsection
