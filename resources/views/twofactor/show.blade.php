@extends('layouts.app')

@section('content')

    <form action="{{ route('two-factor.destroy') }}" method="post">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-primary">Disable TFA</button>
    </form>

@endsection
