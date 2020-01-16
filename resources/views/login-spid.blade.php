@inject('SPIDAuth', 'SPIDAuth')

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SPIDAuth') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div id="app" class="flex-center position-ref full-height">
        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                @if (session('url.intended'))
                                @lang('spid-auth::messages.spid_access_required')
                                @else
                                @lang('spid-auth::messages.spid_login')
                                @endif</div>
                            <div class="card-body">
                                @include('spid-auth::spid-button', ['size' => 'm'])
                                @lang('spid-auth::messages.or') <a href="{{ url('/') }}">@lang('spid-auth::messages.return_home')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
