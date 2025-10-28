@extends('layouts.user_profile_app')

@push('styles')
    <style type="text/css">
        div#fs-calender-action .modal-header {
            background: #24a9e0;
            padding: 8px 15px;
            color: #fff;
        }

        div#fs-calender-action .modal-footer {
            padding: 2px;
            background: #24a9e0;
        }
        .btn.btn-ext {
           white-space: normal;  /* Allows text to wrap */
            word-wrap: break-word; /* Breaks long words to fit inside */
            overflow: hidden;      /* Prevents overflow beyond button */
            text-align: center;    /* Centers the text */
            padding: 6px 2px;     /* Ensures padding doesn't increase button size */
            display: block; 
        }
    </style>
@endpush

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/employer/dashboard">My Dashboard</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-sm-6 col-xs-12 pad0">
                        <div class="set-icon registration-icon">
                            <i class="glyphicon glyphicon-user" aria-hidden="true"></i>
                        </div>
                        <div class="set-title">
                            <h3>My Dashboard</h3>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12 pad0">
                        <a class="btn btn-ext btn-sm btn-block btn-info" href="{{ route('employer.help.job-booking-employer') }}">Job booking guide</a><a class="btn btn-ext btn-sm btn-block btn-info"
                           href="{{ route('employer.help.finance-model-employer') }}">Finance section explained</a>
                    </div>
                </div>
            </div>
        </div>

        @if (!Auth::user()->isUserProfileCompleted())
            <div class="modal fade" id="complate-profile-notification" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="modal-body">
                            <p class="complete-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></p>
                            <p>We noticed you have some questions unasnwered - Please complete your profile to ensure that we can match you to employers for job invitations.</p>
                            <p> <a href="/freelancer/edit-questions">Click here</a> to complete your profile.</p>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                $(window).on('load', function() {
                    $('#complate-profile-notification').modal('show');
                });
            </script>
        @endif

    </section>
    <div id="primary-content" class="main-content profiles">
        <div class="container">
            <div class="row">
                <div class="gray-gradient contents">
                    <div class="welcome-heading">
                        <h1>Welcome <span> {{ Auth::user()->firstname . ' ' . Auth::user()->lastname }} </span> [ID {{ Auth::user()->id }}]</h1>
                        <hr class="shadow-line">
                    </div>
                    <div class="profile-details">
                        <div class="profile-title">
                            <h1>My Dashboard</h1>
                        </div>
                        <div class="profile-edit" style="display:none;">
                            <div class="col-md-3 col-sm-3">
                                <div class="margin-bottom prof-img">
                                    <img src="/frontend/locumkit-template/img/no-photo-icon.png" width="200" class="img-responsive">
                                </div>
                                <div class="profile-name">
                                    <h2>{{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}</h2>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3">
                                <div class="profile-tab">
                                    <h3>Membership ID</h3>
                                    <span> {{ Auth::user()->id }} </span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3">
                                <div class="profile-tab">
                                    <h3>Membership Since</h3>
                                    <span> {{ Auth::user()->email_verified_at }} </span>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2">
                                <div class="profile-tab">
                                    <h3>Last Login</h3>
                                    <span> {{ now()->toDateString('d-m-Y') }} </span>
                                </div>
                            </div>
                        </div>
                        <div class="profile-edit-btn">
                            <a class="gradient-threeline" href="/employer/edit-profile">View / Edit Profile</a>
                            <a class="gradient-threeline" href="/employer/edit-questions">Edit Registration</a>
                            <a href="/employer/job-listing?sort_by=job_date&order=DESC" class="gradient-threeline">Job Management</a>
                            <a href="{{ route('employer.manage-store.index') }}" class="gradient-threeline">MANAGE STORE</a>
                        </div>
                        <div style="clear:both;"></div>

                        <div id="section1" style="text-align:left;">
                            <div id="delete_box" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <div class="modal-content">
                                        <div class="modal-header no-border-bottom">
                                            <button type="button" class="close" data-dismiss="modal" onclick="close_dive('delete_box');">&times;</button>
                                            <h4 class="modal-title">Locumkit</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form name="delete_profile_frm" id="delete_profile" action="/employer/profile/delete" method="post" onsubmit="return validate()">
                                                @csrf
                                                @method('delete')
                                                <p>Reason For Deleting Profile:</p>
                                                <p class="padding-left-30"><input type="checkbox" name="reason[]" value="Not satisfied with the customer service ">&nbsp; Not satisfied with the customer service.</p>
                                                <p class="padding-left-30"><input type="checkbox" name="reason[]" value="Not value for money">&nbsp; Not value for money.</p>
                                                <p class="padding-left-30"><input type="checkbox" name="reason[]" value="Not enough bookings through the website">&nbsp; Not enough bookings through the website.</p>
                                                <p class="padding-left-30"><input type="checkbox" name="reason[]" value="Not happy with website/difficult to use">&nbsp; Not happy with website/difficult to use.</p>

                                                <p><input type="submit" name="delete_user" class="btn btn-small btn-warning" value="Submit"></p>
                                            </form>
                                        </div>
                                        <div class="modal-footer no-border-top">
                                            <div id="question_error" class="css_error margin-bottom"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row avilability-div">
                        <div class="welcome-heading job-overview">
                            <h1><span>JOB OVERVIEW</span></h1>
                        </div>
                        <div class="col-md-6 profile-action-btn">
                            <div id="date-dialog"></div>
                            <div id="block-dates" class="box"></div>
                            <div class="coloe-info">
                                <ul>
                                    <li><span class="color-box orange light-orange"></span> Days worked </li>
                                    <li><span class="color-box" style="background:#00a9e0 ;"></span> Current Date </li>
                                    <li><span class="color-box orange"></span> Booked</li>
                                    <li><span class="color-box green"></span> Available</li>
                                </ul>
                            </div>


                            <input type="hidden" id="block_date_array" value="" name="block_dates">
                        </div>

                        <div class="col-md-6 current-booking">
                            <h3>Current month bookings</h3>
                            @if (sizeof($current_month_bookings) > 0)
                                <div class="emp-current-booking-info">
                                    <table class="table-striped table" id="current_booking-info">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Day</th>
                                                <th>Locum ID</th>
                                                <th>Locum Name</th>
                                                <th>Job Rate</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($current_month_bookings as $job)
                                                @php
                                                    $freelancer_data = $job->getAcceptedFreelancerData();
                                                @endphp
                                                <tr @if ($job->job_date >= today()) class="coming-date" @endif>
                                                    <td data-order="{{ $job->job_date }}">
                                                        <span @if ($job->job_date >= today()) class="coming-date" @else class="old-date" @endif> {{ get_date_with_default_format($job->job_date) }} </span>
                                                    </td>
                                                    <td> {{ $job->job_date->format('D') }} </td>
                                                    <td> {{ $freelancer_data['id'] }} </td>
                                                    <td> {!! $freelancer_data['name'] !!} </td>
                                                    <td> {{ set_amount_format($job->job_rate) }} </td>
                                                    <td style="text-align:center;">
                                                        <a href="/employer/view-job/{{ $job->id }}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 pull-right" align="right"><a href="/employer/job-listing?sort_by=job_date&order=DESC" titlt="Read More">More</a></div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-4 booking-dates"></div>
                                    <div class="col-md-8 booking-info">
                                        <p>No records found.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>

                    <div class="row feedback-div" style="clear: both;">
                        <div class="welcome-heading finance">
                            <h1><span>FINANCES</span></h1>
                        </div>
                    </div>

                    <div class="row feedback-div margin-top" style="clear: both;">

                        <div class="col-md-6 finance-graph" align="center">
                            <h2 class="marb0"><span class="color-finance">Expenditure on locum<span></h2>
                            <span class="finance-txt-css">Year </span>

                            <div class="income-graph graph-chart">
                                <span style="position: relative;top: 6px;left: -245px;">Expense</span>
                                <canvas id="myChart" width="500" height="230" class="well"></canvas>
                                <span style="position: relative;top: 6px;left: -245px;">Months</span>
                                <div id="myChart-legend" class="chart-legend"></div>
                            </div>

                        </div>
                        <div class="col-md-6 finance-graph" align="center">
                            <h2 class="marb0"><span class="color-finance">No. of locums recruited</span></h2>
                            <span class="finance-txt-css">Year </span>

                            <div class="income-graph graph-chart">
                                <span style="position: relative;top: 6px;left: -245px;">Numbers</span>
                                <canvas id="myChart2" width="500" height="230" class="well"></canvas>
                                <span style="position: relative;top: 6px;left: -245px;">Months</span>
                                <div id="myChart2-legend" class="chart-legend"></div>
                            </div>

                        </div>
                        <div class="col-md-12" align="center">
                            <div class="profile-edit-btn">
                                <a class="btn btn-info" href="javascript:void(0);" id="manageFinancialyear" onclick="manageFinancialyear();">Update financial year</a>
                                <a class="btn btn-info" href="/employer/finance">See more</a>

                            </div>
                            <p>&nbsp;</p>
                        </div>
                    </div>

                    @can('manage_feedback')
                        <div class="row feedback-div feedback-section" style="clear: both;">
                            <div class="welcome-heading feedback">
                                <h1><span>FEEDBACK</span></h1>
                            </div>
                            @if (sizeof($feedbacks) > 0)
                                <div class="col-md-5 total-rating" style="float: left;">
                                    <div class="rating-bordered">
                                        <h3>Overall average score</h3>

                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="{{ $overall_rating }}" aria-valuemin="{{ $overall_rating }}" aria-valuemax="100" style="width:{{ $overall_rating }}%;">
                                                <div id="profle-progress-bar">{{ $overall_rating }}%</div>
                                            </div>
                                        </div>
                                        <h4> Rated by <span> {{ sizeof($feedbacks) }} </span> {{ Auth::user()->user_acl_role_id == 2 ? 'employers' : 'locums' }} </h4>
                                    </div>
                                </div>

                                <div class="col-md-6 individual-rating">
                                    <h3>Individual ratings</h3>
                                    <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner" role="listbox">
                                            @foreach ($feedbacks as $feedback)
                                                <div class="item @if ($loop->iteration == 1) active @endif">
                                                    <div class="row">
                                                        <div class="col-md-6 feedback-img">
                                                            <div class="user-info">
                                                                <h3> <i class="fa fa-user" aria-hidden="true"></i> {{ $feedback->freelancer->firstname . ' ' . $feedback->freelancer->lastname }} </h3>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 feebacl-comment-section">

                                                            <div class="user-info">
                                                                <ul>
                                                                    <li>
                                                                        <div id="stars-rating" class="user-rating">
                                                                            @for ($i = 5; $i >= 1; $i--)
                                                                                @if ($feedback->rating <= $i)
                                                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                                                @else
                                                                                    <i class="fa fa-star-o" aria-hidden="true"></i>
                                                                                @endif
                                                                            @endfor

                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                                <div class="feedback-date">
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i> {{ $feedback->created_at->format('d-m-Y') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="carousel-nav">
                                            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="all-feedback-btn">
                                    <a class="btn btn-info" href="/employer/feedback-detail" role="button">
                                        View all
                                    </a>
                                </div>
                            @else
                                <h5 style='text-align: center; font-size: 22px; color: red;'> No record found. </h5>
                            @endif
                        </div>
                    @endcan


                    <div class="row feedback-div cancellation-percentage" style="clear: both;">
                        <div class="welcome-heading">
                            <div class="col-md-12 col-sm-12 col-xs-12 cancellatn-perblk">

                                <h3 class="text-center"><span>Cancellation percentage : {{ $cancellation_rate }}% </span><em>(for last six months)</em></h3>
                            </div>

                        </div>
                    </div>

                    <div class="welcome-heading industrial-news">
                        <h1><span>INDUSTRY NEWS</span></h1>
                    </div>
                    <div class="row feedback-div margin-top" style="clear: both;">
                        @foreach ($industry_news as $news)
                            <div class="col-md-4 small-icon-box ">
                                <div class="set-icon">
                                    <figure class="">

                                        <img src="{{ '/storage/' . $news->image_path }}" alt="{{ $news->title }}" class="img-responsive img-thumbnail" style="height:220px;" width="100%">
                                    </figure>
                                </div>
                                <div class="set-content">
                                    <h3>{{ $news->title }}</h3>
                                    <!-- <p>{{!! $news->description !!}}</p> -->
                                    <p>{!! get_cleaned_html_content($news->description) !!}</p>

                                    
                                </div><br>
                                <div class="set-button">
                                    <!-- <a class="read-common-btn2" href="/c/{{ $news->slug }}">Read More</a> -->
                                    <a class="read-common-btn2" href="/news/{{$news->slug}}">Read More</a>
                                </div>
                            </div>
                        @endforeach


                    </div>
                    <div class="profile-details dlete-profile-section">
                        <div class="profile-edit-btn">
                            <a href="javascript:void(o);" class="btn-small btn-warning" onClick="confirm_delete();">Delete Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="manage-financial-year" class="modal fade financepopup" role="dialog">
            <div class="modal-dialog">
                <form action="" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"> Select financial year </h4>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-12 pad0 financeform">
                                <div class="form-group" id="bank_date">
                                    <input type="hidden" name="fiusertype" id="fiusertype" value="limitedcompany">
                                    <div class="fiusertypecon">
                                        <div class="col-md-5">Select financial year starting month</div>
                                        <div class="col-md-7">
                                            <select name="finmonth" id="finmonth" class="form-control" required>
                                                <option value="">Select</option>
                                                <option value="1" @selected($finance_year_start_month == '1')>January</option>
                                                <option value="2" @selected($finance_year_start_month == '2')>February</option>
                                                <option value="3" @selected($finance_year_start_month == '3')>March</option>
                                                <option value="4" @selected($finance_year_start_month == '4')>April</option>
                                                <option value="5" @selected($finance_year_start_month == '5')>May</option>
                                                <option value="6" @selected($finance_year_start_month == '6')>June</option>
                                                <option value="7" @selected($finance_year_start_month == '7')>July</option>
                                                <option value="8" @selected($finance_year_start_month == '8')>August</option>
                                                <option value="9" @selected($finance_year_start_month == '9')>September</option>
                                                <option value="10" @selected($finance_year_start_month == '10')>October</option>
                                                <option value="11" @selected($finance_year_start_month == '11')>November</option>
                                                <option value="12" @selected($finance_year_start_month == '12')>December</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-right" onclick="savefinancialyear();" data-dismiss="modal">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="fs-calender-action" role="dialog">
            <div class="modal-dialog">

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close close-alert" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Date Information - <span id="fs-selected-date"></span></h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirm_delete() {
            $('div#alert-confirm-modal #alert-message').html('<span>Are you sure you want to delete your account? <br/> Please note - Once account is deleted all data will be erased.</span>');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                messageBoxClose();
                $('#delete_box').show();
                $('#delete_box').addClass('in');
                $('#delete_box').css('display', 'block');
            });
        }

        function close_dive(id) {
            $("#" + id).hide();
            $('.modal-backdrop').hide();
            //location.reload();
        }

        function validate() {
            var chks = document.getElementsByName('reason[]');
            var hasChecked = false;
            for (var i = 0; i < chks.length; i++) {
                if (chks[i].checked) {
                    hasChecked = true;
                    break;
                }
            }
            if (hasChecked == false) {
                //alert("Please select at least one.");
                messageBoxOpen('Please select a reason to delete your account.');
                $('.alert-modal .modal-footer button.btn.btn-default').removeAttr('onclick');
                return false;
            }
            return true;
        }
    </script>

    {{-- User work calender --}}
    <script type="text/javascript">
        previousDateSpam();
        previousDateBookHistory();

        var bookdates = @json($bookedDates);

        function DisableSpecificDates(date) {
            var string = $.datepicker.formatDate('yy-mm-dd', date);
            var today = $.datepicker.formatDate('yy-mm-dd', new Date());

            var currentTime = new Date(`{{ now() }}`);
            var block_today = 0;
            if (string == today) {
                if (currentTime.getHours() > 11) {
                    block_today = 1;
                }
                if (currentTime.getHours() == 11 && currentTime.getMinutes() > 30) {
                    block_today = 1;
                }
            }
            if (string < today) {
                if ($.inArray(string, bookdates) > -1) {
                    return [true, " ui-datepicker-unselectable ui-state-disabled booked-date old-booked-date", ""];
                }
            }

            if ($.inArray(string, bookdates) > -1) {
                return [true, " ui-datepicker-unselectable ui-state-disabled booked-date", ""];
            } else if (string > today) {
                return [true, "available-date", ""];
            } else if (string == today && block_today == 0) {
                return [true, "available-date", ""];
            } else if (string == today && block_today == 1) {
                return [true, "ui-datepicker-unselectable ui-state-disabled", ""];
            } else {
                return [true, "ui-datepicker-unselectable ui-state-disabled", ""];
            }

        }

        $('#block-dates').datepicker({
            dateFormat: 'yy-m-d',
            inline: true,
            beforeShowDay: DisableSpecificDates,
            minDate: -120,
            /* onSelect freezes the dialog box and populates with a link
             want to populate the dialog on hover and then freeze so that link can be followed*/
            onSelect: function(date) {
                performSpecificAction();
            }
        });

        calenderAction();

        function calenderAction() {
            $("#block-dates table.ui-datepicker-calendar a").mouseover(function() {
                $(this).attr('title', 'Click for details');
            });

            $("#block-dates td.available-date a").click(function() {
                $('#fs-calender-action').modal('show');
                event.preventDefault();
                var MyDateString = (0 + $(this).text()).slice(-2) + "/" + ('0' + (parseInt($(this).parents().attr('data-month')) + 1)).slice(-2) + "/" + $(this).parents().attr('data-year');
                var date = $(this).parents().attr('data-year') + '-' + (parseInt($(this).parents().attr('data-month')) + 1) + '-' + $(this).text();
                var available_html = '';
                available_html += '<p><b>This date is avilable to post job.</b><br/><span style="font-size:12px">(<a href="/employer/managejob?doj=' + date + '">Click here</a> to post job.)</span></p><hr/>';
                $('#fs-calender-action .modal-body').html(available_html);
                $('#fs-calender-action').modal('show');

            });

            $("#block-dates td.booked-date a").click(function() {
                $('#fs-calender-action').modal('show');
                event.preventDefault();
                $('#fs-calender-action .modal-body').html('<div id="loader-div-dialog"><div class="loader-dialog"></div></div>');
                var hoverDate = $(this).text() + " " + $(".ui-datepicker-month", $(this).parents()).text() + " " + $(".ui-datepicker-year", $(this).parents()).text();
                var dataMonth = $(this).parent('td').attr('data-month');
                var currentMonth = parseInt(dataMonth) + 1;
                $('#fs-selected-date').html((0 + $(this).text()).slice(-2) + "/" + (0 + currentMonth) + "/" + $(this).parents().attr('data-year'));

                $.ajax({
                    'url': '/ajax/get-booked-date-info',
                    'type': 'POST',
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    'data': {
                        'date': hoverDate,
                    },
                    'success': function(result) {
                        $("#loader-div-dialog").hide(100);
                        if (result.success) {
                            $('#fs-calender-action .modal-body').html(result.html);
                        }
                    }
                });
            });

        }
        $("div#block-dates").click(function() {
            previousDateSpam();
            previousDateBookHistory();
            calenderAction();
        });

        function showMinRate() {
            $('#min_rate_date').show(1000);
        }

        function hideMinRate() {
            $('#min_rate_date').hide(1000);
        }

        function previousDateSpam() {
            $("td.ui-datepicker-unselectable.ui-state-disabled a").each(function() {
                $(this).replaceWith(function() {
                    return $("<span class='ui-state-default'>" + $(this).html() + "</span>");
                });
            });
            $("td.ui-datepicker-week-end.block-date a").each(function() {
                $(this).replaceWith(function() {
                    return $("<span class='ui-state-default'>" + $(this).html() + "</span>");
                });
            });
        }

        function previousDateBookHistory() {
            $("td.ui-datepicker-unselectable.ui-state-disabled.booked-date span").each(function() {
                $(this).replaceWith(function() {
                    return $("<a href='#' class='ui-state-default'>" + $(this).html() + "</a>");
                });
            });
        }
    </script>

    <script src="/frontend/locumkit-template/js/Chart.js"></script>

    <script>
        /* Injecting blade data into javascript as global variable  */
        const income_by_months = @json($employer_finance_cost);
        const expense_chart_data = @json($employer_finance_job);
        const site_currency = `{{ get_site_currency_symbol() }}`;
        var income_months_labels = Object.keys(income_by_months);
        var income_chart_data = Object.values(income_by_months);
        var expense_labels = Object.keys(expense_chart_data);
        var expense_data_values = Object.values(expense_chart_data);

        var options = {
            animation: true,
            multiTooltipTemplate: "£ <%= value %>.00",
            scaleLabel: "<%= ' ' + value%>",
        };

        //Income chart
        var data = {
            labels: income_months_labels,
            datasets: [{
                label: "Income",
                fillColor: "#85A04C",
                strokeColor: "#85A04C",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                data: income_chart_data
            }]
        };

        //Expense chart
        var dataExpense = {
            labels: expense_labels,
            datasets: [{
                label: "Expense",
                fillColor: "#A44442",
                strokeColor: "#A44442",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                data: expense_data_values
            }]
        }

        $(document).ready(function() {

            var ctx = document.getElementById("myChart").getContext("2d");
            var myChart = new Chart(ctx).Bar(data, options);

            var ctx2 = document.getElementById("myChart2").getContext("2d");
            var myChart2 = new Chart(ctx2).Bar(dataExpense, options);

        });
    </script>
    <script>
        function manageFinancialyear() {
            $('#manage-financial-year').modal('show');
        }

        $('#fiusertype').change(function() {
            var val = $(this).val();
            if (val == 'soletrader') {
                $(".fiusertypecon").hide('1000');
                $('#finmonth').val('4');
            } else if (val == 'limitedcompany') {
                $(".fiusertypecon").show('1000');
            } else {
                $(".fiusertypecon").hide('1000');
            }
        });

        function savefinancialyear() {
            var month = $('#finmonth').val();
            var fiusertype = $('#fiusertype').val();

            $.ajax({
                'url': '/ajax/employer/update-financial-year',
                'type': 'PUT',
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                },
                'data': {
                    month: month,
                    usertype: fiusertype
                },
                'success': function(result) {
                    if (result.success) {
                        location.reload();
                    }
                },
                'error': function() {
                    alert("Some error occured during updation");
                }
            });
        }

        function confirm_change_pkg(msg) {
            event.preventDefault();
            $('div#alert-confirm-modal #alert-message').html(msg);
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                messageBoxClose();
                window.location.href = $('a#change-pkg-btn').attr('href');
            });
        }

        //datatable on job overview section
        $(document).ready(function() {

            $('#current_booking-info').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
                "order": [
                    [0, "desc"]
                ],
                "columnDefs": [{
                    "targets": [1, 2, 3, 4, 5],
                    "orderable": false
                }]
            });
        });
    </script>
@endpush
