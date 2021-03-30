<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Geräteverleih') }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#313B46">

    <link rel="icon" type="image/svg" href="{{asset('images/icons/htl_logo.svg')}}" />

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.min.css')}}" rel="stylesheet" />
    <link href="{{ asset('css/app-creative-dark.min.css')}}" rel="stylesheet" type="text/css" id="dark-style" />
    <link href="{{ asset('css/app-creative.min.css')}}" rel="stylesheet" type="text/css" id="light-style" />
    <link href="{{ asset('css/own.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" crossorigin="anonymous">

    @yield('custom-head')
</head>
<body>
<body class="authentication-bg authentication-bg-primary">

<div class="account-pages mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header pt-4 pb-4 text-center bg-primary">
                        <a href="{{route("index")}}">
                            <span><img src="{{asset('images/icons/htl_logo.svg')}}" alt="" height="40"></span>
                        </a>
                    </div>

                    <div class="card-body p-4">
                        <div class="text-center">
                            <h1 class="text-error">@yield('code')</h1>
                            <h4 class="text-uppercase text-danger mt-3">@yield('message')</h4>
                            <a class="btn btn-primary mt-3" href="{{route("index")}}"><i class="mdi mdi-reply"></i> Zurück zur Startseite</a>
                        </div>
                    </div> <!-- end card-body-->
                </div>
                <!-- end card -->
            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
<!-- end page -->
<script src="{{asset('js/vendor.min.js')}}"></script>
<script src="{{asset('js/moment-de_AT.UTF-8.js')}}"></script>
<script src="{{asset('js/app.min.js')}}"></script>
</body>
</html>
