@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="{{ route('freelancer.dashboard') }}">My Dashboard</a></li>
                        <li><a href="{{ route('freelancer.job-listing') }}"">JOB MANAGEMENT</a></li>
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
                        <h3>Job List Page</h3>
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
                        <h1><span>JOB MANAGEMENT</span></h1>
                        <hr class="shadow-line">
                    </div>

                    <div class="profile-details">
                        <div class="row">
                            <div class="col-md-12" align="right">
                                <div class="profile-edit-btn" style="padding-top: 0;">
                                    <a href="/freelancer/private-job" class="gradient-threeline post-new-job-btn">Manage Private Job(s)</a>
                                </div>
                            </div>
                        </div>

                        <div class="job-list-div">
                            <div class="job-filter">
                                <ul class="nav nav-tabs">
                                    <li @if ($filterJobStatusId == null) class="active" @endif><a href=" {{ route('freelancer.job-listing', 'sort_by=job_date&order=DESC') }} ">All</a></li>
                                    <li @if ($filterJobStatusId == 4) class="active" @endif><a href=" {{ route('freelancer.job-listing', 'filter=accepted&sort_by=job_date&order=DESC') }} ">Accepted</a></li>
                                    <li @if ($filterJobStatusId == 5) class="active" @endif><a href=" {{ route('freelancer.job-listing', 'filter=completed&sort_by=job_date&order=DESC') }} ">Completed</a></li>
                                    <li @if ($filterJobStatusId == 8) class="active" @endif><a href=" {{ route('freelancer.job-listing', 'filter=cancel&sort_by=job_date&order=DESC') }} ">Cancelled</a></li>
                                </ul>
                            </div>

                            <div class="profile-edit job-list-table">
                                <div class="row list-head">
                                    <table class="table-hover table">
                                        <col width="13%">
                                        <col width="13%">
                                        <col width="20%">
                                        <col width="20%">
                                        <col width="20%">
                                        <col width="10%">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a href="/freelancer/job-listing?sort_by=job_date&order={{ $job_filter_order == 'asc' ? 'DESC' : 'ASC' }}">Job date
                                                        @if ($job_sort_by_filter == 'job_date' && $job_filter_order == 'desc')
                                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                        @else
                                                            <i class="fa fa-caret-up" aria-hidden="true"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="/freelancer/job-listing?sort_by=job_rate&order={{ $job_filter_order == 'asc' ? 'DESC' : 'ASC' }}">Rate
                                                        @if ($job_sort_by_filter == 'job_rate' && $job_filter_order == 'desc')
                                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                        @else
                                                            <i class="fa fa-caret-up" aria-hidden="true"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>Store Name</th>
                                                <th>Location</th>
                                                <th>Status</th>
                                                <th>Action<br>
                                                    <span class="action-note">(view)</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($jobs as $job)
                                                <tr @if ($job->job_date < today()) class="new-date" @endif>
                                                    <td>
                                                        <span @if ($job->job_date < today()) class="new-date" @else class="old-date" @endif> {{ get_date_with_default_format($job->job_date) }} </span>
                                                    </td>
                                                    <td>
                                                        {{ set_amount_format($job->job_rate) }}
                                                    </td>
                                                    <td>
                                                        {{ $job->job_store->store_name }}
                                                    </td>
                                                    <td>
                                                        {{ $job->job_store->store_address }}
                                                    </td>
                                                    <td> {!! $job->job_status_html !!} </td>
                                                    <td class="table-job-info action-btn">
                                                        <ul>
                                                            <li> {!! $job->job_view_link !!} </li>
                                                            <li> {!! $job->cancel_action_link !!} </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if (sizeof($jobs) > 0)
                                    {{ $jobs->links() }}
                                @else
                                    <h4 class='record_not_found'>No records were found.</h4>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
