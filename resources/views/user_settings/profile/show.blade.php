@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="card">
        <div class="card-header">
            {{ trans('ui.settings.profile.title') }}
        </div>
        <div class="card-body">
            <form action="{{ route('user_settings.profile.update') }}" method="post">
                @csrf
                {{ method_field('PUT') }}

                {!! \App\Helpers\Form::text('name', trans('ui.settings.profile.name'), auth()->user()->name, true, true) !!}
                {!! \App\Helpers\Form::email('email', trans('ui.settings.profile.email'), auth()->user()->email, true) !!}

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{ trans('ui.settings.profile.update') }}
                        </button>
                    </div>
                </div>

            </form>

            @if ( ! auth()->user()->hasVerifiedEmail())
                <form action="{{ route('verification.resend') }}" method="post" >
                    @csrf

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{ trans('Resend verification email') }}
                            </button>
                        </div>
                    </div>

                </form>
            @endif
        </div>
    </div>
@endsection
