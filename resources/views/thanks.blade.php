@extends('layouts.app')

@section('content')
    <section class="innerhead">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 bannercont animate zoomIn text-center" data-anim-type="zoomIn" data-anim-delay="300">
                        <div class="center">
                            <h1>Thank You</h1>
                        </div>
                        <div class="breadcrum-sitemap">
                            <ul>
                                <li><a href="/">Home</a></li>
                                <li><a href="javascript:void(0);">Login</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg thank-you-page contents">
                    <section>
                        <p style="text-align:center;">
                            <img src="/frontend/locumkit-template/img/logo.png" alt="logo">
                        </p>
                        <h4 class="feedback_msg"> {!! $msg !!} </h4>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        localStorage.clear();
    </script>
@endpush
