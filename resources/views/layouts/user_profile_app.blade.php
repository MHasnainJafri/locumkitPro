<!DOCTYPE html>
<html>

<head>
    <title>My Dashboard</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="description" content="My Dashboard" />
    <meta name="keywords" content="My Dashboard" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="/frontend/locumkit-template/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/frontend/locumkit-template/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="/frontend/locumkit-template/css/style.css" rel="stylesheet" type="text/css">
    <link href="/frontend/locumkit-template/css/responsive.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <style type="text/css" src="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css"></style>

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

    <style>
        .toastify {
            background-color: red !important;
        }

        .toastify.on {
            background-color: red !important;
        }
    </style>

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

    @stack('styles')

    <style>
        .d-none {
            display: none;
        }

        .d-block {
            display: block;
        }

        footer .recentpost li:before {
            content: "\F309" !important;
            font-family: fontawesome;
            color: #fff;
            font-size: 14px;
            position: absolute;
            left: 0;
        }
        svg {
            color: #696969;
        }
    </style>
</head>

<body>

    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('app.google_tag_manager_id') }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <div id="loader-div">
        <div class="loader"></div>
    </div>
    <header class="header-wrapper" id="header">
        <div class="container">
            <div class="row">
                <nav class="navbar navbar-default">

                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="/"><img src="/frontend/locumkit-template/img/logo.png"
                                alt="Locumkit" title="Locumkit"></a>
                    </div>

                    <div class="top-main-nav pull-right">
                        <div class="uploadinfo pull-right">
                            <ul>
                                @guest
                                    <li><a href="javascript:void(0);" title="Log In" alt="Log In" data-toggle="modal"
                                            data-target="#login-form-model">Log In</a></li>
                                    <li><a href="/register" title="Register" alt="Register">Register</a></li>
                                @else
                                    @can('is_employer')
                                        <li><a href="/employer/dashboard" title="My Profile" alt="My Profile">My Dashboard</a>
                                        </li>
                                    @endcan
                                    @can('is_freelancer')
                                        <li><a href="/freelancer/dashboard" title="My Profile" alt="My Profile">My Dashboard</a>
                                        </li>
                                    @endcan
                                    <li>
                                        <a href="javascript:void(0);" onclick="$('#logout-form').submit();" title="Logout"
                                            alt="Logout"><i class="fa fa-power-off" aria-hidden="true"></i></a>
                                    </li>
                                    <form style="display: none;" aria-hidden="true" action="/logout" id="logout-form"
                                        style="display: inline-block;" method="post" hidden>
                                        @csrf
                                    </form>
                                @endguest

                            </ul>
                        </div>
                        <div class="collapse navbar-collapse pull-right" id="bs-example-navbar-collapse-1">
                            <nav id="nav" style="margin-top:10px;">
                                <ul class="sf-menu navigation">
                                    <li>
                                        <a href="/">Home</a>
                                    </li>
                                    <li>
                                        <a href="/contact">Contact Us</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <section id="profile-banner"
        style="background:url('/media/files/33/245/5765322e4f7ba.jpg') no-repeat center center;-webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;background-attachment: fixed; width:100%;min-height: 350px; max-height:auto;display:none;">
        <div class="container">
            <div class="row">
                <div class="banner-header" align="center">
                    <div class="banner-text-bottom">
                        <div class="profile-banner-head">Discover Locumkit</div>
                        <p>If you need a doctor for to consectetuer Lorem ipsum dolor, consectetur adipiscing elit.
                            Utvolutpat eros adipiscing elit Ut volutpat. aliquam erat volutpat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @yield('content')

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
                        <!--<style type="text/css">-->
                        <!--    div#mc_embed_signup form {-->
                        <!--        padding: 0;-->
                        <!--    }-->

                        <!--    input#mc-embedded-subscribe {-->
                        <!--        margin: 0;-->
                        <!--        width: 100px !important;-->
                        <!--        text-transform: uppercase;-->
                        <!--        border-color: #25a8dd;-->
                        <!--        border-radius: 0;-->
                        <!--        float: left !important;-->
                        <!--        clear: none;-->
                        <!--    }-->

                        <!--    input#mce-EMAIL {-->
                        <!--        border-color: #fff;-->
                        <!--        border-radius: 0;-->
                        <!--        width: 66% !important;-->
                        <!--        float: left;-->
                        <!--    }-->

                        <!--    div#mce-responses {-->
                        <!--        padding: 0 !important;-->
                        <!--        margin: 0px !important;-->
                        <!--    }-->

                        <!--    div#mce-error-response {-->
                        <!--        margin: 0 !important;-->
                        <!--    }-->
                        <!--</style>-->
                        <!--@if (session('success'))-->
                        <!--    <div class="alert alert-success">-->
                        <!--        {{ session('success') }}-->
                        <!--    </div>-->
                        <!--@endif-->
                        <div id="mc_embed_signup">
                            <!--<form action="//fudugosolutions.us13.list-manage.com/subscribe/post?u=41b543e3133f958b3c58df8b5&amp;id=fb441ef5f1" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form"-->
                            <form action="{{ route('subscribed-news-letter') }}" method="post" class="validate"
                            novalidate="novalidate">
                            @csrf
                            <div id="mc_embed_signup_scroll" style="position: relative;">
                                <input type="email" value="" name="email" class="required email"
                                    id="mce-EMAIL" placeholder="Email Address" aria-required="true"
                                    style="padding-right: 100px;">
                                <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe"
                                    class="btn btn-default btn-1 lkbtn mt-0" style="padding: 0px 20px 0px 20px !important;">
                            </div>
                        </form>

                        <style type="text/css">
                            #mc_embed_signup_scroll {
                                position: relative;
                                width: 100%;
                                /* Adjust to your form width */
                            }

                            input#mce-EMAIL {
                                width: 100%;
                                padding: 10px;
                                padding-right: 110px;
                                /* Space for the button */
                                border: 1px solid #ddd;

                                font-size: 16px;
                            }

                            #mc-embedded-subscribe {
                                padding: 0px 20px 0px 20px !important;
                            }

                            input#mc-embedded-subscribe {
                                position: absolute;
                                top: 0;
                                right: 0;
                                height: 100%;
                                border: none;
                                background-color: #25a8dd;
                                color: white;
                                font-weight: bold;
                                text-transform: uppercase;
                                padding: 0px 20px 20px 0px !important;
                                cursor: pointer;
                                /* Match email input's border-radius */
                                font-size: 14px;

                            }

                            input#mc-embedded-subscribe:hover {
                                background-color: #1d8bc6;
                            }
                        </style>
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
    'tw' => 'fa-twitter-square', 
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
                        
                        <div>
                            
                              @foreach($socialIcons as $platform => $icon)
    @if(isset($socialLinks[$platform]))
    
    <a href="{{ $socialLinks[$platform] }}"><i class="fa  {{ $icon }} text-light ml-2" style="    color: white;" aria-hidden="true"></i></a></li>
     

    @endif
@endforeach
                            
                        </div>
                        
                        
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
                                <!--<a href="https://www.facebook.com/share/186z1L65Xg/" target="_blank">-->
                                <!--    <span class="mx-3">-->
                                <!--        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"-->
                                <!--            fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">-->
                                <!--            <path-->
                                <!--                d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />-->
                                <!--        </svg>-->
                                <!--    </span>-->
                                <!--</a>-->
                                <!--<a href="https://www.linkedin.com/company/locumkit/" target="_blank">-->
                                <!--    <span class="mx-3">-->
                                <!--        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"-->
                                <!--            fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">-->
                                <!--            <path-->
                                <!--                d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z" />-->
                                <!--        </svg>-->
                                <!--    </span>-->
                                <!-- </a>-->
                                <!--<span class="mx-3">-->
                                <!--    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"-->
                                <!--        fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">-->
                                <!--        <path-->
                                <!--            d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z" />-->
                                <!--    </svg>-->
                                <!--</span>-->
                                <!--<span class="mx-3">-->
                                <!--    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"-->
                                <!--        fill="currentColor" class="bi bi-pinterest" viewBox="0 0 16 16">-->
                                <!--        <path-->
                                <!--            d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0" />-->
                                <!--    </svg>-->
                                <!--</span>-->
                                <!--<span class="mx-3">-->
                                <!--    <svg xmlns="http://www.w3.org/2000/svg" width="1" height="16"-->
                                <!--        fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">-->
                                <!--        <path-->
                                <!--            d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />-->
                                <!--    </svg>-->
                                <!--</span>-->
                                <!--<span class="mx-3">-->
                                <!--    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"-->
                                <!--        fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">-->
                                <!--        <path-->
                                <!--            d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />-->
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

    <div id="alert-modal" class="alert-modal modal fade">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title">LocumKit</h4>
                </div>
                <div class="modal-body">
                    <h3 id="alert-message"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close-alert btn btn-default" data-dismiss="modal"
                        onClick="window.location.reload()">Ok</button>
                </div>
            </div>
        </div>
    </div>

    @guest
        <div id="login-form-model" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <div class="modal-content">
                    <div class="modal-header no-border-bottom">
                        <button type="button" class="close" data-dismiss="modal"
                            onclick="close_dive('profession_question');">&times;</button>
                        <h4 class="modal-title">Login Form</h4>
                    </div>
                    <div class="modal-body">
                        <form id="one-page-form" action="{{ route('login') }}" method="post" class="login-from"
                            class="login-form-pop">
                            @csrf
                            <fieldset class="has-warning">
                                <span class="input-glyphicon input-glyphicon-right block">
                                    <input name="login" type="text" class="form-control margin-bottom"
                                        placeholder="Enter username or email" autofocus required />
                                </span>
                                <span class="input-glyphicon input-glyphicon-right block">
                                    <input name="password" type="password" class="form-control margin-bottom"
                                        placeholder="Enter Password" required />
                                </span>
                                <div class="clearfix buttons">
                                    <button class="pull-left btn btn-small btn-warning">
                                        <i class="glyphicon glyphicon-log-in"></i>
                                        Log In </button>
                                    <a href="{{ route('password.request') }}" class="pull-right">Forgot Password?</a>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endguest

    <div id="alert-confirm-modal" class="alert-modal modal fade">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title">LocumKit</h4>
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

<style>
        #ui-datepicker-div {
            width: 420px;
        }
        .financeform .input-group button {
            width: 17%;
        }
        .financeform .input-group input {
            width: 80%;
        }
        
        .btn {
            padding: 8px 30px;
        }
    </style>
    <div id="manage-bank-income" class="modal fade financepopup" role="dialog">
        <div class="modal-dialog">
            <form action="/freelancer/income/update-bank-detail" method="post">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                                    <input type="date" class="form-control" name="in_bankdate" id="in_bankdate"
                                        required>
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
            <form action="/freelancer/expense/update-bank-detail" method="post">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Locumkit</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 pad0 financeform">
                            <div class="form-group" id="bank_date">
                                <div class="input-group" id="for-displayex" style="display:block">
                                    <p>Please enter the date the transaction hit the bank</p>
                                    <input type="hidden" name="ex_bankid" id="ex_bankid">
                                    <input type="date" class="form-control" name="ex_bankdate" required>
                                    <button type="submit" class="btn btn-info" id="expense-bank-btn">Submit</button>
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

    @if (Auth::check() && Auth::user()->user_acl_role_id == 2)
        <div id="locum-manage-financial-type" class="modal fade financepopup" role="dialog">
            <div class="modal-dialog">
                <form action="/freelancer/update-employment-status" method="post">
                    @csrf
                    <div class="modal-content first-model d-block" id="model-1">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"> Employment status </h4>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-12 pad0 financeform">
                                <div class="form-group" id="bank_date">
                                    <div class="col-md-5">Who are you?</div>
                                    <div class="col-md-7">
                                        <select name="user_finance_type" id="user_finance_type" class="form-control"
                                            onchange="limitedCompany()" required id="employmentStatus">
                                            <option value="">Select</option>
                                            <option value="soletrader">Sole trader</option>
                                            <option value="limitedcompany">Limited company</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: center" class="form-group">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-content second-model d-none" id="model-2">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"> Select End Month </h4>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-12 pad0 financeform">
                                <div class="form-group" id="bank_date">
                                    <div class="col-md-5">Select the Start Month</div>
                                    <div class="col-md-7">
                                        <select name="start_month" onchange="remove_disabled()" class="form-control">
                                            <option value="">Select</option>
                                            <option value="1">January</option>
                                            <option value="2">Febrary</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: center" class="form-group">
                                    <button type="button" id="backbutton" class="btn btn-primary"
                                        style="margin-right:2px !important;">Back</button>
                                    <button type="submit" id="savebtn"
                                        class="btn btn-primary disabled">Save</button>
                                </div>
                                <div style="display: flex; justify-content: center" class="form-group">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <a href="javascript:void(0);" class="scrollToTop"><i class="fa fa-chevron-circle-up" aria-hidden="true"></i></a>

    <script src="/frontend/locumkit-template/js/jquery-1.10.2.min.js"></script>
    <script src="/frontend/locumkit-template/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="/frontend/locumkit-template/js/jquery-ui.multidatespicker.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script type="text/javascript" src="/frontend/locumkit-template/js/jquery.dataTables.min.js" charset="UTF-8"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>

    <script>
        window.defaultDateFormat = `{{ get_web_default_date_format() }}`;
    </script>

    {{-- Scroll manager --}}
    <script>
        function limitedCompany() {
            var limitedCompany = document.getElementById('user_finance_type').value;
            if (limitedCompany === 'limitedcompany') {
                var element = document.getElementById("model-2");
                element.classList.remove("d-none");
                element.classList.add("d-block");

                var element2 = document.getElementById("model-1");
                element2.classList.remove("d-block");
                element2.classList.add("d-none");
            }
        }

        $('#backbutton').click(function goBack() {
            var element2 = document.getElementById("model-1");
            element2.classList.remove("d-none");
            element2.classList.add("d-block");

            var element = document.getElementById("model-2");
            element.classList.remove("d-block");
            element.classList.add("d-none");
        })
        $('#month_start').click(function remove_disabled() {})

        function remove_disabled() {
            var savebtn = document.getElementById("savebtn");
            savebtn.classList.remove("disabled");
        }
    </script>
    <script type="text/javascript">
        $(function() {
            var headerTop = $('#header').offset().top + 5;
            $(window).scroll(function() {
                if ($(window).scrollTop() > headerTop) {
                    $('#header').css({
                        position: 'fixed',
                        top: '0px'
                    });
                    $('#header').addClass('fixed-header-wrapper');
                    $('#header .navbar-default .navbar-brand img').css({
                        width: '70px',
                        transition: 'all 0.4s ease 0s'
                    });
                    $('#header .navbar-default .navbar-brand ').css({
                        padding: '5px 15px',
                        transition: 'all 0.4s ease 0s'
                    });
                    $('#header .navbar-default .top-main-nav').css({
                        padding: '15px 0 10px',
                        transition: 'all 0.4s ease 0s'
                    });
                } else {
                    $('#header').css({
                        position: 'static',
                        top: '0px'
                    });
                    $('#header').removeClass('fixed-header-wrapper');
                    $('#header .navbar-default .navbar-brand img').css({
                        width: '80px',
                        transition: 'all 0.4s ease 0s'
                    });
                    $('#header .navbar-default .navbar-brand ').css({
                        padding: '15px',
                        transition: 'all 0.4s ease 0s'
                    });
                    $('#header .navbar-default .top-main-nav').css({
                        padding: '30px 0 15px',
                        transition: 'all 0.4s ease 0s'
                    });
                }
            });
        });

        $(document).ready(function() {
            //Check to see if the window is top if not then display button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('.scrollToTop').fadeIn();
                } else {
                    $('.scrollToTop').fadeOut();
                }
            });

            //Click event to scroll to top
            $('.scrollToTop').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });

        });
    </script>

    {{-- Model and bank manager --}}
    <script type="text/javascript">
        /*use for manage bank status*/
        $('input#modal-in_bank').change(function() {
            var c = this.checked ? '1' : '0';
            if (c == 1) {
                $('#fordisplay').show();
            } else {
                $('#fordisplay').hide();
            }
        });

        $('input#modal-ex_bank').change(function() {
            var c = this.checked ? '1' : '0';
            if (c == 1) {

                $('#fordisplayex').show();
            } else {
                $('#fordisplayex').hide();
            }
        });

        $(document).ready(function() {
            $('input.financein_bankdate').datepicker({
                maxDate: '0',
                dateFormat: window.defaultDateFormat
            });
        });
        $(document).ready(function() {
            $('input.financeex_bankdate').datepicker({
                maxDate: '0',
                dateFormat: window.defaultDateFormat
            });
        });

        function managebankincome(id) {
            $('#fordisplay').hide();
            $('#in_bankdate').val('');
            $('#modal-in_bank').attr('checked', false);
            $('#in_bankid').val(id);
            $('#manage-bank-income').modal('show');
        }

        function managebankexpanse(id) {
            $('#fordisplayex').hide();
            $('#ex_bankdate').val('');
            $('#modal-ex_bank').attr('checked', false);
            $('#ex_bankid').val(id);
            $('#manage-bank-expense').modal('show');
        }
        /*use for manage bank status end*/

        $('div.alert-modal button.close-alert').click(function() {
            messageBoxClose();
        });
        $('div.alert-modal button.close.hide-pop-up').click(function() {
            messageBoxClose();
        });

        function messageBoxClose() {
            $('div#alert-modal').removeClass('in');
            $('div#alert-modal').css('display', 'none');
            $('div#alert-confirm-modal').removeClass('in');
            $('div#alert-confirm-modal').css('display', 'none');
        }

        function messageBoxOpen(msg, url) {
            $('div#alert-modal #alert-message').html(msg);
            $('div#alert-modal').addClass('in');
            $('div#alert-modal').css('display', 'block');
            $('button.close-alert').attr('onClick', 'window.location.reload()');
            if (url != null) {
                $('button.close-alert').attr('onClick', 'window.location.replace("' + url + '")');
            }
            if (url == 'not-reload') {
                $('button.close-alert').removeAttr("onClick");
                $('button.close-alert').removeAttr("onclick");
            }
        }

        $("#loader-div").hide(100);
    </script>

    @if (Auth::check() && Auth::user()->user_acl_role_id == 2 && Auth::user()->financial_year()->count() == 0)
        <div class="container-fluid copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <p>Copyright © {{ today()->format('Y') }} Locumkit - All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $("#locum-manage-financial-type").modal("show");
            })
        </script>
    @endif

    {{-- Notification and error, success messages --}}
    @include('components.validation-notifications')

    @stack('scripts')
</body>

</html>
