@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\UserFeedbackController')

    <div class="main-container container">
        @include('admin.layout.sidebar')
        <div class="col-lg-12 main-content">
            <div id="breadcrumbs" class="breadcrumbs">
                <div id="menu-toggler-container" class="hidden-lg">
                    <span id="menu-toggler">
                        <i class="glyphicon glyphicon-new-window"></i>
                        <span class="menu-toggler-text">Menu</span>
                    </span>
                </div>
                <ul class="breadcrumb">
                    <li>
                        <i class="glyphicon glyphicon-home home-icon"></i>
                        <a href="/admin">Dashboard</a>
                    </li>
                    <li>
                        <a href="/admin/config">Feedback Management</a>
                    </li>
                    <li class="active">
                        User Feedback</li>
                    <li class="active"> edit </li>
                </ul>
            </div>
        </div>
        <div class="page-content" style="margin-top: -10px">
            <form class="relative form-horizontal" action="{{route('admin.userfeedback.update', $userFeedback->jobfeedback->id)}}" method="post"
                enctype="application/x-www-form-urlencoded">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_type" value="2">
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="feedback_from">Feedback From</label>
                        <div class="col-lg-10">
                            <p class="form-control" readonly="" style="text-transform: capitalize;">
                                {{ $userFeedback['user_type'] == 'employer' ? $userFeedback->jobFeedback->employer->firstname : $userFeedback->jobFeedback->freelancer->firstname }}
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="feedback_to">Feedback To</label>
                        <div class="col-lg-10">
                            <p class="form-control" readonly="" style="text-transform: capitalize;">
                                {{ $userFeedback['user_type'] == 'employer' ?$userFeedback->jobFeedback->freelancer->firstname : $userFeedback->jobFeedback->employer->firstname }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="feedback_status">Feedback Status</label>
                        <div class="col-lg-10">
                            <select name="status" class="form-control" id="fd_qus_status">
                                <option value="1" selected="">Approved</option>
                                <option value="2">Dispute Pending</option>
                                <option value="3">Dispute Approved</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="average_rate">Average Rate</label>
                        <div class="col-lg-10">
                            <div id="stars-rating col-lg-5" class="user-rating"
                                style="width: 130px;  float: left;  padding: 6px 0;">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $userFeedback->rating)
                                        <span class="glyphicon glyphicon-star"></span>
                                    @else
                                        <span class="glyphicon glyphicon-star-empty"></span>
                                    @endif
                                @endfor
                            </div>
                            <div id="stars-rating col-lg-5" style="width: 130px;  float: left;  padding: 8px 0;">
                                <a href="javascript:void(0)" id="show-details-feedback">Show Details</a>
                            </div>
                            <div id="details-feedback" style="display:block;">
                                @foreach (json_decode($userFeedback->jobfeedback->feedback) as $item)
                                    <div class="feedback-qus-ans">
                                        <p class="qus"><span>Qus. {{ $loop->iteration }})</span>{{ $item->qus }}</p>
                                        <div class="user-rating"></div>
                                        <div style="clear:both">
                                            <div class="rating pull-left" id="note-rate-{{ $loop->iteration }}">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <span>
                                                        <input type="radio" name="rating{{ $loop->iteration }}"
                                                            value="{{ $i }}"
                                                            @if ($i == $item->qusRate) checked @endif>
                                                        <label for="str{{ $i }}"></label>
                                                    </span>
                                                @endfor
                                                <input type="hidden" name="ratevalue[]"
                                                    id="rate-val-{{ $loop->iteration }}" value="{{ $item->qusRate }}">
                                                <input type="hidden" name="fdqus[]" id="fd-qus-emp-{{ $loop->iteration }}"
                                                    value="{{ $item->qus }}">
                                                <input type="hidden" name="fdqusid[]"
                                                    id="fd-qus-id-{{ $loop->iteration }}" value="{{ $item->qusId }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <input type="hidden" name="total-rates" id="total-rates" value="3">
                                <div class="feedback-comment">
                                    <label class="required control-label " for="feedback_comment">Feedback Comment</label>
                                    <div class="f-comment">
                                        <p class="form-control" readonly="">Hi, Nice client to work with</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">

                    <style type="text/css">
                        div#details-feedback {
                            clear: both;
                            padding: 10px;
                            border: 1px solid #ccc;
                            border-radius: 3px;
                            float: left;
                            width: 100%;
                        }

                        div#details-feedback p {
                            font-weight: bold;
                        }

                        .user-rating {
                            font-size: 16px;
                            color: #df7900;
                        }

                        .feedback-comment {
                            border-top: 1px solid #ccc;
                            margin-top: 15px;
                            display: none;
                        }

                        .feedback-comment.dispute {
                            display: block;
                        }

                        .feedback-comment label.required.control-label {
                            margin: 10px 0;
                        }

                        .f-comment p {
                            font-weight: bold;
                            height: auto;
                        }

                        .feedback-qus-ans {
                            border-bottom: 1px solid #cfcfcf;
                            padding: 10px;
                            margin-bottom: 10px;
                            background: #fff;
                            border-radius: 3px;
                            float: left;
                            width: 100%;
                        }

                        .feedback-qus-ans:last-child {
                            border-bottom: 0px solid #cfcfcf;
                        }

                        p.qus {
                            font-style: italic;
                        }

                        p.qus span {
                            padding-right: 8px;
                        }

                        /* Rating selected star */
                        .rating span {
                            float: right;
                            position: relative;
                        }

                        .rating span input {
                            position: absolute;
                            top: 0px;
                            left: 0px;
                            opacity: 0;
                            width: 30px;
                            height: 25px;
                            cursor: pointer;
                        }

                        .rating span label {
                            display: inline-block;
                            width: 30px;
                            height: 30px;
                            text-align: center;
                            color: #FFF;
                            background: url('http://lucmkit-php.local/public/frontend/locumkit-template/css/img/rating.png');
                            font-size: 30px;
                            margin-right: 2px;
                            line-height: 30px;
                            border-radius: 50%;
                            -webkit-border-radius: 50%;
                            cursor: pointer;
                        }

                        .rating span:hover~span label,
                        .rating span:hover label,
                        .rating span.checked label,
                        .rating span.checked~span label {
                            background: url('http://lucmkit-php.local/public/frontend/locumkit-template/css/img/rating.png');
                            background-position: 30px 28px;
                            color: #FFF;
                        }

                        span.feedback-name-icon i,
                        span.feedback-email-icon i {
                            width: 36px;
                            position: absolute;
                            left: 16px;
                            top: 1px;
                            color: #00A9E0;
                            font-size: 20px;
                            border-right: 2px solid;
                            padding: 5px 10px 7px 10px;
                            background: #ECECEC;
                            border-top-left-radius: 5px;
                            border-bottom-left-radius: 5px;
                        }

                        input.form-control.margin-bottom.feedback-name,
                        input.form-control.margin-bottom.feedback-email {
                            padding-left: 45px;
                        }
                    </style>
                    <script type="text/javascript">
                        jQuery("a#show-details-feedback").click(function() {
                            jQuery("#details-feedback").toggle(1000);
                        });





                        jQuery(document).ready(function() {
                            jQuery("div[id*='note-rate-'] input:radio").click(function() {
                                var id = jQuery(this).parent().parent().attr('id');
                                var qusnum = id.replace('note-rate-', ' ').trim();
                                jQuery("#" + id + " input:radio").attr("checked", true);
                                jQuery("#" + id + " span").removeClass('checked');
                                jQuery(this).parent().addClass('checked');
                                jQuery("#rate-val-" + qusnum).val(jQuery(this).val());
                                var values = [];
                                var coutQus = jQuery("input[name='ratevalue[]']").length;
                                jQuery("input[name='ratevalue[]']").each(function() {
                                    values.push(isNaN(parseInt(jQuery(this).val())) ? 0 : parseInt(jQuery(this)
                                        .val()));
                                });
                                var totalRateValues = 0;
                                jQuery.each(values, function() {
                                    totalRateValues += this;
                                });
                                var perOfRating = (totalRateValues / (coutQus * 5)) * 100;
                                jQuery("#feedback-progress-bar").attr("aria-valuenow", perOfRating);
                                var pWidth = Math.round(perOfRating) + "%";
                                jQuery("#feedback-progress-bar").css("width", pWidth);
                                jQuery("#feedback-progress-bar").html('<div id="percentRating">' + perOfRating + "%</div>");
                                jQuery("#total-rates").val((totalRateValues / (coutQus)).toFixed(1));

                            });
                            //});

                            jQuery("div[id*='qus-']").click(function() {
                                var id = jQuery(this).attr('id');
                                if (!jQuery("#" + id + " a").hasClass("active")) {
                                    jQuery("#" + id + " a").addClass("active");
                                    jQuery('#' + id + ' a span i').addClass("fa-minus");
                                    jQuery('#' + id + ' a span i').removeClass("fa-plus");
                                } else {
                                    jQuery("#" + id + " a").removeClass("active");
                                    jQuery('#' + id + ' a span i').removeClass("fa-minus");
                                    jQuery('#' + id + ' a span i').addClass("fa-plus");
                                }

                            });
                        });

                        function isValidate() {
                            var valid = 1;
                            jQuery("input[name='ratevalue[]']").each(function() {
                                if (jQuery(this).val() == '') {
                                    valid = 0;
                                }
                            });
                            if (jQuery("input[name='t_and_c']").prop("checked") == false && valid == 1) {
                                valid = 0;
                            }

                            return valid;
                        }
                    </script>
            </form>
        </div>
    </div>
@endsection
