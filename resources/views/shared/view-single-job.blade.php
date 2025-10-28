@extends('layouts.user_profile_app')
@section('content')

    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        @if (Auth::check())
                            @if (Auth::user()->user_acl_role_id == 2)
                                <li><a href="/freelancer/job-listing">List Page</a></li>
                            @else
                                <li><a href="/employer/job-listing">List Page</a></li>
                            @endif
                        @endif
                        <li><a href="#">JOB INFORMATION</a></li>
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
                        <h3>BOOKING INFORMATION </h3>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div id="primary-content" class="main-content profiles">
        <div class="container">
            <div class="row">
                <div class="gray-gradient contents">
                    <div class="welcome-heading">
                        <h1><span># {{ $job->id }} </span></h1>
                        <hr class="shadow-line">
                    </div>
                    <div style="clear: both"></div>

                    <div class="job-view job-view-scroll">
                        <div class="general-info">
                            <h4 class="job_bar_color_{{ $type_jst }}">{{ $setTitle1 }} </h4>
                            <table class="clickable table-striped table-hover table">
                                <colgroup>
                                    <col width="30%">
                                    <col width="70%">
                                </colgroup>
                                <tbody>
                                    @if ($job['job_status'] == 2 || $job['job_status'] == 4)
                                        <tr>
                                            <th width="30%">Date</th>
                                            <td> {{ get_date_with_default_format($job->job_date) }} </td>
                                        </tr>
                                        <tr>
                                            <th>Daily Rate</th>
                                            <td> {{ set_amount_format($job->job_rate) }} </td>
                                        </tr>
                                        <tr>
                                            <th>Store Contact Details</th>
                                            <td>
                                                @if ($job->employer && $job->employer->user_extra_info)
                                                    <?php echo $store_contact_details; ?>
                                                @else
                                                    No store contact detail available.
                                                @endif
                                            </td>
                                            <!--<td><?php echo $store_contact_details; ?></td>-->
                                        </tr>
                                        <tr>
                                            <th>Store Address</th>
                                            <td> {{ $job->job_address . ', ' . $job->job_region . ', ' . $job->job_zip }} </td>
                                        </tr>
                                        <tr style="display:none;">
                                            <th>Job Status</th>
                                            <td> {!! $job_status_html !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Additional Info</th>
                                            <td style="color:red;font-weight:bold;"> {{ $job->job_post_desc }} </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <th>Job Ref</th>
                                            <td> {{ $job->job_id }} </td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td> {{ get_date_with_default_format($job->job_date) }} </td>
                                        </tr>
                                        <tr>
                                            <th>Daily Rate</th>
                                            <td> {{ set_amount_format($job->job_rate) }} </td>
                                        </tr>
                                        @if (sizeof($job->job_post_timelines) > 0)
                                            <tr>
                                                <th>If increase rate timeline</th>
                                                <td>
                                                    @foreach ($job->job_post_timelines as $timeline)
                                                        <p><strong>Date:</strong> {{ $timeline->job_date_new }} <strong>Rate:</strong> {{ set_amount_format($timeline->job_rate_new) }} </p>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Store Contact Details</th>
                                            <td>
                                                @if ($job->employer && $job->employer->user_extra_info)
                                                    <?php echo $store_contact_details; ?>
                                                @else
                                                    No store contact detail available.
                                                @endif
                                            </td>
                                            <!--<td><?php echo $store_contact_details; ?></td>-->
                                        </tr>
                                        <tr>
                                            <th>Store Address</th>
                                            <td> {{ $job->job_address . ', ' . $job->job_region . ', ' . $job->job_zip }} </td>
                                        </tr>
                                        <tr>
                                            <th>Date Posted </th>
                                            <td> {{ get_date_with_default_format($job->created_at) }} </td>
                                        </tr>

                                        <tr>
                                            <th>Additional Info</th>
                                            <td style="color:red;font-weight:bold;"> {{ $job->job_post_desc }} </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if ($job->job_status == 1)
                            <div class="general-info" style="overflow:hidden;">
                                <h4 class="job_bar_color_{{ $type_jst }}"> {{ $setTitle2 }} </h4>

                                <table class="clickable table-striped table-hover table">
                                    <colgroup>
                                        <col width="30%">
                                        <col width="70%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th><b>Start Time</b> </th>
                                            <td> {{ $job->get_store_start_time() }} </td>
                                        </tr>
                                        <tr>
                                            <th><b>Finish Time</b> </th>
                                            <td> {{ $job->get_store_finish_time() }} </td>
                                        </tr>
                                        <tr>
                                            <th><b>Lunch Break (minutes)</b> </th>
                                            <td> {{ $job->get_store_lunch_time() }} </td>
                                        </tr>

                                        {!! get_question_answer_rows() !!}

                                    </tbody>
                                </table>

                            </div>
                        @endif
                        <div class="margin-top"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
