@extends('layouts.app')

@section('content')
    <section class="innerhead">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 bannercont animate zoomIn text-center" data-anim-type="zoomIn" data-anim-delay="800">

                        <div class="center">
                            <h1>SiteMap</h1>
                        </div>
                        <div class="breadcrum-sitemap">
                            <ul>
                                <li><a href="/">Home</a></li>
                                <li><a href="javascript:void(0);">SiteMap</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="innerlayout">
        <div class="container">
            <div class="row bgwhite inbox terms-page">
                <div id="primary-content" class="main-content termscondition">
                    <div class="container">
                        <div class="row">
                            <div class="white-bg SiteMap contents">
                                <section class="terms_para">

                                    <div class="terms_contents animate fadeInUp" style="display:flex !important; justify-content:space-between !important;">
                                        <p><a href="/">Home</a></p>
                                        <p><a href="/register">Register</a></p>
                                        <p><a href="/about">About Us</a></p>
                                        <p><a href="/contact">Contact Us</a></p>
                                        <p><a href="/term-condition">Terms of use</a></p>
                                        <p><a href="/privacy-policy">Privacy Policy</a></p>
                                        <p><a href="/blogs/recent-posts">Recent Posts</a></p>
                                        <p><a href="/package/">Packages</a></p>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
