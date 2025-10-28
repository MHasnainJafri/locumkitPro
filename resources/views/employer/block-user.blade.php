@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/employer/dashboard">My Dashboard</a></li>
                        <li><a href="javascript:void(0);">Block Locum</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <section>
                        <div class="welcome-heading">
                            <h1><span>Block Locum</span></h1>
                            <hr class="shadow-line">
                        </div>
                        By clicking the below button you can block the current locum.
                        <div id="block-freelancer-confirm">
                            <form action="/employer/block-user/{{ $freelancer->id }}" method="post">
                                @csrf
                                <button type="submit" class="read-common-btn">Block Locum</button>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
