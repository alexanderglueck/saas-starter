@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="card">
        <div class="card-header">
            {{ __('ui.settings.deactivate.title') }}
        </div>
        <div class="card-body">
            <form action="{{ route('user_settings.deactivate.store') }}" method="post">
                @csrf

                @subscriptionnotcancelled
                <p>{{ __('ui.settings.deactivate.warning') }}</p>
                @endsubscriptionnotcancelled

                {!! \App\Helpers\Form::password('current_password', __('ui.settings.deactivate.current_password') , true, true) !!}

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('ui.settings.deactivate.deactivate') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
