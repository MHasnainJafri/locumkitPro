@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/{{ $user_type ?? '' }}/dashboard">My Dashboard</a></li>
                        <li><a href="javascript:void(0)">Feedback Dispute Form</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3> Feedback Dispute Form </h3>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <div class="welcome-heading">
                        <h1>Dispute feedback</h1>
                        <hr class="shadow-line">
                    </div>
                    <div class="feedback-form">
                        <form id="dispute-form" method="POST">
                            @csrf
                            <input type="hidden" name="feedback_id" value="{{ $feedback->id }}">
                            <div class="form-group">
                                <label class="required control-label col-lg-2" for="feedback_from">Feedback By</label>
                                <div class="col-lg-10">
                                    <p class="form-control" readonly style="text-transform: capitalize;">{{ $feedback_from }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="required control-label col-lg-2" for="average_rate">Average Rate</label>
                                <div class="col-lg-10">
                                    <div id="stars-rating col-lg-5" class="user-rating" style="width: 130px;  float: left;  padding: 6px 0;   text-align: left;">
                                        {!! render_feedback_stars($feedback->rating) !!}
                                    </div>
                                    <div id="stars-rating col-lg-5" style="width: 130px;  float: left;  padding: 8px 0; text-align: left;">
                                        <a href="javascript:void(0)" id="show-details-feedback">Show Details</a>
                                    </div>
                                    <div id="details-feedback" style="display:none;">
                                        @if (json_decode($feedback->feedback, true))
                                            @foreach (json_decode($feedback->feedback, true) as $feedbackQuestion)
                                                <div class="feedback-qus-ans">
                                                    <p class="qus"><span>Qus. {{ $loop->iteration }} </span> {{ $feedbackQuestion['qus'] }} </p>
                                                    <div class="user-rating">
                                                        {!! render_feedback_stars($feedbackQuestion['qusRate']) !!}
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="dispute-comment">
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="dispute_comment">Dispute Comment</label>
                                    <div class="col-lg-10">
                                        <textarea class="form-control" id="dispute-comment" placeholder="Enter comment here...." name="dispute-comment"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="dispute-submit">
                                <a href="javascript:void(0);" id="dispute-submit" class="read-common-btn">Submit</a>
                            </div>
                            <h3 id="dispute-notice-msg"></h3>
                            <div class="lodader" style="display:none">
                                <img src="{{ asset('/frontend/locumkit-template/img/loader.gif') }}"><span>Please wait...</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById("dispute-submit").addEventListener('click',() => {
            document.getElementById("dispute-form").submit()
        } ) 
    </script>
    <script type="text/javascript">
        jQuery("a#show-details-feedback").click(function() {
            jQuery("#details-feedback").toggle(1000);
        });
    </script>
@endsection
