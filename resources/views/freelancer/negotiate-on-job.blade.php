@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        @auth
                            <li><a href="/freelancer/dashboard">Dashbord</a></li>
                            <li><a href="/freelancer/job-listing">Job List</a></li>
                        @endauth
                        <li><a href="javascript:void(0);">Negotiate On Job</a></li>
                    </ul>

                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon" style="padding: 8px 13px 0px;">
                        <i class="glyphicon glyphicon-briefcase" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Negotiate On Job Rate </h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row container">
                <div class="white-bg col-md-6 contents">
                    <form id="negotiate-on-job" action="/negotiate/freelancer-negotiate-on-job/{{ $job->id }}" method="post">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group" style="display: flex; flex-direction: column;">
                                <label style="text-align: start;" for="rate">Enter expected rate</label>
                                <input type="number" name="rate" id="rate" min="{{ $job->job_rate }}" required>
                            </div>
                            <div class="form-group" style="display: flex; flex-direction: column;">
                                <label style="text-align: start;" for="rate">Enter message for employer</label>
                                <textarea name="message" id="message" minlength="10" cols="30" rows="10" required></textarea>
                            </div>

                            <p class="note" style="font-style: italic;">Job will automatically accepted if employer accepted your offer!</p>

                            <div class="form-group d-flex justify-content-center">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
