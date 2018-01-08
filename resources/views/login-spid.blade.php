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
    <link type="text/css" rel="stylesheet" href="{{ asset('/vendor/spid-auth/css/agid-spid-enter.min.1.0.0.css') }}">
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-rel {
            position: relative;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="flex-center position-rel full-height">
        <div id="app">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                @if (session('url.intended'))
                                @lang('spid-auth::messages.spid_access_required')
                                @else
                                @lang('spid-auth::messages.spid_login')
                                @endif
                            </div>
                            <div class="panel-body center">
                                @include('spid-auth::spid-button', ['size' => 'm'])
                                @lang('spid-auth::messages.or') <a href="{{ url('/') }}">@lang('spid-auth::messages.return_home')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
