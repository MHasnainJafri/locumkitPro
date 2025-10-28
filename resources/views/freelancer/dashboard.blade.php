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

        .model {
            border: 5px dashed#0b7bab;
            margin-bottom: 5%;
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
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
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
                        <a class="btn btn-ext btn-sm btn-block btn-info"
                            href="{{ route('freelancer.help.job-booking-freelancer') }}">Job booking guide</a><a
                            class="btn btn-ext btn-sm btn-block btn-info"
                            href="{{ route('freelancer.help.finance-model-freelancer') }}">Finance section explained</a>
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
                            <p>We noticed you have some questions unasnwered - Please complete your profile to ensure that
                                we can match you to employers for job invitations.</p>
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
        @foreach ($job_action as $notification)
        <div class="container  model d-none">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Alert Notification!</h5>
                <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <form action="{{route('freelancer.add.feedback')}}" method="POST">
                        @csrf
                        <input type="hidden" name="job_id" value="{{$notification->jobposting->id}}">
                        <input type="hidden" name="employer_id" value="{{$notification->jobposting->employer_id}}">
                        <input type="hidden" name="freelancer_id" value="{{$notification->freelancer_id}}">
                        <input type="hidden" name="cat_id" value="{{$notification->freelancer->user_acl_profession_id}}">
                        <input type="hidden" name="user_type" value="{{$notification->freelancer->user_acl_role_id}}">

                        <div class="form-group">
                            <label for="rating">Rating*</label>
                            <select name="rating" id="" class="form-control ">
                                <option value="" selected disabled>Select</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group col-6">
                            <label for="feedback">Feedback*</label>
                            <input type="text" class="form-control" id="feedback" name="feedback" value="">
                        </div>
                        <div class="form-group">
                            <label for="comments">Comments*</label>
                            <input type="text" class="form-control" id="comments" name="comments" value="">
                        </div>
                        <input type="submit" value="Save" class="btn btn-primary">
                    </form>
                    {{-- <h3>JobID..{{$notification->jobposting->id}}</h3>
                    <h2>EmployerID..{{$notification->jobposting->employer_id}}</h2>
                    <h4>Hello.. {{ $notification->jobposting->job_title }}</h4>
                    <p>Your work , {{ $notification->jobposting->job_post_desc }}</p> --}}

                </div>
            <div class="modal-footer">
            </div>
        </div>
    @endforeach
        @foreach ($notify as $notification)


            <div class="container  model">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Alert Notification!</h5>
                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                        <h4>Hello.. {{ $notification->jobposting->job_title }}</h4>
                        <p>Your work , {{ $notification->jobposting->job_post_desc }}</p>
                        <h6>{{ $notification->message }}</h6>
                    </div>
                <div class="modal-footer">
                    @if($notification->status==2)
                    <a class="btn btn-info sm-btn" href="{{route('freelancer.final-update-status',$notification->id)}}">Yes</a>
                    @else
                    <a type="button" href="{{route('freelancer.update-status-yes',$notification->id)}}" class="btn btn-info" data-dismiss="modal">Yes</a>
                    @endif
                    <a class="btn btn-info sm-btn" href="{{route('freelancer.update-status-no',$notification->id)}}">No</a>
                </div>
            </div>
        @endforeach

        <div class="container">
            <div class="row">
                <div class="gray-gradient contents">
                    <div class="welcome-heading">
                        <h1>Welcome <span> {{ Auth::user()->firstname . ' ' . Auth::user()->lastname }} </span> [ID
                            {{ Auth::user()->id }}]</h1>
                        <hr class="shadow-line">
                    </div>
                    <div class="profile-details">
                        <div class="profile-title">
                            <!--<h1>My Dashboard</h1>-->
                        </div>
                        <div class="profile-edit" style="display:none;">
                            <div class="col-md-3 col-sm-3">
                                <div class="margin-bottom prof-img">
                                    <img src="/frontend/locumkit-template/img/no-photo-icon.png" width="200"
                                        class="img-responsive">
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
                            <a class="gradient-threeline" href="/freelancer/edit-profile">View / Edit Profile</a>
                            <a class="gradient-threeline" href="/freelancer/edit-questions">Edit Registration</a>
                            <a href="/freelancer/job-listing?sort_by=job_date&order=DESC" class="gradient-threeline">Job
                                Management</a>
                            <a href="{{ route('freelancer.locumlogbook.follow-up-procedures.index') }}"
                                class="gradient-threeline">LOCUM DIARY</a>
                        </div>
                        <div style="clear:both;"></div>

                        <div id="section1" style="text-align:left;">
                            <div id="delete_box" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <div class="modal-content">
                                        <div class="modal-header no-border-bottom">
                                            <button type="button" class="close" data-dismiss="modal"
                                                onclick="close_dive('delete_box');">&times;</button>
                                            <h4 class="modal-title">Locumkit</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form name="delete_profile_frm" id="delete_profile"
                                                action="/freelancer/profile/delete" method="post"
                                                onsubmit="return validate()">
                                                @csrf
                                                @method('delete')
                                                <p>Reason For Deleting Profile:</p>
                                                <p class="padding-left-30"><input type="checkbox" name="reason[]"
                                                        value="Not satisfied with the customer service ">&nbsp; Not
                                                    satisfied with the customer service.</p>
                                                <p class="padding-left-30"><input type="checkbox" name="reason[]"
                                                        value="Not value for money">&nbsp; Not value for money.</p>
                                                <p class="padding-left-30"><input type="checkbox" name="reason[]"
                                                        value="Not enough bookings through the website">&nbsp; Not
                                                    enough
                                                    bookings through the website.</p>
                                                <p class="padding-left-30"><input type="checkbox" name="reason[]"
                                                        value="Not happy with website/difficult to use">&nbsp; Not
                                                    happy
                                                    with website/difficult to use.</p>

                                                <p><input type="submit" name="delete_user"
                                                        class="btn btn-small btn-warning" value="Submit"></p>
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
                                    <li><span class="color-box" style="background: #00A9E0;"></span> Current Date </li>
                                    <li><span class="color-box orange"></span> Booked</li>
                                    <li><span class="color-box green"></span> Available</li>
                                    <li><span class="color-box red"></span> Not available</li>
                                </ul>
                            </div>


                            <input type="hidden" id="block_date_array" value="" name="block_dates">
                        </div>
                        <div class="col-md-6 current-booking">
                            <h3>Current month bookings</h3>
                            <h4>Live Job(s)</h4>
                            <div class="table-responsive">

                                @if ($currentMonthLiveJobs && sizeof($currentMonthLiveJobs) > 0)
                                    <table class="table-striped table" id="current_booking-info">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Day</th>
                                                <th>Rate</th>
                                                <th>Store</th>
                                                <th>Location</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($currentMonthLiveJobs as $currentMonthLiveJob)
                                                <tr
                                                    class="{{ today()->greaterThan($currentMonthLiveJob->job_date) ? 'old-date' : '' }}">
                                                    <td data-order="{{ $currentMonthLiveJob->job_date }}"> <span
                                                            class="{{ today()->greaterThan($currentMonthLiveJob->job_date) ? 'old-date' : 'coming-date' }}">
                                                            {{ get_date_with_default_format($currentMonthLiveJob->job_date) }}
                                                        </span>
                                                    </td>
                                                    <td> {{ $currentMonthLiveJob->job_date->format('l') }} </td>
                                                    <td> {{ set_amount_format($currentMonthLiveJob->job_rate) }} </td>
                                                    <td> {{ $currentMonthLiveJob->job_store->store_name }} </td>
                                                    <td> {{ $currentMonthLiveJob->job_address }} </td>

                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                @else
                                    <div class="col-md-12 booking-info">
                                        <p>No record found.</p>
                                    </div>
                                @endif

                            </div>
                            <div class="row">
                                <div class="col-md-8 pull-right" align="right"><a href="/freelancer/job-listing"
                                        titlt="Read More"> <b>View More</b> </a></div>
                            </div>
                            <h4>Private Job(s)</h4>
                            @if (sizeof($currentMonthPrivateJobs) > 0)
                                <div class="emp-current-booking-freelancer private-job-jobs-book">
                                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper no-footer">
                                        <table class="table-striped short-job-desc dataTable no-footer table"
                                            id="DataTables_Table_0" role="grid">
                                            <thead>
                                                <tr role="row">
                                                    <th class="sorting_desc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        aria-sort="descending"
                                                        aria-label="Date: activate to sort column ascending"
                                                        style="width: 138px;">Date</th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Day" style="width: 51px;">Day</th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Rate" style="width: 95px;">Rate</th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Title" style="width: 88px;">Title</th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Location" style="width: 104px;">Location</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($currentMonthPrivateJobs as $job)
                                                    <tr @if ($job->job_date >= today()) class="coming-date" @endif
                                                        role="row">
                                                        <td data-order="{{ $job->job_date }}">
                                                            <span
                                                                class="{{ $job->job_date >= today() ? 'coming-date' : 'old-date' }}">
                                                                {{ get_date_with_default_format($job->job_date) }}
                                                            </span>
                                                        </td>
                                                        <td> {{ $job->job_date->format('D') }} </td>
                                                        <td> {{ set_amount_format($job->job_rate) }} </td>
                                                        <td>
                                                            <p> {{ substr($job->job_title, 0, 10) }}... </p>
                                                        </td>
                                                        <td> {{ substr($job->job_location, 0, 10) }}... </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 pull-right" align="right"><a href="/freelancer/private-job"
                                            titlt="Read More"><b>View More</b></a></div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-12 booking-info">
                                        <p>Currently there are no private job bookings. <a
                                                href="/freelancer/private-job">Click
                                                here</a> to add.</p>
                                    </div>
                                </div>
                            @endif


                        </div>
                    </div>

                    @if (sizeof($intersetedJobs) > 0)
                        <div class="row job-interested-div" style="clear: both;">
                            <div class="welcome-heading">
                                <h1><span>JOB(S) YOU MAY BE INTERESTED IN </span></h1>
                            </div>
                            <div class="job-list-table">
                                <div class="job-list-table-inner">
                                    <table class="table-striped table">
                                        <colgroup>
                                            <col width="10%">
                                            <col width="10%">
                                            <col width="8%">
                                            <col width="20%">
                                            <col width="35%">
                                            <col width="20%">
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th>Job Id</th>
                                                <th>Job Date</th>
                                                <th>Rate</th>
                                                <th>Store Name</th>
                                                <th>Location</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($intersetedJobs as $job)
                                                <tr>
                                                    <td> {{ $job->id }} </td>
                                                    <td> {{ get_date_with_default_format($job->job_date) }} </td>
                                                    <td>
                                                        {{ set_amount_format($job->job_rate) }}
                                                    </td>
                                                    <td> {{ $job->job_store->store_name }} </td>
                                                    <td>{{ $job['job_address'] . ', ' . $job['job_region'] . ', ' . $job['job_zip'] }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="job-accept-btn"
                                                            href="{{ get_interested_job_links($job->id, Auth::user()->id)['accept_href_link'] }}">Accept</a>
                                                        <a class="job-accept-btn"
                                                            href="{{ get_interested_job_links($job->id, Auth::user()->id)['negotiate_href_link'] }}">Negotiate</a>
                                                        @if (today()->addDays(2)->lessThan($job->job_date) && can_user_package_has_privilege(Auth::user(), 'job_freeze'))
                                                            <a class="job-freeze-btn"
                                                                href="{{ get_interested_job_links($job->id, Auth::user()->id)['freeze_href_link'] }}">
                                                                Freeze</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row feedback-div" style="clear: both;">
                        <div class="welcome-heading finance">
                            <h1><span>FINANCES</span></h1>
                        </div>
                    </div>

                    <div class="finance-blk-wppr">
                        <div class="col-md-3 col-xs-6" align="center">
                            <p class="finance-price"> {{ set_amount_format($total_income) }} </p><span
                                class="finance-txt-css">TOTAL INCOME<span>
                        </div>
                        <div class="col-md-3 col-xs-6" align="center">
                            <p class="finance-price"> {{ set_amount_format($total_expense) }} </p><span
                                class="finance-txt-css">TOTAL EXPENSE</span>
                        </div>
                    </div>

                    <div class="col-md-3" align="center">
                        <p class="finance-price"> {{ set_amount_format($total_income - $total_expense) }} </p><span
                            class="finance-txt-css">NET INCOME</span>
                    </div>
                    <div class="col-md-3" align="center">
                        <p class="finance-price"> {{ set_amount_format($user_total_tax) }} </p><span
                            class="finance-txt-css">ESTIMATED TAX</span>
                    </div>

                    <div class="row feedback-div margin-top" style="clear: both;">

                        <div class="col-md-6 finance-graph incm-desktp" align="center">
                            <h2 class="marb0"><span class="color-finance">INCOME<span></h2>
                            <span class="finance-txt-css">Year
                                {{ get_financial_year_range_string($finance_year_start_month) }} </span>

                            <div class="income-graph graph-chart">
                                <span style="position: relative;top: 6px;left: -245px;">Income</span>
                                <!--<span style="position: relative;top: 6px;left: -245px;">Expence</span>-->
                                <canvas id="myChart" width="400" height="200" class="well"></canvas>
                                <span style="position: relative;top: -15px;left: -200px;">Months</span>
                                <div id="myChart-legend" class="chart-legend"></div>
                            </div>

                        </div>
                        <div class="col-md-6 finance-graph expense-desktop" align="center">
                            <h2 class="marb0"><span class="color-finance">EXPENSE</span></h2>
                            <span class="finance-txt-css">Year
                                {{ get_financial_year_range_string($finance_year_start_month) }} </span>

                            <div class="income-graph graph-chart">
                                <span style="position: relative;top: 6px;left: -245px;">Expense</span>
                                <canvas id="myChart2" width="400" height="200" class="well"></canvas>
                                <span style="position: relative;top: -15px;left: -200px;">Months</span>
                            </div>

                        </div>
                        <div class="col-md-12" align="center">
                            <div class="profile-edit-btn">
                                <a class="btn btn-info" href="/freelancer/finance">See more</a>
                            </div>
                            <p>&nbsp;</p>
                        </div>
                    </div>
                    @can('manage_feedback')
                        @if(isset($feedbacks) && sizeof($feedbacks) > 0)
                        <div class="row feedback-div feedback-section" style="clear: both;">
                            <div class="welcome-heading feedback">
                                <h1><span>FEEDBACK</span></h1>
                            </div>
                            @if (sizeof($feedbacks) > 0)
                                <div class="col-md-5 total-rating" style="float: left;">
                                    <div class="rating-bordered">
                                        <h3>Overall average score</h3>

                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar"
                                                aria-valuenow="{{ $overall_rating }}" aria-valuemin="{{ $overall_rating }}"
                                                aria-valuemax="100" style="width:{{ $overall_rating }}%;">
                                                <div id="profle-progress-bar">{{ $overall_rating }}%</div>
                                            </div>
                                        </div>
                                        <h4> Rated by <span> {{ sizeof($feedbacks) }} </span>
                                            {{ Auth::user()->user_acl_role_id == 2 ? 'employers' : 'locums' }} </h4>
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
                                                                <h3> <i class="fa fa-user" aria-hidden="true"></i>
                                                                    {{ $feedback->employer->firstname . ' ' . $feedback->employer->lastname }}
                                                                </h3>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 feebacl-comment-section">

                                                            <div class="user-info">
                                                                <ul>
                                                                    <li>
                                                                        <div id="stars-rating" class="user-rating">
                                                                            @for ($i = 5; $i >= 1; $i--)
                                                                                @if ($feedback->rating <= $i)
                                                                                    <i class="fa fa-star"
                                                                                        aria-hidden="true"></i>
                                                                                @else
                                                                                    <i class="fa fa-star-o"
                                                                                        aria-hidden="true"></i>
                                                                                @endif
                                                                            @endfor

                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                                <div class="feedback-date">
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    {{ $feedback->created_at->format('d-m-Y') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="carousel-nav">
                                            <a class="left carousel-control" href="#myCarousel" role="button"
                                                data-slide="prev">
                                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="right carousel-control" href="#myCarousel" role="button"
                                                data-slide="next">
                                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="all-feedback-btn">
                                    <a class="btn btn-info" href="/freelancer/feedback-detail" role="button">
                                        View all
                                    </a>
                                </div>
                            @else
                                <h5 style='text-align: center; font-size: 22px; color: red;'> No record found. </h5>
                            @endif
                        </div>
                        @endif 
                    @endcan
                    


                    <div class="row feedback-div cancellation-percentage" style="clear: both;">
                        <div class="welcome-heading">
                            <div class="col-md-12 col-sm-12 col-xs-12 cancellatn-perblk">

                                <h3 class="text-center"><span>Cancellation percentage : {{ $cancellation_rate }}%
                                    </span><em>(for last six months)</em></h3>
                            </div>

                        </div>
                    </div>
                    @if(isset($industry_news))
                    
                        <div class="row feedback-div margin-top" style="clear: both;">
                            <div class="welcome-heading industrial-news">
                                <h1><span>INDUSTRY NEWS</span></h1>
                            </div>
                            @foreach ($industry_news as $news)
                                <div class="col-md-4 col-sm-4 small-icon-box industrial-news-content">
                                    <div class="set-icon">
                                        <figure>
    
                                            <img src="{{ '/storage/' . $news->image_path }}" alt="{{ $news->title }}"
                                                class="img-responsive" width="100%">
                                        </figure>
                                    </div>
                                    <div class="set-content">
                                        <h3>{{ $news->title }}</h3>
                                        {{-- <!-- <p>{{!! $news->description !!}}</p> --> --}}
                                        <p>{!! get_cleaned_html_content($news->description) !!}</p>
                                    </div><br>
                                    <div class="set-button">
                                        <!-- <a class="read-common-btn2" href="/c/{{ $news->slug }}">Read More</a> -->
                                        <a class="read-common-btn2" href="/news/{{ $news->slug }}">Read More</a>
                                    </div>
                                </div>
                            @endforeach
    
    
                        </div>
                    @endif

                    <div class="profile-details dlete-profile-section">
                        <div class="profile-edit-btn">
                            <a href="javascript:void(o);" class="btn-small btn-warning"
                                onClick="confirm_delete();">Delete
                                Profile</a>
                        </div>
                    </div>
                </div>
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
            $('div#alert-confirm-modal #alert-message').html(
                '<span>Are you sure you want to delete your account? <br/> Please note - Once account is deleted all data will be erased.</span>'
            );
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
        var disableddates = @json($userBlockDates);

        var bookdates = @json($bookedDates);

        function DisableSpecificDates(date) {
            var string = $.datepicker.formatDate('yy-mm-dd', date);
            var today = $.datepicker.formatDate('yy-mm-dd', new Date());

            var currentTime = new Date(`{{ today() }}`);
            var block_today = 0;
            if (string == today) {
                if (currentTime.getHours() > 11 || (currentTime.getHours() == 11 && currentTime.getMinutes() > 30)) {
                    block_today = 1;
                }
            }
            if (string < today) {
                if ($.inArray(string, disableddates) > -1) {
                    return [true, "ui-datepicker-unselectable ui-state-disabled block-date", ""];
                } else if ($.inArray(string, bookdates) > -1) {
                    return [true, " ui-datepicker-unselectable ui-state-disabled booked-date old-booked-date", ""];
                }
            }

            if ($.inArray(string, disableddates) > -1) {
                return [true, "block-date", ""];
            } else if ($.inArray(string, bookdates) > -1) {
                return [true, " ui-datepicker-unselectable ui-state-disabled booked-date", ""];
            } else if (string > today) {
                return [true, "available-date", ""];
            } else if (string == today && block_today == 0) {
                return [true, "available-date", ""];
            } else if (string == today && block_today == 1) {
                return [true, "ui-datepicker-unselectable past-available-dates", ""];
            } else {
                return [true, "ui-datepicker-unselectable past-available-dates", ""];
            }
        }

        $('#block-dates').datepicker({
            dateFormat: 'yy-m-d',
            inline: true,
            beforeShowDay: DisableSpecificDates,
            minDate: -120,
            onSelect: function(date) {
                performSpecificAction();
            }
        });

        calenderAction();

        function calenderAction() {
            $("#block-dates table.ui-datepicker-calendar a").mouseover(function() {
                $(this).attr('title', 'Click for details');
            });

            $("#block-dates td.past-available-dates a").click(function() {
                $('#fs-calender-action').modal('show');
                event.preventDefault();
                var MyDateString = (0 + $(this).text()).slice(-2) + "/" + ('0' + (parseInt($(this).parents().attr(
                    'data-month')) + 1)).slice(-2) + "/" + $(this).parents().attr('data-year');
                $('#fs-selected-date').html(MyDateString);
                

                let parts = MyDateString.split('/');
                
                let formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                const dateInfoForm = `
                    <p>You don't have any work for this date.</p>
                    <p><a href="/freelancer/private-job?p-date=${formattedDate}" title="Add Private job" style="color:blue;">Click here</a> to add private job</p>
                `;
                $('#fs-calender-action .modal-body').html(dateInfoForm);
            });

            $("#block-dates td.available-date a").click(function() {
                $('#fs-calender-action').modal('show');
                event.preventDefault();
                var MyDateString = (0 + $(this).text()).slice(-2) + "/" + ('0' + (parseInt($(this).parents().attr(
                    'data-month')) + 1)).slice(-2) + "/" + $(this).parents().attr('data-year');
                var date = $(this).parents().attr('data-year') + '-' + ('0' + (parseInt($(this).parents().attr(
                    'data-month')) + 1)).slice(-2) + '-' + (0 + $(this).text()).slice(-2);
                if ($.inArray(MyDateString, disableddates) > -1) {
                    var dateInfoForm =
                        '<form action="#" method="POST" id="calander_date_info" onsubmit="return false;"><input type="radio" name="availability" value="1" onClick="showMinRate()" id="available"> Available (edit rate)<input type="number" name="min_rate_date" value="" min="1" class="form-control margin-bottom" id="min_rate_date" style="display:none" placeholder="Please enter minimum rate"><br/><input type="radio" name="availability" value="2" onClick="hideMinRate()" id="not_available"> Not available<br/><input type="hidden" name="selected_date" id="selected_date" value="' +
                        date +
                        '" class="form-control margin-bottom"><input type="hidden" name="uid"  value="12671" class="form-control margin-bottom"><a href="javascript:void(0)" onClick="return action_save_date_record()" class="save_date_info">Save</a></form>';
                } else {
                    var dateInfoForm =
                        '<form action="#" method="POST" id="calander_date_info" onsubmit="return false;"><input type="radio" name="availability" value="1" onClick="showMinRate()" id="available"> Available (edit rate) <input type="number" name="min_rate_date" value="" min="1" class="form-control margin-bottom" id="min_rate_date" style="display:none" placeholder="Please enter minimum rate"><br/><input type="radio" name="availability" value="2" onClick="hideMinRate()" id="not_available"> Not available<br/><br/><p><a href="/freelancer/private-job?p-date=' +
                        date +
                        '" title="Add Private job" style="color:blue;">Click here</a> to add private job</p><input type="hidden" name="selected_date" id="selected_date" value="' +
                        date +
                        '" class="form-control margin-bottom"><input type="hidden" name="uid"  value="12671" class="form-control margin-bottom"><a href="javascript:void(0)" onClick="return action_save_date_record()" class="save_date_info">Save</a></form>';
                }
                $.ajax({
                    'url': '/ajax/get-info-about-date',
                    'type': 'POST',
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    'data': {
                        'date': date,
                    },
                    'success': function(result) {
                        if (result.success) {
                            var available_html = '';
                            $('#fs-selected-date').html(MyDateString);
                            available_html +=
                                "<p>This date is currently set as available to work </p> <p><b>Minimum Rate :</b> £" +
                                result.rate + "</p>";
                            $('#fs-calender-action .modal-body').html(available_html + dateInfoForm);
                        }
                    }
                });
            });

            $("#block-dates td.booked-date a").click(function() {
                $('#fs-calender-action').modal('show');
                event.preventDefault();
                $('#fs-calender-action .modal-body').html(
                    '<div id="loader-div-dialog"><div class="loader-dialog"></div></div>');
                var hoverDate = $(this).text() + " " + $(".ui-datepicker-month", $(this).parents()).text() + " " +
                    $(".ui-datepicker-year", $(this).parents()).text();
                var dataMonth = $(this).parent('td').attr('data-month');
                var currentMonth = parseInt(dataMonth) + 1;
                $('#fs-selected-date').html((0 + $(this).text()).slice(-2) + "/" + (0 + currentMonth) + "/" + $(
                    this).parents().attr('data-year'));

                var uid = $("#uid").val();
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


            $("#block-dates td.block-date a").click(function() {
                event.preventDefault();
                var MyDateString = (0 + $(this).text()).slice(-2) + "/" + ('0' + (parseInt($(this).parents().attr(
                    'data-month')) + 1)).slice(-2) + "/" + $(this).parents().attr('data-year');
                var date = $(this).parents().attr('data-year') + '-' + ('0' + (parseInt($(this).parents().attr(
                    'data-month')) + 1)).slice(-2) + '-' + (0 + $(this).text()).slice(-2);
                if ($.inArray(MyDateString, disableddates) > -1) {
                    var dateInfoForm =
                        '<form action="#" method="POST" id="calander_date_info" onsubmit="return false;"><input type="radio" name="availability" value="1" onClick="showMinRate()" id="available"> Available (edit rate)<input type="number" name="min_rate_date" value="" min="1" class="form-control margin-bottom" id="min_rate_date" style="display:none" placeholder="Please enter minimum rate"><br/><input type="radio" name="availability" value="2" onClick="hideMinRate()" id="not_available"> Not available<br/><input type="hidden" name="selected_date" id="selected_date" value="' +
                        date +
                        '" class="form-control margin-bottom"><input type="hidden" name="uid"  value="12671" class="form-control margin-bottom"><a href="javascript:void(0)" onClick="return action_save_date_record()" class="save_date_info">Save</a></form>';
                } else {
                    var dateInfoForm =
                        '<form action="#" method="POST" id="calander_date_info" onsubmit="return false;"><input type="radio" name="availability" value="1" onClick="showMinRate()" id="available"> Available (edit rate) <input type="number" name="min_rate_date" value="" min="1" class="form-control margin-bottom" id="min_rate_date" style="display:none" placeholder="Please enter minimum rate"><br/><input type="radio" name="availability" value="2" onClick="hideMinRate()" id="not_available"> Not available<br/><br/><p><a href="/freelancer/private-job?p-date=' +
                        date +
                        '" title="Add Private job" style="color:blue;">Click here</a> to add private job</p><input type="hidden" name="selected_date" id="selected_date" value="' +
                        date +
                        '" class="form-control margin-bottom"><input type="hidden" name="uid"  value="12671" class="form-control margin-bottom"><a href="javascript:void(0)" onClick="return action_save_date_record()" class="save_date_info">Save</a></form>';
                }
                $.ajax({
                    'url': '/ajax/get-info-about-date',
                    'type': 'POST',
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    'data': {
                        'date': date,
                    },
                    'success': function(result) {
                        if (result.success) {
                            var available_html = '';
                            $('#fs-selected-date').html(MyDateString);
                            available_html +=
                                "<p>This date is currently set as not available to work </p> <p><b>Minimum Rate :</b> £" +
                                result.rate + "</p>";
                            $('#fs-calender-action .modal-body').html(available_html + dateInfoForm);
                            $('#fs-calender-action').modal('show');
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


        function action_save_date_record() {
            var validate = '';
            if ($('#available').is(':checked')) {
                if ($('#min_rate_date').val() == '' || $('#min_rate_date').val() == null) {
                    //alert("Please enter minimum rate");
                    messageBoxOpen('Please enter minimum rate.');
                    $('.alert-modal .modal-footer button.btn.btn-default').removeAttr('onclick');
                    validate = 0;
                    return false;
                } else {
                    validate = 1;
                }
            } else if ($('#not_available').is(':checked')) {
                validate = 1;
            } else {
                //alert("Please select one option from availability");
                messageBoxOpen('Please select one option from availability');
                $('.alert-modal .modal-footer button.btn.btn-default').removeAttr('onclick');
                validate = 0;
                return false;
            }
            if (validate == 1) {
                //$("#loader-div").show(100);
                $('div#fs-calender-action').modal('hide');
                var date_info = $("#calander_date_info").serialize();
                $.ajax({
                    'url': '/ajax/update-calender',
                    'type': 'POST',
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    data: date_info,
                    success: function(result) {
                        console.log(result);
                        $('.ui-dialog').css('display', 'none');
                        //alert("You calendar updated successfully.");
                        //$('#alert-modal').modal('show');
                        $('.alert-modal button.close.hide-pop-up').hide();
                        if (result.availability == 1) {
                            messageBoxOpen('We have updated your calendar with the required rates.');
                            DisableSpecificDates('2024-08-20')
                        } else {
                            messageBoxOpen('Your availability has been updated.');
                        }

                        $('.alert-modal .modal-footer button.btn.btn-default').attr('onclick',
                            "window.location.reload()");
                        //location.reload();
                    }
                });
            }

        }

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
        const income_by_months = @json($income_chart_data);
        const expense_chart_data = @json($expense_chart_data);
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
            var uid = '12671';
            var month = $('#finmonth').val();
            var fiusertype = $('#fiusertype').val();

            var fyid = '424';

            $.ajax({
                'url': '/ajax-request',
                'type': 'POST',
                'data': {
                    'managefinancialyear': 1,
                    'uid': uid,
                    month: month,
                    fyid: fyid,
                    fiusertype: fiusertype
                },
                'success': function(result) {
                    location.reload();
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
            $('.short-job-desc').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
                "order": [
                    [0, "desc"]
                ],
                "columnDefs": [{
                    "targets": [1, 2, 3, 4],
                    "orderable": false
                }]
            });
            $('#current_booking-info').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
                "order": [
                    [0, "desc"]
                ],
                "columnDefs": [{
                    "targets": [1, 2, 3, 4],
                    "orderable": false
                }]
            });
        });
    </script>
@endpush
