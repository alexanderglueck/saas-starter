@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Setup') }}</div>

                    <div class="card-body">
                        <p>Change your team name</p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <x-inputs.input type="text" name="name" :label="__('Team name')" required
                                            :value="$team->name"/>

                            <x-inputs.submit label="Update" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
