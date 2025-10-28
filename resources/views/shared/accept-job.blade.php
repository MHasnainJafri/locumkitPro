@extends('layouts.user_profile_app')

@push('styles')
    <style>
        .sj-term-div {
            background: #fff;
            float: left;
            width: 100%;
        }

        .sj-term-div table tbody tr td ul li {
            list-style: disc !important;
            margin: 5px 0 5px 30px;
        }

        .sj-term-div table tbody tr:first-child th {
            background-color: #8cc700 !important;
            background: -webkit-linear-gradient(#7aae00, #8cc700, #7aae00) !important;
            background: -o-linear-gradient(#7aae00, #8cc700, #7aae00) !important;
            background: -moz-linear-gradient(#7aae00, #8cc700, #7aae00) !important;
            background: linear-gradient(#7aae00, #8cc700, #7aae00) !important;

            margin: 0;
            color: #fff;
            border: 1px solid #ccc;
            height: 48px;
            font-size: 18px;
            padding-left: 20px !important;
        }

        .sj-term-div table th,
        .sj-term-div table td {
            border: 1px solid #dddddd !important;
            border-right: 1px solid #24a9e0 !important;
            border-left: 1px solid #24a9e0 !important;
        }

        .sj-term-div table tr:last-child td {
            border-bottom: 1px solid #24a9e0 !important;
        }

        .table>tbody>tr>th {
            position: relative;
        }

        .sj-term-div table tbody td p {
            text-transform: initial !important;
            height: auto !important;
            font-weight: normal !important;
            padding: inherit !important;
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
                        <li><a href="javascript:void(0)"> Job Details </a></li>
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
                        <h3>Job Details</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content profiles">
        <div class="container">
            <div class="row">
                <div class="job-offer job-accept-wrap contents">
                    <div class="welcome-heading">
                        <h1><span>Job</span> Details </h1>
                        <hr class="shadow-line">
                    </div>
                    <div class="job-info">
                        <section>
                            <h1 class="successfull_msg" id="notification_msg">
                                @if ($success)
                                    <div class="notification success">{{ $success }}</div>
                                @endif
                                @if ($error)
                                    <div class="notification error">{{ $error }}</div>
                                @endif
                            </h1>
                        </section>

                        <div class="accept-job-div">
                            <table class="table-striped table" width="100%">
                                <tr>
                                    <th class="heading" colspan="2"> Job confirmation (Key Details)</th>
                                </tr>
                                <tr>
                                    <th class="normal">Date</th>
                                    <td> {{ get_date_with_default_format($job->job_date) }} </td>
                                </tr>
                                <tr>
                                    <th class="normal">Daily Rate</th>
                                    <td>
                                        {{ set_amount_format($job->job_rate) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="normal">Store Contact Details</th>
                                    <td> {{ $store_contact_details }} </td>
                                </tr>
                                <tr>
                                    <th class="normal">Store Address</th>
                                    <td> {{ $job->job_address . ', ' . $job->job_region . ', ' . $job->job_zip }} </td>
                                </tr>
                                <tr>
                                    <th class="normal">Additional Booking Info</th>
                                    <td class="job-desc-td" style="color:red; font-weight:bold;"> {{ $job->job_post_desc }} </td>
                                </tr>
                            </table>
                            <br>
                            <table class="table-striped table" width="100%">
                                <tr>
                                    <th class="heading" colspan="2"> Booking confirmation (additional information)</th>
                                </tr>
                                <tr>
                                    <th class="normal">Start Time</th>
                                    <td> {{ $job->get_store_start_time() }} </td>
                                </tr>
                                <tr>
                                    <th class="normal">Finish Time</th>
                                    <td> {{ $job->get_store_finish_time() }} </td>
                                </tr>
                                <tr>
                                    <th class="normal">Lunch Break (minutes)</th>
                                    <td> {{ $job->get_store_lunch_time() }} </td>
                                </tr>
                                @foreach ($employer_answers as $user_answer)
                                    <tr>
                                        <th class="normal"> {{ $user_answer->question->employer_question }} </th>
@php
    $decoded = json_decode($user_answer->type_value, true);
@endphp
<td>{{ is_array($decoded) ? implode(' / ', $decoded) : $user_answer->type_value }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <th class="normal">Store cancellation percentage</th>
                                    <td> {{ get_job_cancellation_rate_by_user($job->employer_id, 'employer') }} </td>
                                </tr>
                                <tr>
                                    <th class="normal">Store feedback</th>
                                    <td> {{ get_overall_feedback_rating_by_user($job->employer_id, 'employer') }} </td>
                                </tr>

                            </table>
                            <br>

                            @if ($freelancer_type == 'live')
                                <table class="table-striped table" width="100%">
                                    <tr>
                                        <th class="heading" colspan="2"> Job Invitation – Information you provided us </th>
                                    </tr>
                                    <tr>
                                        <th class="headingcolor:red; font-weight:bold;" colspan="2">Please check the details below and advise us immediately if this information is incorrect</th>
                                    </tr>
                                    @if ($freelancer->goc)
                                        <tr>
                                            <th class="normal">GOC Number</th>
                                            <td> {{ $freelancer->goc }} </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="normal">AOP Membership Number</th>
                                        <td>{{ $freelancer->aop }}</td>
                                    </tr>
                                    @if ($freelancer->inshurance_company && $freelancer->inshurance_no)
                                        <tr>
                                            <th class="normal">Insurance</th>
                                            <td>{{ ucfirst($freelancer->inshurance_company) . '-' . $freelancer->inshurance_no }} </td>
                                        </tr>
                                        <tr>
                                            <th class="normal">Insurance expiry</th>
                                            <td> {{ $freelancer->inshurance_renewal_date }} </td>
                                        </tr>
                                    @endif
                                    @foreach ($freelancer->user_answers as $user_answer)
                                        <tr>
                                            <th class="normal"> {{ $user_answer->question->freelancer_question }} </th>
                                            <td> {{ json_decode($user_answer->type_value) ? join(' / ', json_decode($user_answer->type_value)) : $user_answer->type_value }} </td>
                                        </tr>
                                    @endforeach
                                </table>
                                <br />
                            @endif
                            <div class="sj-term-div"> {!! get_locum_email_terms() !!} </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
