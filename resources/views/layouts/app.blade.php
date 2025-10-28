<!DOCTYPE html>
<html lang="en">

<head>

    <title>Locum Optician Agency | Locum Dispensing Optician - Locumkit.Com | Locum Accountant</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="description"
        content="LocumKit is a bespoke platform which acts for Locum Optometrists and Locum Dispensing Opticians. We offer it all from Accountancy to locum bookings. We connects locum Optometrists and Opticians with employers">
    <meta name="keywords" content="Locum agency, Locum optician agency, Locum dispensing optician">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="/frontend/locumkit-template/new-design-assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/frontend/locumkit-template/new-design-assets/css/animations.min.css" type="text/css" rel="stylesheet">
    <link href="/frontend/locumkit-template/new-design-assets/css/theme.css" type="text/css" rel="stylesheet">
    <link href="/frontend/locumkit-template/new-design-assets/css/style.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootsrap-5.css') }}">
    <link href="/frontend/locumkit-template/new-design-assets/css/theme-fonts.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/frontend/locumkit-template/new-design-assets/css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', `{{ config('app.google_tag_manager_id') }}`);
    </script>

    <style>
        #mc_embed_signup input.mce_inline_error {
            border-color: #6B0505;
        }

        #mc_embed_signup div.mce_inline_error {
            margin: 0 0 1em 0;
            padding: 5px 10px;
            background-color: #6B0505;
            font-weight: bold;
            z-index: 1;
            color: #fff;
        }

        svg {
            color: #696969;
        }
    </style>

    @stack('styles')
</head>

<body>

    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('app.google_tag_manager_id') }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <div id="loader-div" style="display: none;">
        <div class="loader"></div>
    </div>

    <header class="header">
        <div class="container-fluid headbar">
            <div class="container">
                <div class="row">
                    <nav class="navbar navbar-default">
                        <div class="container">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                    data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                                <a class="navbar-brand" href="/">
                                    <img src="/frontend/locumkit-template/new-design-assets/img/logo.png"
                                        title="Locumkit" alt="Locumkit" width="80px"
                                        style="width: 60px; transition: all 0.4s ease 0s;">
                                </a>
                            </div>
                            <div id="navbar" class="navbar-collapse collapse">
                                <ul class="nav navbar-nav navbarmid">
                                    <li class="@if (Route::currentRouteName() === 'index') active @endif"><a
                                            href="/">Home</a>
                                    </li>
                                    <li class="@if (Route::currentRouteName() === 'contact') active @endif"><a
                                            href="/contact">Contact Us</a>
                                    </li>

                                    <li class="@if (Route::currentRouteName() === 'accountancy') active @endif"><a href="/accountancy"
                                            title="Accountancy" alt="Luis">Accountancy</a></li>

                                    @guest
                                        <li><a href="javascript:void(0);" title="Log In" alt="Log In" data-toggle="modal"
                                                data-target="#login-form-model">Log In</a></li>
                                        <li class="@if (Route::currentRouteName() === 'register') active @endif"><a href="/register"
                                                title="Register" alt="Register">Register</a></li>
                                    @else
                                        @can('is_freelancer')
                                            <li><a href="/freelancer/dashboard" title="Dashboard" alt="Dashboard">My
                                                    Dashboard</a></li>
                                        @endcan
                                        @can('is_employer')
                                            <li><a href="/employer/dashboard" title="Dashboard" alt="Dashboard">My Dashboard</a>
                                            </li>
                                        @endcan
                                        <li>
                                            <a href="javascript:void(0);" onclick="$('#logout-form').submit();"
                                                title="Logout" alt="Logout"><i class="fa fa-power-off"
                                                    aria-hidden="true"></i></a>
                                        </li>
                                        <form style="display: none;" aria-hidden="true" action="/logout"
                                            id="logout-form" style="display: inline-block;" method="post" hidden>
                                            @csrf
                                        </form>
                                    @endguest

                                </ul>
                                <ul class="nav navbar-nav navbar-right hidden-xs">
                                    <a href="https://play.google.com/store/apps/details?id=com.FuduGo.Locumkit&amp;hl=en"
                                        target="_blank"><img
                                            src="/frontend/locumkit-template/new-design-assets/images/googleplay.png"
                                            class="Locumkit Google Play"></a>
                                    <a href="https://itunes.apple.com/gb/app/locumkit/id1362518464"
                                        target="_blank"><img
                                            src="/frontend/locumkit-template/new-design-assets/images/appstore.png"
                                            class="Locumkit App Store"></a>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    <div class="modal fade-scale" id="sremail" tabindex="-1" role="dialog">
        <div class="modal-dialog cmnpopup bvpop" role="document">
            <div class="modal-content nobshadow col-md-12 col-sm-12 col-xs-12">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <div class="inbox" id="video-iframe">
                </div>
            </div>
        </div>
    </div>

    <div id="login-form-model" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal"
                        onclick="close_dive('profession_question');">×</button>
                    <section class="signsc">
                        <div class="innerhead signlft">
                            <div class="container-fluid">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-right">

                                    <div class="center">
                                        <h1>Work the way you <br> want. Find opportunities <br> around you.</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="signrgt">
                            <div class="col-md-12 col-sm-12 col-xs-12 formlft">
                                <h2>Welcome back!</h2>

                                @if ($errors->any())
                                    <div class="alert alert-danger"
                                        style="margin-bottom: 15px; padding: 10px; border-radius: 5px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">
                                        <ul class="mb-0" style="margin-bottom: 0; padding-left: 20px;">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('login') }}" method="post" id="signinform">
                                    @csrf
                                    <div class="form-group">
                                        <label for="email" style="font-weight:800;">Enter your email or
                                            username</label>
                                        <input type="text"
                                            class="form-control @error('login') is-invalid @enderror" id="login-email"
                                            name="login" placeholder="Enter username or email"
                                            value="{{ old('login') }}" required="">
                                        @error('login')
                                            <div class="invalid-feedback"
                                                style="display: block; color: #dc3545; font-size: 0.875em; margin-top: 0.25rem;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password" style="font-weight: 800;">Enter your password</label>
                                        <div class="input-group @error('password') is-invalid @enderror"
                                            style="border: 1px solid grey; display: flex; align-items: center; padding: 0px 10px;">
                                            <input type="password" style="border: none !important; box-shadow: none;"
                                                class="form-control" id="password" name="password"
                                                placeholder="Password" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="toggle-password"
                                                    style="cursor: pointer;">
                                                    <i class="bi bi-eye-slash-fill"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback"
                                                style="display: block; color: #dc3545; font-size: 0.875em; margin-top: 0.25rem;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>




                                    <div class="col-md-6 col-sm-6 col-xs-12 btnbx">
                                        <button type="submit"
                                            class="btn btn-default btn-1 lkbtn"><span>Login</span></button>
                                    </div>
                                    <div class="col-md-6 col-sm-6 linkbx col-xs-12">
                                        <a href="{{ route('password.request') }}" class="simpllink">Forgot
                                            password?</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            var passwordField = document.getElementById('password');
            var icon = this.querySelector('i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
            }
        });

        // Show login modal if there are validation errors
        @if ($errors->any())
            $(document).ready(function() {
                // Show the modal and prevent it from being hidden
                $('#login-form-model').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });

                // Prevent the modal from being hidden by other scripts
                $('#login-form-model').off('hide.bs.modal');

                // Only allow manual close via the X button
                $('#login-form-model .close').on('click', function() {
                    $('#login-form-model').modal('hide');
                });
            });
        @endif

        // Handle successful login (no errors) - ensure modal can close normally
        @if (!$errors->any())
            $(document).ready(function() {
                // Allow normal modal behavior when there are no errors
                $('#login-form-model').modal({
                    backdrop: true,
                    keyboard: true
                });
            });
        @endif

        // Additional protection to prevent modal hiding when there are errors
        @if ($errors->any())
            $(document).on('hide.bs.modal', '#login-form-model', function(e) {
                // Only allow hiding if it's triggered by the close button
                if (!$(e.target).hasClass('manual-close-allowed')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });

            // Allow manual close when clicking the X button
            $(document).on('click', '#login-form-model .close', function() {
                $('#login-form-model').addClass('manual-close-allowed');
                $('#login-form-model').modal('hide');
            });
        @endif
    </script>
    <footer class="footer animate fadeInUp" data-anim-type="fadeInUp" data-anim-delay="800">
        <div class="container-fluid">
            <div class="container">
                <div class="row foocolums">
                    <div class="col-md-4 col-sm-3 col-xs-12 fooc1">
                        <div class="footer-logo">
                            <img src="/frontend/locumkit-template/img/logo.png" title="Locumkit" alt="Locumkit"
                                width="110px">
                        </div>
                        <p>To subscribe to our newsletter please enter your email below.</p>

                        {{-- Mainlchip embed subcriber form started --}}
                        <link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet"
                            type="text/css">
                        <style type="text/css">
                            div#mc_embed_signup form {
                                padding: 0;
                            }

                            input#mc-embedded-subscribe {
                                margin: 0;
                                width: 100px !important;
                                text-transform: uppercase;
                                border-color: #25a8dd;
                                border-radius: 0;
                                float: left !important;
                                clear: none;
                            }

                            input#mce-EMAIL {
                                border-color: #fff;
                                border-radius: 0;
                                width: 66% !important;
                                float: left;
                            }

                            div#mce-responses {
                                padding: 0 !important;
                                margin: 0px !important;
                            }

                            div#mce-error-response {
                                margin: 0 !important;
                            }
                        </style>
                        <!--@if (session('success'))
-->
                        <!--    <div class="alert alert-success">-->
                        <!--        {{ session('success') }}-->
                        <!--    </div>-->
                        <!--
@endif-->
                        <div id="mc_embed_signup">
                            <!--<form action="//fudugosolutions.us13.list-manage.com/subscribe/post?u=41b543e3133f958b3c58df8b5&amp;id=fb441ef5f1" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form"-->
                            <form action="{{ route('subscribed-news-letter') }}" method="post" class="validate"
                                novalidate="novalidate">
                                @csrf
                                <div id="mc_embed_signup_scroll">
                                    <div class="mc-field-group form-group">
                                        <input type="email" value="" name="email" class="required email"
                                            id="mce-EMAIL" placeholder="Email Address" aria-required="true">
                                        <input type="submit" value="Subscribe" name="subscribe"
                                            id="mc-embedded-subscribe" class="btn btn-default btn-1 lkbtn mt-0"
                                            style="margin-top: 0px !important;">
                                    </div>
                                    <div id="mce-responses" class="clear">
                                        <div class="response" id="mce-error-response" style="display:none"></div>
                                        <div class="response" id="mce-success-response" style="display:none"></div>
                                    </div>
                                    <div style="position: absolute; left: -5000px; top:0" aria-hidden="true"><input
                                            type="text" name="b_41b543e3133f958b3c58df8b5_fb441ef5f1"
                                            tabindex="-1" value=""></div>
                                </div>
                            </form>
                        </div>
                        <script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script>
                        <script type="text/javascript">
                            (function($) {
                                window.fnames = new Array();
                                window.ftypes = new Array();
                                fnames[0] = 'EMAIL';
                                ftypes[0] = 'email';
                                fnames[1] = 'FNAME';
                                ftypes[1] = 'text';
                                fnames[2] = 'LNAME';
                                ftypes[2] = 'text';
                            }(jQuery));
                            var $mcj = jQuery.noConflict(true);
                        </script>
                        {{-- Mainlchip embed subcriber form ended --}}
                    </div>
                    <div class="col-md-2 col-sm-3 col-xs-12 fooc2">
                        <h5>Useful Links</h5>
                        <ul class="carretlist">
                            <li><a href="/about">About Us</a></li>
                            <li><a href="/contact">Contact Us</a></li>
                            <li><a href="/term-condition">Terms of use</a></li>
                            <li><a href="/privacy-policy">Privacy Policy</a></li>
                            <li><a href="/sitemap">Sitemap</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 fooc3">
                        <h5>Recent Posts</h5>
                        <ul class="carretlist">
                            @foreach ($latest_blogs as $blog)
                                <li>
                                    <a href="/blog/{{ $blog->slug }}" target="_blank"> {{ $blog->title }} </a>
                                    <span><i class="fa fa-calendar" aria-hidden="true"></i>
                                        {{ $blog->created_at->format('d M y') }} </span>
                                </li>
                            @endforeach
                            </li>
                        </ul>
                    </div>
                    @php
                        use App\Models\coreConfigData;

                        $socialIcons = [
                            'fb' => 'fa-facebook-square',
                            'li' => 'fa-linkedin-square',
                            'gp' => 'fa-google',
                            'pi' => 'fa-pinterest',
                            'tw' => '',
                        ];

                        $socialLinks = coreConfigData::whereIn('identifier', array_keys($socialIcons))->pluck(
                            'value',
                            'identifier',
                        );
                    @endphp
                    <div class="col-md-3 col-sm-3 col-xs-12 fooc4">
                        <h5>Contact Us</h5>
                        <ul>
                            <li><a href="tel:07452 998 238"><i class="fa fa-phone" aria-hidden="true"></i>07452 998
                                    238</a></li>
                            <li><a href="mailto:admin@locumkit.com"><i class="fa fa-envelope"
                                        aria-hidden="true"></i>admin@locumkit.com</a></li>
                        </ul>
                        <ul class="list-inline socialicon">
                            <!--<li><a href="https://www.facebook.com/locumkit" target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>-->
                            <!--<li><a href="https://www.linkedin.com/company/locumkit" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a></li>-->
                            <!--<li><a href="https://www.linkedin.com/company/locumkit" target="_blank"><i class="fa fa-google" aria-hidden="true"></i></a></li>-->
                            <!--<li><a href="https://www.linkedin.com/company/locumkit" target="_blank"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>-->
                            <!--<br>-->
                            <!--@foreach ($socialIcons as $identifier => $icon)
-->
                            <!--    @if (!empty($socialLinks[$identifier]))
-->
                            <!--        <li>-->
                            <!--            <a href="{{ $socialLinks[$identifier] }}" target="_blank">-->
                            <!--                <i class="fa {{ $icon }} p-2" aria-hidden="true"></i>-->
                            <!--            </a>-->
                            <!--        </li>-->
                            <!--
@endif-->
                            <!--
@endforeach-->
                            <span class="d-flex">
                                <a href="https://www.facebook.com/share/186z1L65Xg/" target="_blank">
                                    <span class="mx-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="#fff" class="bi bi-facebook" viewBox="0 0 16 16">
                                            <path
                                                d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                                        </svg>
                                    </span>
                                </a>
                                <a href="https://www.linkedin.com/company/locumkit/" target="_blank" <span
                                    class="mx-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="#fff" class="bi bi-linkedin" viewBox="0 0 16 16">
                                        <path
                                            d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z" />
                                    </svg>
                            </span>
                            </a>
                            <!--<span class="mx-3">-->
                            <!--    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">-->
                            <!--      <path d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z"/>-->
                            <!--    </svg> -->
                            <!--</span>-->
                            <!--<span class="mx-3">-->
                            <!--    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pinterest" viewBox="0 0 16 16">-->
                            <!--      <path d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0"/>-->
                            <!--    </svg> -->
                            <!--</span>-->
                            <!--<span class="mx-3">-->
                            <!--    <svg xmlns="http://www.w3.org/2000/svg" width="1" height="16" fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">-->
                            <!--      <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>-->
                            <!--    </svg> -->
                            <!--</span>-->
                            <!--<span class="mx-3">-->
                            <!--    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">-->
                            <!--      <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>-->
                            <!--    </svg>-->
                            <!--</span>-->
                            </span>



                        </ul>
                    </div>
                </div>
                <!--<ul class="list-inline socialicon">-->
                <!--    @foreach ($socialIcons as $identifier => $icon)
-->
                <!--        @if (!empty($socialLinks[$identifier]))
-->
                <!--            <li>-->
                <!--                <a href="{{ $socialLinks[$identifier] }}" target="_blank">-->
                <!--                    <i class="fa {{ $icon }}" aria-hidden="true"></i>-->
                <!--                </a>-->
                <!--            </li>-->
                <!--
@endif-->
                <!--
@endforeach-->
                <!--    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-twitter-x" viewBox="0 0 16 16">-->
                <!--      <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>-->
                <!--    </svg>-->
                <!--</ul>-->
            </div>
        </div>
        <div class="container-fluid copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <p>Copyright © {{ today()->format('Y') }} Locumkit - All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <script type="text/javascript" id="cookieinfo" src="/frontend/locumkit-template/new-design-assets/js/cookieinfo.min.js"
        data-bg="#ffffff" data-fg="#000000" data-link="#00a8dd" data-cookie="CookieInfoScript" data-text-align="left"
        data-close-text="I Agree"></script>

    <div id="alert-modal" class="alert-modal modal fade">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title">Locumkit</h4>
                </div>
                <div class="modal-body">
                    <h3 id="alert-message"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close-alert btn btn-default" data-dismiss="modal"
                        onclick="window.location.reload()">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-confirm-modal" class="alert-modal modal fade">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title">Locumkit</h4>
                </div>
                <div class="modal-body">
                    <h3 id="alert-message"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="confirm">Yes</button>
                    <button type="button" class="close-alert btn btn-default">No</button>
                </div>
            </div>
        </div>
    </div>

    <div id="manage-bank-income" class="modal fade financepopup" role="dialog">
        <div class="modal-dialog">
            <form action="" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h4 class="modal-title">Locumkit</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 pad0 financeform">
                            <div class="form-group" id="bank_date">
                                <div class="pull-left" style="display: none;">
                                    <input name="in_bank" id="modal-in_bank" value="1" type="hidden">
                                </div>
                                <div class="input-group" id="for-display" style="display: block;">
                                    <p>Please enter the date the transaction hit the bank</p>
                                    <input type="hidden" name="in_bankid" id="in_bankid">
                                    <input type="text"
                                        class="form-control financein_bankdate readonly hasDatepicker"
                                        name="in_bankdate" id="in_bankdate" placeholder="dd/mm/yyyy" required="">
                                    <button type="submit" class="btn btn-info" name="income-bank-btn"
                                        value="income-bank-btn" id="income-bank-btn">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="manage-bank-expense" class="modal fade financepopup" role="dialog">
        <div class="modal-dialog">
            <form action="" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h4 class="modal-title">Locumkit</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 pad0 financeform">
                            <div class="form-group" id="bank_date">
                                <div class="pull-left" style="display:none">
                                    <input name="ex_bank" id="modal-ex_bank" value="1" type="hidden">
                                </div>
                                <div class="input-group" id="for-displayex" style="display:block">
                                    <p>Please enter the date the transaction hit the bank</p>
                                    <input type="hidden" name="ex_bankid" id="ex_bankid">
                                    <input type="text"
                                        class="form-control financeex_bankdate readonly hasDatepicker"
                                        name="ex_bankdate" id="ex_bankdate" placeholder="dd/mm/yyyy" required="">
                                    <button type="submit" class="btn btn-info" id="expense-bank-btn"
                                        name="expense-bank-btn" value="expense-bank-btn">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <a href="javascript:" id="return-to-top" class="onHoverWave" style="display: inline;"><i
            class="fa fa-arrow-up"></i></a>

    <script src="/frontend/locumkit-template/new-design-assets/js/jquery-3.3.1.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
    <script src="/frontend/locumkit-template/new-design-assets/js/jquery-ui.js" type="text/javascript"></script>
    <script src="/frontend/locumkit-template/new-design-assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/frontend/locumkit-template/new-design-assets/js/animations.min.js" type="text/javascript"></script>
    <script src="/frontend/locumkit-template/new-design-assets/js/theme.js" type="text/javascript"></script>
    <script type="text/javascript" src="/frontend/locumkit-template/new-design-assets/js/jquery.dataTables.min.js"
        charset="UTF-8"></script>
    <script type="text/javascript">
        $(function() {
            $("#datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: "yy-mm-dd",
                yearRange: '1950:2000', // specifying a hard coded year range
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                }
            });
        });
    </script>

    <script type="text/jscript">
	    $("#loader-div").hide(100);
	</script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    {{-- Notification and error, success messages --}}
    @include('components.validation-notifications')

    @stack('scripts')

    <div id="ui-datepicker-div" class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
    </div>
</body>

</html>
