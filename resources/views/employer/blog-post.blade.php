@extends('layouts.user_profile_app')
@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/blogs">Blog</a></li>
                        <li><a href="javascript:void(0);">{{ $title }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon" style="padding: 3px 16px 0px;">
                        <i class="fa fa-rss" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>{{ $title }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="subpage" id="main-wrapper">
        <div class="container">
            <div class="row">
                <div class="9u skel-cell-mainContent">
                    <article class="first post-content">
                        <div class="col-md-12 margin-top">
                            <div class="post-feature-img margin-top"><img src="{{ $image_path }}" alt="{{ $title }}" width="200px">
                            </div>
                            <h2 class="title">{{ $title }}</h2>
                            <p class="sub">
                                <i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
                                {{ $date }}
                            </p>
                            <div class="hr dotted clearfix">&nbsp;</div>

                            <div class="blog-content-area">
                                {!! $content !!}
                            </div>

                        </div>
                    </article>
                    <div class="hr clearfix">&nbsp;</div>
                    <div class="hr clearfix">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
@endsection
