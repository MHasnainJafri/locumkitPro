@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="{{ $dashboard_url }}">My Dashboard</a></li>
                        <li><a href="javascript:void(0)">Feedback Form</a></li>
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
                        <h3> {{ $page_title }} </h3>
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
                        <h1>Leave your feedback</h1>
                        <hr class="shadow-line">
                    </div>
                    <div class="feedback-form">
                        @if ($alreadyFeedbackCount > 0)
                            <h3 id='feedback-msg'><span style='color:#EBA34D'> Feedback already submitted.</span></h3>
                        @else
                            <form method="post" id="feedback-form" onsubmit="return submitfeedback();" action="/post-feedback">
                                @csrf
                                <input type="hidden" id="employer_id" name="employer_id" value="{{ $job_on_day_count->employer_id }}">
                                <input type="hidden" id="freelancer_id" name="freelancer_id" value="{{ $job_on_day_count->freelancer_id }}">
                                <input type="hidden" id="job_id" name="job_id" value="{{ $job->id }}">
                                <input type="hidden" id="to_user_id" name="to_user_id" value="{{ $job_to_user->id }}">
                                <input type="hidden" name="user_type" value="{{ $user_type }}">
                                <input type="hidden" name="cat_id" value="{{ Auth::user()->user_acl_profession_id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" name="feedback_name" class="form-control margin-bottom feedback-name" placeholder="Full name" value="{{ $job_to_user->firstname . ' ' . $job_to_user->lastname }}" readonly>
                                        <span class="feedback-name-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="hidden" name="feedback_email" class="form-control margin-bottom feedback-email" placeholder="E-mail" value="{{ $job_to_user->email }}" readonly>
                                        <input type="text" name="feedback_jobdate" class="form-control margin-bottom feedback-email" placeholder="Job Date" value="{{ get_date_with_default_format($job->job_date) }}" readonly>
                                        <span class="feedback-email-icon"><i class="fa fa-at" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="feedback_Jobid" class="form-control margin-bottom feedback-name" placeholder="Job Id" value="{{ $job->id }}" readonly>
                                        <span class="feedback-email-icon"><i class="fa fa-slack" aria-hidden="true"></i></span>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="feedback_Jobrate" class="form-control margin-bottom feedback-name" placeholder="Job Rate" value="{{ set_amount_format($job->job_rate) }}" readonly>
                                        <span class="feedback-email-icon"><i class="fa fa-gbp" aria-hidden="true"></i></span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="feedback-page-info">
                                            <h4>Star rating indicates</h4>
                                            <ul><img src="{{ asset('/frontend/locumkit-template/img/help.png') }}" alt="feedback"></ul>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Feedback Questions</h4>
                                    </div>
                                    @foreach ($allFeedbackQusArray as $feedbackQus)
                                        <div class="col-md-12 feedback-question-div">
                                            <div class="feedback-question" id="qus-{{ $loop->iteration }}" onclick="showQus('q-{{ $loop->iteration }}')">
                                                <a href="javascript:void(0);" class="{{ $loop->iteration == 1 ? 'active' : '' }}">
                                                    {{ $feedbackQus['question_' . $user_type] }}
                                                    <span class="pull-right">
                                                        @if ($loop->iteration == 1)
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        @else
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        @endif
                                                    </span>
                                                </a>
                                            </div>
                                            <div id="q-{{ $loop->iteration }}" class="collapse" style="{{ $loop->iteration == 1 ? 'display:block;' : '' }}">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="col-md-4">Your Answer:</div>
                                                        <div class="col-md-8">
                                                            <div class="rating pull-left" id="note-rate-{{ $loop->iteration }}">
                                                                <span><input type="radio" value="5"><label for="str5"></label></span>
                                                                <span><input type="radio" value="4"><label for="str4"></label></span>
                                                                <span><input type="radio" value="3"><label for="str3"></label></span>
                                                                <span><input type="radio" value="2"><label for="str2"></label></span>
                                                                <span><input type="radio" value="1"><label for="str1"></label></span>
                                                                <input type="hidden" name="ratevalue[]" id="rate-val-{{ $loop->iteration }}">
                                                                <input type="hidden" name="fdqus[]" id="fd-qus-emp-{{ $loop->iteration }}" value="{{ $feedbackQus['question_' . $user_type] }}">
                                                                <input type="hidden" name="fdqusid[]" id="fd-qus-id-{{ $loop->iteration }}" value="{{ $feedbackQus->id }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="col-md-12 comment-box">
                                        <textarea name="comment" placeholder="Please type your comment here..." id="feed_comment" minlength="5" maxlength="1000" required></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <h3>Total Rating summery</h3>
                                    </div>
                                    <div class="col-md-8 total-rating">
                                        <div class="progress">
                                            <div id="feedback-progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 term-cond">
                                        <input type="checkbox" name="t_and_c" required="required" checked="checked"><label>I agree that the information provided is accurate and a true representative of my experience. </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 submit-feedback">
                                        <input type="hidden" name="total-rating" id="total-rates">
                                        <button type="submit" class="submit-feedback-btn">Submit</button>
                                    </div>
                                </div>

                            </form>
                            <h3 id="feedback-msg"></h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("div[id*='note-rate-'] input:radio").click(function() {
                var id = $(this).parent().parent().attr('id');
                var qusnum = id.replace('note-rate-', ' ').trim();
                $("#" + id + " input:radio").attr("checked", true);
                $("#" + id + " span").removeClass('checked');
                $(this).parent().addClass('checked');
                $("#rate-val-" + qusnum).val($(this).val());
                var values = [];
                var coutQus = $("input[name='ratevalue[]']").length;
                $("input[name='ratevalue[]']").each(function() {
                    values.push(isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val()));
                });
                var totalRateValues = 0;
                $.each(values, function() {
                    totalRateValues += this;
                });
                var perOfRating = (totalRateValues / (coutQus * 5)) * 100;
                $("#feedback-progress-bar").attr("aria-valuenow", perOfRating);
                var pWidth = Math.round(perOfRating) + "%";
                $("#feedback-progress-bar").css("width", pWidth);
                $("#feedback-progress-bar").html('<div id="percentRating">' + perOfRating.toFixed(2) + "%</div>");
                $("#total-rates").val((totalRateValues / (coutQus)).toFixed(1));

            });

            $("div[id*='qus-']").click(function() {
                var id = $(this).attr('id');
                if (!$("#" + id + " a").hasClass("active")) {
                    $("#" + id + " a").addClass("active");
                    $('#' + id + ' a span i').addClass("fa-minus");
                    $('#' + id + ' a span i').removeClass("fa-plus");
                } else {
                    $("#" + id + " a").removeClass("active");
                    $('#' + id + ' a span i').removeClass("fa-minus");
                    $('#' + id + ' a span i').addClass("fa-plus");
                }

            });
        });

        function showQus(qusCollaps) {
            $('#' + qusCollaps).toggle(500);
        }

        function submitfeedback() {
            var name = $(".feedback-name").val();
            var email = $(".feedback-email").val();
            var uId = $("#user_id").val();
            var touserid = $("#to_user_id").val();
            var jId = $("#job_id").val();
            var comment = $("#feed_comment").val();
            var user_role = $("#user_role").val();
            var user_cat = $("#user_cat").val();
            var rating = $("#total-rates").val();
            var feedback = '';
            var feedbackQus = '';
            var feedbackQuestionId = '';
            var feedbackvalues = [];
            var feedbackQusId = [];
            var feedbackQus = [];
            $("input[name='ratevalue[]']").each(function() {
                feedbackvalues.push(isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val()));
            });
            feedback = feedbackvalues.toString();
            $("input[name='fdqusid[]']").each(function() {
                feedbackQusId.push(isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val()));
            });
            feedbackQuestionId = feedbackQusId.toString();
            $("input[name='fdqus[]']").each(function() {
                feedbackQus.push($(this).val());
            });
            feedbackQuestion = feedbackQus.toString();


            if (isValidate() != 0) {
                return true;
            } else {
                $("#feedback-msg").html('<span style="color:red">Ohhh..! look like you miss somthing please check & try again.</span>');
                return false;
            }

        }

        function isValidate() {
            var valid = 1;
            $("input[name='ratevalue[]']").each(function() {
                if ($(this).val() == '') {
                    valid = 0;
                }
            });
            if ($("input[name='t_and_c']").prop("checked") == false && valid == 1) {
                valid = 0;
            }

            return valid;
        }
    </script>
@endpush
