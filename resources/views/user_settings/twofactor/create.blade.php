@extends('user_settings.layout.default')

@section('user_settings.content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('ui.settings.two_factor_authentication.title') }}</div>

                <div class="card-body">
                        {!!  $as_string  !!}<br>
                        {!! $as_uri !!}<br>
                        {!! $as_qr_code  !!}

                    <form action="{{ route('user_settings.two-factor.store') }}" method="post" class="form-horizontal">
                        @csrf

                        <x-inputs.input type="text" label="Token" name="token" required />
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">{{ __('ui.settings.two_factor_authentication.enable') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>





@endsection
