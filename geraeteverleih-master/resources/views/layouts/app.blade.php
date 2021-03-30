<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#3a444e">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#3a444e">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#3a444e">

    <link rel="icon" type="image/svg" href="{{asset('images/icons/htl_logo.svg')}}" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Geräteverleih') }}</title>

    <!-- Jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{asset("js/vendor/jquery-ui.min.js")}}"></script>
    <!-- Fonts -->
    @laravelPWA


@yield('head-top')
<!-- Styles -->
    {{--<link rel="apple-touch-icon" href="{{asset("images/icons/icon-512.png")}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{asset("images/icons/icon-152.png")}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset("images/icons/icon-180.png")}}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{asset("images/icons/icon-167.png")}}">--}}

    <link href="{{ asset('css/app.min.css')}}" rel="stylesheet"/>
    <link href="{{ asset('css/app-creative-dark.min.css')}}" rel="stylesheet" type="text/css" id="dark-style"/>
    <link href="{{ asset('css/app-creative.min.css')}}" rel="stylesheet" type="text/css" id="light-style"/>
    <link href="{{ asset('css/own.css')}}" rel="stylesheet"/>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" crossorigin="anonymous">
    @yield('head-bottom')

</head>
<body data-layout="topnav" class="loading">
<div class="wrapper">

    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="content-page">
        <div class="content">
            <!-- Topbar Start -->
            <nav class="navbar-custom topnav-navbar topnav-navbar-dark fixed-top position-fixed" id="desktopNav">
                <div class="container-fluid">

                    <!-- LOGO -->
                    <a href="{{route('index')}}" class="topnav-logo align-self-center">
                        <span class="topnav-logo-lg">
                                <img src="{{ asset('images/icons/htl_logo.svg') }}" alt="" height="50">
                            </span>
                        <span class="topnav-logo-sm">
                                <img src="{{ asset('images/icons/htl_logo.svg') }}" alt="" height="40">
                            </span>
                    </a>


                    <nav
                        class="navbar navbar-dark navbar-expand-lg float-left d-none d-lg-block mt-1 pt-2 pl-1  pr-0 mr-0">
                        <div class="d-none d-lg-block">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link align-middle" role="button" href="{{route("index")}}">Start</a>
                                </li>
                                @guest
                                    <li class="nav-item">
                                        {{--                                        <a class="nav-link align-middle" role="button" href="{{route("pages.show", "info")}}">Infos</a>--}}
                                    </li>
                                @else
                                    @if(Auth::user()->isStudent())
                                        <li class="nav-item">
                                            <a class="nav-link align-middle" role="button"
                                               href="{{route("orders.index")}}">Bestellungen</a>
                                        </li>
                                    @endif
                                    @if(Auth::user()->isTeacher())
                                        @if(Auth::user()->types->count() != 0)
                                            <li class="nav-item">
                                                <a class="nav-link align-middle" role="button"
                                                   href="{{route("devices.index")}}">Geräte</a>
                                            </li>
                                        @endif
                                            <li class="nav-item">
                                                <a class="nav-link align-middle" role="button"
                                                   href="{{route("orders.index")}}">Bestellungen</a>
                                            </li>
                                    @endif
                                    @if(Auth::user()->isAdmin())
                                        <li class="nav-item">
                                            <a class="nav-link align-middle" role="button"
                                               href="{{route("devices.index")}}">Geräte</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link align-middle" role="button"
                                               href="{{route("users.index")}}">Benutzer</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link align-middle" role="button"
                                               href="{{route("orders.index")}}">Bestellungen</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link align-middle" role="button"
                                               href="{{route("types.index")}}">Kategorien</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link align-middle" role="button"
                                               href="{{route("settings")}}">Einstellungen</a>
                                        </li>
                                    @endif
                                @endguest
                            </ul>
                        </div>
                    </nav>
                    <a class="navbar-toggle" data-toggle="collapse" data-target="#mobileNav">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                    @guest
                    @else
                        <ul class="list-unstyled topbar-right-menu float-right mb-0">
                            <li class="dropdown notification-list">
                                <a class="nav-link dropdown-toggle nav-user arrow-none mr-0" data-toggle="dropdown"
                                   href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <span class="account-user-avatar">
                                        <img
                                            src="https://avatars.dicebear.com/api/male/{{Auth::user()->displayName()}}.svg?options[fontSize]=35"
                                            alt="user-image" class="rounded-circle">
                                    </span>
                                    <span>
                                        <span class="account-user-name">{{Auth::user()->displayName()}}</span>
                                        <span
                                            class="account-position">{{Auth::user()->isStudent() ? Auth::user()->class : Auth::user()->role->name }}</span>
                                    </span>
                                </a>
                                <div
                                    class="dropdown-menu dropdown-menu-right dropdown-menu-animated topbar-dropdown-menu profile-dropdown"
                                    style="">
                                    <!-- item-->
                                    <a href="{{route('logout.azure')}}" class="dropdown-item notify-item">
                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                        @if(Auth::user()->isStudent() || Auth::user()->isTeacher())
                            <ul class="list-unstyled topbar-right-menu float-right mb-0">
                                <li class="notification-list">
                                    <a class="nav-link" id="shoppingCartInfo" href="{{route("shoppingCart.show")}}"><i
                                            class="fas fa-shopping-cart noti-icon"></i>
                                        <span
                                            class="badge badge-light">{{\Illuminate\Support\Facades\Auth::user()->shoppingCart->count()}}</span></a>
                                </li>
                            </ul>
                        @endif
                    @endguest
                </div>
            </nav>
            <div class="collapse d-lg-none fixed-top" style="top:70px;" id="mobileNav">
                <div class="navbar-dark bg-always-dark p-4">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link align-middle" role="button" href="{{route("index")}}">Start</a>
                        </li>
                        @guest
                            <li class="nav-item">
                                {{--                                <a class="nav-link align-middle" role="button" href="{{route("pages.show", "info")}}">Infos</a>--}}
                            </li>
                        @else
                            @if(Auth::user()->isStudent())
                                <li class="nav-item">
                                    <a class="nav-link align-middle" role="button"
                                       href="{{route("orders.index")}}">Bestellungen</a>
                                </li>
                            @endif
                            @if(Auth::user()->isTeacher())
                                @if(Auth::user()->types->count() != 0)
                                    <li class="nav-item">
                                        <a class="nav-link align-middle" role="button"
                                           href="{{route("devices.index")}}">Geräte</a>
                                    </li>
                                @endif
                                    <li class="nav-item">
                                        <a class="nav-link align-middle" role="button"
                                           href="{{route("orders.index")}}">Bestellungen</a>
                                    </li>
                            @endif
                            @if(Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link align-middle" role="button"
                                       href="{{route("devices.index")}}">Geräte</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link align-middle" role="button"
                                       href="{{route("users.index")}}">Benutzer</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link align-middle" role="button"
                                       href="{{route("orders.index")}}">Bestellungen</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link align-middle" role="button"
                                       href="{{route("types.index")}}">Kategorien</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link align-middle" role="button"
                                       href="{{route("settings")}}">Einstellungen</a>
                                </li>
                            @endif
                        @endguest
                    </ul>
                </div>
            </div>
            <!-- end Topbar -->

            @if($errors)
                @foreach ($errors->all() as $key=>$error)
                    <div id="error-{{$key}}" class="modal fade show" tabindex="-1" role="dialog" aria-hidden="false">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content modal-filled bg-danger">
                                <div class="modal-body p-4">
                                    <div class="text-center">
                                        <i class="fas fa-exclamation-circle h1"></i>
                                        <h4 class="mt-2">Ups!</h4>
                                        <p class="mt-3">{!! $error !!}</p>
                                        <button type="button" class="btn btn-light my-2" data-dismiss="modal">Ok
                                        </button>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <script type="text/javascript">
                        $(window).on('load', function () {
                            $('#error-{{$key}}').modal('show');
                        });
                    </script>
                @endforeach
            @endif
            <div class="my-3 pt-5">
                @yield('content')
            </div>

            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            {{date("Y")}} - {{env('APP_NAME')}}
                        </div>
                        <div class="col-md-6">
                            <div class="text-md-right footer-links">
                                <a href="{{route("policies")}}" class="text-muted">Richtlinien</a>
                                <a href="{{route("impressum")}}" class="text-muted">Impressum</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->


    </div>
    <!-- END wrapper -->
    <!-- Scripts - Vendor.min causes conflicts -->
    <script src="{{asset("js/vendor/jquery.mask.min.js")}}"></script>
    <script src="{{asset("js/vendor/bootstrap.bundle.min.js")}}"></script>
    <script src="{{asset("js/vendor/bootstrap-maxlength.min.js")}}"></script>
    <script src="{{asset("js/vendor/jquery.bootstrap.wizard.min.js")}}"></script>
    <script src="{{asset("js/vendor/jquery.bootstrap-touchspin.min.js")}}"></script>
    <script src="{{asset("js/vendor/highlight.pack.min.js")}}"></script>
    <script src="{{asset("js/vendor/select2.min.js")}}"></script>
    <script src="{{asset("js/vendor/simplebar.min.js")}}"></script>
    <script src="{{asset("js/vendor/jquery.bootstrap-touchspin.min.js")}}"></script>
    <script src="{{asset("js/vendor/jquery.toast.min.js")}}"></script>
    <script src="{{asset("js/vendor/moment.js")}}"></script>
    <script src="{{asset("js/vendor/moment-de.js")}}"></script>
    <script src="{{asset("js/vendor/daterangepicker.js")}}"></script>
    <script src="{{asset('js/hyper.js')}}"></script>
    <script src="{{asset('js/layout.js')}}"></script>
    <script src="{{asset("js/vendor/bootstrap-datepicker.min.js")}}"></script>
    @yield("body-bottom")
    <script>
        $.fn.datepicker.dates['de'] = {
            days: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag"],
            daysShort: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"],
            daysMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"],
            months: ["Jänner", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
            monthsShort: ["Jän", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
            today: "Heute",
            clear: "Löschen",
            closeText: "Schließen",
            weekHeader: "KW",
            dateFormat: "dd.mm.yy",
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ""
        };

        let stack = null;

        $(document).ready(function () {
            setStackCount()
        });

        $(window).on('resize', function () {
            setStackCount()
        });

        function setStackCount() {
            if (screen.width > 991)
                stack = 4
            else
                stack = 1
        }
    </script>
</body>
</html>
