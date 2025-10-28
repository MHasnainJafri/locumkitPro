@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="javascript:void(0);">Blog</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon" style="   padding: 3px 16px 0px;">
                        <i class="fa fa-rss" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Blog</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="subpage" id="main-wrapper">
        <div class="container">
            <div class="row">
                <div class="9u skel-cell-mainContent">
                    @foreach ($blogs as $blog)
                        <article class="first post-content">
                            <div class="col-md-3 f-img margin-top">
                                <img src="{{ asset('storage/' . $blog->image_path) }}" alt="{{ $blog->title }}" width="100%" height="180px">
                            </div>
                            <div class="col-md-9">
                                <h2 class="title"><a href="/blog/{{ $blog->slug }}"> {{ $blog->title }} </a></h2>
                                <p class="sub"><a href="javascript:void(0);">Recent Posts
                                        <i class="fa fa-rss" aria-hidden="true"></i> </a> &nbsp;
                                    <i class="glyphicon glyphicon-calendar" aria-hidden="true"></i> {{ $blog->created_at->format('d-m-Y') }}
                                </p>
                                {!! get_cleaned_html_content($blog->description, 50) !!}<a href="/blog/{{ $blog->slug }}" class="button right bg-info">Read More...</a>
                                <p class="clearfix"></p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="row mt-4 mb-4" style="display: flex; justify-content: center;">
                {{ $blogs->links() }}
            </div>
        </div>
    </div>
@endsection
