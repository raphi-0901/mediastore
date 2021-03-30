@extends('layouts.app')
@section('body-bottom')
    <script src="{{asset("/js/vendor/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("/js/vendor/dataTables.bootstrap4.js")}}"></script>
@endsection
@section('content')
    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card">

                        <!-- Logo -->
                        <div class=" pt-4 pb-4 text-center" id="lkwContainer">
                            <span> <img src="{{ asset('images/icons/LKW.svg') }}" id="lkw" height="150"></span>
                        </div>

                        <div class="card-body p-4">

                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center mt-0 font-weight-bold">{{ __('Login') }}</h4>
                            </div>
                            <div class="text-center mt-3" id="office365">
                                <p class="text-muted">Sie können sich jetzt direkt anmelden mit</p>
                                <a href="{{route("login.azure")}}">
                                    <img src="{{ asset('images/icons/office-365.svg') }}" alt="" height="50"></a>
                                <p class="mt-4 text-muted" id="safari-info">Zurzeit ist es leider nicht möglich die
                                    Office365 Anmeldung über Safari zu verwenden. Solange dieser Fehler besteht,
                                    verwenden Sie bitte einen anderen Browser.</p>
                            </div>

                            <div id="loginForm" class="mt-4">
                                <div class="text-center mt-3">
                                    <p class="text-muted mb-3 text-center">E-Mail und Passwort für die Anmeldung
                                        eingeben<br/>
                                        Dieses Formular funktioniert nur für manuell erstellte Benutzer.
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label for="email">{{ __('E-Mail') }}</label>
                                        <input class="form-control" id="email" placeholder="Email eingeben"
                                               type="email"
                                               class="form-control {{--@error('email') is-invalid @enderror--}}"
                                               name="email" value="{{ old('email') }}" required autocomplete="email"
                                               autofocus>

                                        {{-- @error('email')
                                         <div class="invalid-feedback d-block">
                                             <strong>{{ $message }}</strong>
                                         </div>
                                         @enderror--}}
                                    </div>

                                    <div class="form-group">
                                        @if (Route::has('password.request'))
                                            <a class="text-muted float-right"
                                               href="{{ route('password.request') }}">
                                                <small>{{ __('Passwort vergessen?') }}</small>
                                            </a>
                                        @endif
                                        <label for="password">Passwort</label>
                                        <div class="input-group input-group-merge">
                                            <input id="password" type="password" placeholder="Passwort eingeben"
                                                   class="form-control" name="password" required
                                                   autocomplete="current-password">
                                            <div class="input-group-append" data-password="false">
                                                <div class="input-group-text">
                                                    <span class="fas fa-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" name="remember"
                                                   id="checkbox-signin" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                   for="checkbox-signin">{{ __('Angemeldet bleiben') }}</label>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0 text-center">
                                        <button class="btn btn-info mt-2"
                                                type="submit"> {{ __('Anmelden') }} </button>
                                    </div>

                                </form>
                            </div>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <div class="text-muted" style="cursor: pointer;" id="showLoginForm">Loginformular
                                verwenden
                            </div>
                            <div class="text-muted" style="cursor: pointer;" id="showOffice365">Office365
                                verwenden
                            </div>
                            @if (Route::has('register'))
                                <p class="text-muted mt-2" id="register">Noch kein Account? <a
                                        href="{{ url('/register') }}"
                                        class="text-muted ml-1"><b>Registrieren</b></a></p>
                            @endif
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->

            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            $(document).ready(function () {
                $('#showOffice365').hide()
                $('#loginForm').hide()
                $('#register').hide()
                $('footer').hide()


                $('#showLoginForm').click(function () {
                    $('#loginForm').show()
                    $('#register').show()
                    $('#office365').hide()
                    $('#showLoginForm').hide()
                    $('#showOffice365').show()
                    $('#lkwContainer').hide()
                })

                $('#showOffice365').click(function () {
                    $('#loginForm').hide()
                    $('#register').hide()
                    $('#office365').show()
                    $('#showLoginForm').show()
                    $('#showOffice365').hide()
                    $('#lkwContainer').show()
                })

                //Show Message for Safari users
                $('#safari-info').hide()
                let isSafari = /constructor/i.test(window.HTMLElement) || (function (p) {
                    return p.toString() === "[object SafariRemoteNotification]";
                })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));
                if (isSafari) $('#safari-info').show()
            })
        })
    </script>
@endsection
