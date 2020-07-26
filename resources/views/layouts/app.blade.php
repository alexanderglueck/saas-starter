<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('ui.toggle_navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @subscribed


                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('plans.index') }}">
                                    {{ __('ui.plans') }}
                                </a>
                            </li>

                            @auth
                                @if(auth()->user()->onTrial())
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            {{ __('ui.trial_ends_in', ['days' => auth()->user()->trial_ends_at->diffForHumans() ]) }}

                                        </a>
                                    </li>
                                @endif
                            @endauth

                        @endsubscribed
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('ui.login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('ui.register') }}</a>
                                </li>
                            @endif
                        @else
                            @impersonating
                            <li class="nav-item ">
                                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('impersonation-form').submit();">Stop
                                    impersonating</a>
                            </li>
                            <form id="impersonation-form" action="{{ route('user.impersonate') }}" method="post">
                                @csrf
                                {{ method_field('delete') }}
                            </form>
                            @endimpersonating

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('user_settings.profile.show') }}">
                                        {{ trans('ui.settings.title') }}
                                    </a>

                                    <a class="dropdown-item" href="{{ route('logs.index') }}">
                                        {{ trans('ui.logs') }}
                                    </a>


                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="{{ route('teams.index') }}">
                                        {{ trans('ui.team') }}
                                    </a>

                                    <a class="dropdown-item" href="{{ route('roles.index') }}">
                                        {{ trans('ui.roles') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('session.index') }}"
                                     >
                                        {{ __('ui.sessions.title') }}
                                    </a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('ui.logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @include('layouts.partials.alert')

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @yield('js-links')

    @yield('js')
</body>
</html>
