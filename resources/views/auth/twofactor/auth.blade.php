@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>

                    <div class="card-body">
                        <form action="{{ $action }}" method="post">
                            @csrf
                            @foreach($credentials as $name => $value)
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endforeach
                            @if($remember)
                                <input type="hidden" name="remember" value="on">
                            @endif

                            <p class="text-center">
                                {{ __('To log in, open up your Authenticator app and issue the 6-digit code.') }}
                            </p>
                            <div class="form-group row">
                                <label for="token"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Two-Factor Token') }}</label>

                                <div class="col-md-6">
                                    <input id="token" type="text"
                                           class="form-control @if($error) is-invalid @endif"
                                           name="token"
                                           minlength="6"
                                           placeholder="123456"
                                           required
                                           autofocus>
                                    @if($error)
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ __('The Code is invalid or has expired.') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember_browser" id="remember_browser" {{ old('remember_browser') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember_browser">
                                            {{ __("Remember this browser") }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
