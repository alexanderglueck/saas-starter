@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="card">
        <div class="card-header">
            {{ __('ui.settings.password.title') }}
        </div>
        <div class="card-body">
            <form action="{{ route('user_settings.password.update') }}" method="post">
                @csrf
                @method('PUT')

                {!! \App\Helpers\Form::password('password_current', __('ui.settings.password.current_password'), true, true) !!}
                {!! \App\Helpers\Form::password('password', __('ui.settings.password.new_password'), true) !!}
                {!! \App\Helpers\Form::password('password_confirmation', __('ui.settings.password.confirm_password'), true) !!}

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('ui.settings.password.update') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
