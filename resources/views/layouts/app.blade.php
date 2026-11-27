<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?> dir="rtl" <?php } ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Burundi') }}</title>
    <link rel="icon" id="favicon" type="image/x-icon" href="">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap-rtl.min.css')}}" rel="stylesheet">
    <?php } ?>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
    <link href="{{asset('css/style_rtl.css')}}" rel="stylesheet">
    <?php } ?>

    <link href="{{ asset('css/icons/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icons/font-awesome/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet">
    <link href="{{ asset('css/colors/blue.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chosen.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/dist/css/select2.min.css')}}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

</head>

<body>
    <div id="app" class="fix-header fix-sidebar card-no-border non-printable">

        <div id="main-wrapper">

            <header class="topbar">

                <nav class="navbar top-navbar navbar-expand-md navbar-light">
                    @include('layouts.header')
                </nav>

            </header>

            <aside class="left-sidebar">

                <!-- Sidebar scroll-->

                <div class="scroll-sidebar">

                    @include('layouts.menu')

                </div>

                <!-- End Sidebar scroll-->

            </aside>

            <footer class="footer">

                @include('layouts.footer')

            </footer>

        </div>

        <main class="py-4">
            @yield('content')
            <div id="overlay" style="display:none">
                <img src="{{ asset('images/spinner.gif') }}">
            </div>
        </main>
    </div>
    <div class="modal fade" id="notification_payout" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered notification-main" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title payout_subject" id="exampleModalLongTitle">
                        {{trans('lang.new_payout_request')}}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6><span id="auth_accept_name"
                            class="payout_msg">{{trans('lang.new_payout_request_created')}}</span></h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"><a href="{{url('payoutRequest')}}"
                            id="notification_add_payout_request">Go</a></button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('js/waves.js') }}"></script>
    <script src="{{ asset('js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote-bs4.js')}}"></script>
    <script src="{{ asset('js/jquery.resizeImg.js') }}"></script>
    <script src="{{ asset('js/mobileBUGFix.mini.js') }}"></script>

    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-firestore.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-functions.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-storage.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-database.js"></script>
    <script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
    <script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>

    <script src="{{ asset('js/chosen.jquery.js') }}"></script>
    <script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/crypto-js.js') }}"></script>
    <script src="{{ asset('js/jquery.cookie.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD35yP7OKA-KuC709mROV9o1_4dGfrBPQU&libraries=places">
        </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">

        var doNotDeleteAlert = "This is for demo, We can not allow to delete";
        var doNotUpdateAlert = "This is for demo, We can not allow to update content";

        jQuery(window).scroll(function () {
            var scroll = jQuery(window).scrollTop();
            if (scroll <= 60) {
                jQuery("body").removeClass("sticky");
            } else {
                jQuery("body").addClass("sticky");
            }
        });
        var database = firebase.firestore();
        var langcount = 0;
        var languages_list = database.collection('languages');
        languages_list.get().then(async function (snapshotslang) {
           
            if (snapshotslang.docs.length > 0) {
                snapshotslang.docs.forEach((doc) => {
                    var data = doc.data();
                    if (data.enable == true) {
                        langcount++;
                        $('#language_dropdown').append($("<option></option>").attr("value", data.id).attr("data-isrtl", data.isRtl).text(data.name));
                    }
                });

                if (langcount > 1) {
                    $("#language_dropdown_box").css('visibility', 'visible');
                }
                <?php if (session()->get('locale')) { ?>
                $("#language_dropdown").val("<?php    echo session()->get('locale'); ?>");
                <?php } ?>

            }
        });

        var url = "{{ route('changeLang') }}";

        $(".changeLang").change(function () {
            var slug = $(this).val();
            var isrtl = $(this).find(':selected').data('isrtl');
            if (isrtl == true) {
                setCookie('is_rtl', isrtl.toString(), 365);
            } else {
                setCookie('is_rtl', 'false', 365);
            }
            window.location.href = url + "?lang=" + slug;
        });

        var logActivityUrl = "{{ route('saveLogActivity') }}";
        function logFirebaseActivity(action, details = {}) {
            fetch(logActivityUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    user_id: '{{ auth()->id() }}',
                    action: action,
                    details: details
                })
            });
        }

        // logFirebaseActivity('Product Updated');

        function setCookie(cname, cvalue, exdays) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
        var database = firebase.firestore();

    </script>

    {{-- The list and detail pages of the tab count the badge themselves --}}
    @if(auth()->check() && !request()->routeIs('distribution.fuel.request', 'distribution.fuel.request.view') &&
        App\Models\Permission::where('role_id', auth()->user()->role_id)->where('permission', 'fuel-request')->exists())
        @include('distribution_fuel_request.menu_badge')
    @endif

    @yield('scripts')

</body>

</html>