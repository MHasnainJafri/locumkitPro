@extends('admin.layout.app')
@section('content')
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
                            <a href="/admin/dashboard">Dashboard</a>
                        </li>
                        <li class="active">
                            Feedback 
                        </li>
                    </ul>
                </div>


        <div class="page-content">
            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            <form class="relative form-horizontal" action="{{ route('Feedback.update', $feedback->id) }}" method="post"
                enctype="application/x-www-form-urlencoded">
                @csrf
                <input type="hidden" name="user_type" value="2">
                <div class="form-group">
                    <label class="required control-label col-lg-2" for="feedback_from">Feedback From</label>

                    <div class="col-lg-10">
                        <p class="form-control" readonly style="text-transform: capitalize;">
                            {{ $feedback->employer->firstname }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="required control-label col-lg-2" for="feedback_to">Feedback To</label>
                    <div class="col-lg-10">
                        <p class="form-control" readonly style="text-transform: capitalize;">
                            {{ $feedback->freelancer->firstname }}</p>
                    </div>
                </div>



                <div class="form-group">
                    <label class="required control-label col-lg-2 mb-5" for="average_rate">Average Rate</label>
                    <div class="col-lg-10">
                        <div id="stars-rating col-lg-5" class="user-rating" style="width: 130px;  float: left;  padding: 6px 0;">
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star-empty"></span>
                        </div>
                        <div id="stars-rating col-lg-5" style="width: 130px;  float: left;  padding: 4px 0;">
                            <a href="javascript:void(0)" id="show-details-feedback">Show Details</a>
                        </div>
                        <div id="details-feedback" style="display:none;">
                            <div class="feedback-qus-ans">
                                <p class="qus"><span>Qus. 1</span>Was the store/equipment as described in the job advert?</p>
                                <div class="user-rating">

                                </div>
                                <div style="clear:both">
                                    <div class="rating pull-left" id="note-rate-1">
                                        <span><input type="radio" name="rating" value="5"><label
                                                for="str5"></label></span>
                                        <span class="checked"><input type="radio" name="rating" value="4"
                                                checked="checked"><label for="str4"></label></span>
                                        <span><input type="radio" name="rating" value="3"><label
                                                for="str3"></label></span>
                                        <span><input type="radio" name="rating" value="2"><label
                                                for="str2"></label></span>
                                        <span><input type="radio" name="rating" value="1"><label
                                                for="str1"></label></span>
                                        <input type="hidden" name="ratevalue[]" id="rate-val-1" value="4">
                                        <input type="hidden" name="fdqus[]" id="fd-qus-emp-1"
                                            value="Was the store/equipment as described in the job advert?">
                                        <input type="hidden" name="fdqusid[]" id="fd-qus-id-1" value="11">
                                    </div>
                                </div>
                            </div>
                            <div class="feedback-qus-ans">
                                <p class="qus"><span>Qus. 2)</span>How would you rate the employer's professionalism?</p>
                                <div class="user-rating">

                                </div>
                                <div style="clear:both">
                                    <div class="rating pull-left" id="note-rate-2">
                                        <span class="checked"><input type="radio" name="rating" value="5"
                                                checked="checked"><label for="str5"></label></span>
                                        <span><input type="radio" name="rating" value="4"><label
                                                for="str4"></label></span>
                                        <span><input type="radio" name="rating" value="3"><label
                                                for="str3"></label></span>
                                        <span><input type="radio" name="rating" value="2"><label
                                                for="str2"></label></span>
                                        <span><input type="radio" name="rating" value="1"><label
                                                for="str1"></label></span>
                                        <input type="hidden" name="ratevalue[]" id="rate-val-2" value="5">
                                        <input type="hidden" name="fdqus[]" id="fd-qus-emp-2"
                                            value="How would you rate the employer's professionalism?">
                                        <input type="hidden" name="fdqusid[]" id="fd-qus-id-2" value="20">
                                    </div>
                                </div>
                            </div>
                            <div class="feedback-qus-ans">
                                <p class="qus"><span>Qus. 3)</span>How satisfied were you with the the team and working
                                    environment?</p>
                                <div class="user-rating">

                                </div>
                                <div style="clear:both">
                                    <div class="rating pull-left" id="note-rate-3">
                                        <span class="checked"><input type="radio" name="rating" value="5"
                                                checked="checked"><label for="str5"></label></span>
                                        <span><input type="radio" name="rating" value="4"><label
                                                for="str4"></label></span>
                                        <span><input type="radio" name="rating" value="3"><label
                                                for="str3"></label></span>
                                        <span><input type="radio" name="rating" value="2"><label
                                                for="str2"></label></span>
                                        <span><input type="radio" name="rating" value="1"><label
                                                for="str1"></label></span>
                                        <input type="hidden" name="ratevalue[]" id="rate-val-3" value="5">
                                        <input type="hidden" name="fdqus[]" id="fd-qus-emp-3"
                                            value="How satisfied were you with the the team and working environment?">
                                        <input type="hidden" name="fdqusid[]" id="fd-qus-id-3" value="21">
                                    </div>
                                </div>
                            </div>
                            <div class="feedback-qus-ans">
                                <p class="qus"><span>Qus. 4)</span>How would you rate the stores' time-keeping and diary
                                    management?</p>
                                <div class="user-rating">

                                </div>
                                <div style="clear:both">
                                    <div class="rating pull-left" id="note-rate-4">
                                        <span><input type="radio" name="rating" value="5"><label
                                                for="str5"></label></span>
                                        <span class="checked"><input type="radio" name="rating" value="4"
                                                checked="checked"><label for="str4"></label></span>
                                        <span><input type="radio" name="rating" value="3"><label
                                                for="str3"></label></span>
                                        <span><input type="radio" name="rating" value="2"><label
                                                for="str2"></label></span>
                                        <span><input type="radio" name="rating" value="1"><label
                                                for="str1"></label></span>
                                        <input type="hidden" name="ratevalue[]" id="rate-val-4" value="4">
                                        <input type="hidden" name="fdqus[]" id="fd-qus-emp-4"
                                            value="How would you rate the stores' time-keeping and diary management?">
                                        <input type="hidden" name="fdqusid[]" id="fd-qus-id-4" value="22">
                                    </div>
                                </div>
                            </div>
                            <div class="feedback-qus-ans">
                                <p class="qus"><span>Qus. 5</span>Overall how satisfied were you with the employer?</p>
                                <div class="user-rating">

                                </div>
                                <div style="clear:both">
                                    <div class="rating pull-left" id="note-rate-5">
                                        <span><input type="radio" name="rating" value="5"><label
                                                for="str5"></label></span>
                                        <span><input type="radio" name="rating" value="4"><label
                                                for="str4"></label></span>
                                        <span class="checked"><input type="radio" name="rating" value="3"
                                                checked="checked"><label for="str3"></label></span>
                                        <span><input type="radio" name="rating" value="2"><label
                                                for="str2"></label></span>
                                        <span><input type="radio" name="rating" value="1"><label
                                                for="str1"></label></span>
                                        <input type="hidden" name="ratevalue[]" id="rate-val-5" value="3">
                                        <input type="hidden" name="fdqus[]" id="fd-qus-emp-5"
                                            value="Overall how satisfied were you with the employer?">
                                        <input type="hidden" name="fdqusid[]" id="fd-qus-id-5" value="23">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="total-rates" id="total-rates" value="3">
                            <div class="feedback-comment">
                                <label class="required control-label " for="feedback_comment">Feedback Comment</label>
                                <div class="f-comment">
                                    <p class="form-control" readonly>Hi, Nice client to work with</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="padding: 16px 13px;">
                        <label class="required control-label col-lg-2"
                        for="feedback_status">Feedback Status</label>
                        @php
                            $statusOptions = [
                                1 => 'Approved',
                                2 => 'Dispute Pending',
                                3 => 'Dispute Approved',
                            ];
                        @endphp
                        <div class="col-lg-10" style="margin-top:10px !important;">
                            <select name="status" class="form-control" id="status" >
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @if ($feedback->status == $value) selected @endif>{{ $label }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row bg-danger text-center">
                        <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                    </div>
                    </form>
                </div>
        </div>
        </div>


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
                //  Check Radio-box
                //alert("#note-rate-"+i+" input:radio");
                //jQuery("div[id*='note-rate-']").click( function(){
                //var id = jQuery(this).attr('id');
                //var qusnum = id.replace('note-rate-',' ').trim();
                //jQuery("#"+id+" input:radio").attr("checked", false);
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
    </div>
    </div>
    </div>
    <script type="text/javascript">
        Gc.keepAlive('/admin/keep-alive');
    </script>
    <a class="btn-scroll-up btn btn-small btn-inverse" id="btn-scroll-up" href="#">
        <i class="glyphicon glyphicon-open"></i>
    </a>
@endsection
