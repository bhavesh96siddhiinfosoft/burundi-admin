<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="description" content="Burundi">
    <meta name="author" content="Burundi">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title id="app_name">{{ config('app.name', 'Burundi') }}</title>

    <link rel="icon" id="favicon" type="image/x-icon" href="{{ asset('images/burundi_logo.jpeg') }}">

    <!-- Fonts -->

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->

    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    @yield('style')

</head>

<body>

    <style type="text/css">
        .form-group.default-admin {
            padding: 10px;
            font-size: 14px;
            color: #000;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 0px 6px 0px rgba(0, 0, 0, 0.5);
            margin: 20px 10px 10px 10px;
        }

        .form-group.default-admin .crediantials-field {
            position: relative;
            padding-right: 15px;
            text-align: left;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .form-group.default-admin .crediantials-field>a {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto;
            height: 20px;
        }

        .login-register {
            background-color: #000;
        }
    </style>


    <section id="wrapper">


        <div class="login-register">


            <div class="login-logo text-center py-3">

                <a href="#"><img src="{{ asset('images/burundi_logo.jpeg') }}" class="dark-logo"> </a>

            </div>

            <div class="login-box card" style="margin-bottom:0%;">


                <div class="card-body">

                    @if(count($errors) > 0)
                        @foreach($errors->all() as $message)
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button>
                                <span>{{ $message }}</span>
                            </div>
                        @endforeach
                    @endif

                    <form class="form-horizontal form-material" method="POST" action="{{ route('login') }}">

                        @csrf

                        <div class="box-title m-b-20">{{ __('Login') }}</div>


                        <div class="form-group ">


                            <div class="col-xs-12">


                                <input class="form-control" placeholder="{{ __('Email Address') }}" id="email"
                                    type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>


                            @error('email')

                                <span class="invalid-feedback" role="alert">

                                    <strong>{{ $message }}</strong>

                                </span>

                            @enderror


                        </div>


                        <div class="form-group">


                            <div class="col-xs-12">


                                <input id="password" placeholder="{{ __('Password') }}" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="current-password">
                            </div>


                            @error('password')

                                <span class="invalid-feedback" role="alert">

                                    <strong>{{ $message }}</strong>

                                </span>

                            @enderror


                        </div>


                        <div class="form-group text-center m-t-20">


                            <div class="col-xs-12">


                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember')
    ? 'checked' : '' }}>


                                <label class="form-check-label" for="remember">

                                    {{ __('Remember Me') }}

                                </label>


                            </div>


                        </div>


                        <div class="form-group text-center m-t-20 mb-0">


                            <div class="col-xs-12">


                                <button type="submit"
                                    class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">

                                    {{ __('Login') }}

                                </button>



                            </div>


                        </div>
                    </form>
                    {{--<h3 class="mt-3">Download APK:</h3>
                    <div class="">
                       <button class="btn btn-info ml-auto mb-2" type="button"
                            onclick="window.location.href='{{ route('apk.download', ['apk' => 'customer']) }}'"><i
                                class="fa fa-download"></i>
                            {{trans("lang.customer_app")}}</button><br>

                        <button class="btn btn-info m-auto mb-2" type="button"
                            onclick="window.location.href='{{ route('apk.download', ['apk' => 'owner']) }}'"><i
                                class="fa fa-download"></i>
                            {{trans("lang.owner_app")}}</button><br>

                        <button class="btn btn-info ml-auto mt-2" type="button"
                            onclick="window.location.href='{{ route('apk.download', ['apk' => 'employee']) }}'"><i
                                class="fa fa-download"></i> {{trans("lang.employee_app")}}</button>
                                
                    </div>--}}
                </div>


            </div>

        </div>

    </section>

    <script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-firestore.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-storage.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-database.js"></script>
    <script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
    <script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
    <script src="{{ asset('js/crypto-js.js') }}"></script>
    <script src="{{ asset('js/jquery.cookie.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.js') }}"></script>

    <script type="text/javascript">

        function copyToClipboard(text) {
            const elem=document.createElement('textarea');
            elem.value=text;
            document.body.appendChild(elem);
            elem.select();
            document.execCommand('copy');
            document.body.removeChild(elem);
        }


    </script>


</body>

</html>