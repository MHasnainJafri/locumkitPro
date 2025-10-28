@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="{{ route('employer.dashboard') }}">My Dashboard</a></li>
                        <li><a href="{{ route('employer.job-listing') }}"">JOB MANAGEMENT</a></li>
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
                        @if (session('success'))
                            <h5>{{ session('success') }}</h5>
                        @endif
                        @if (session('error'))
                            <h5>{{ session('error') }}</h5>
                        @endif
                        <hr class="shadow-line">
                    </div>

                    <div class="profile-details">
                        <div class="row">
                            <div class="col-md-12" align="right">
                                <div class="profile-edit-btn listing-page">
                                    <a href="/employer/managejob" class="gradient-threeline post-new-job-btn">Post new job</a>
                                    <a href="/employer/manage-block-freelancer" class="gradient-threeline-orange post-new-job-btn manage-block-list">Manage blocked locum(s)</a>
                                </div>
                            </div>
                        </div>

                        <div class="job-list-div">
                            <div class="job-filter">
                                <ul class="nav nav-tabs">
                                    <li @if ($filterJobStatusId == null) class="active" @endif><a href=" {{ route('employer.job-listing', 'sort_by=job_date&order=DESC') }} ">All</a></li>
                                    <li @if ($filterJobStatusId == 1) class="active" @endif><a href=" {{ route('employer.job-listing', 'filter=waiting&sort_by=job_date&order=DESC') }} ">Waiting</a></li>
                                    <li @if ($filterJobStatusId == 4) class="active" @endif><a href=" {{ route('employer.job-listing', 'filter=accepted&sort_by=job_date&order=DESC') }} ">Accepted</a></li>
                                    <li @if ($filterJobStatusId == 5) class="active" @endif><a href=" {{ route('employer.job-listing', 'filter=completed&sort_by=job_date&order=DESC') }} ">Completed</a></li>
                                    <li @if ($filterJobStatusId == 6) class="active" @endif><a href=" {{ route('employer.job-listing', 'filter=freeze&sort_by=job_date&order=DESC') }} ">Frozen</a></li>
                                    <li class="@if ($filterJobStatusId == 8 && request('filter') === 'cancel') active @endif">
                                        <a href="{{ route('employer.job-listing', ['filter' => 'cancel', 'sort_by' => 'job_date', 'order' => 'DESC']) }}">
                                            Cancelled
                                        </a>
                                    </li>

                                    <li @if ($filterJobStatusId == 2) class="active" @endif><a href=" {{ route('employer.job-listing', 'filter=close&sort_by=job_date&order=DESC') }} ">Expired</a></li>
                                    
                                </ul>
                            </div>

                            <div class="profile-edit job-list-table">
                                <div class="row list-head">
                                    <table class="table-hover table">
                                        <col width="13%">
                                        <col width="13%">
                                        <col width="13%">
                                        <col width="13%">
                                        <col width="13%">
                                        <col width="20%">
                                        <col width="15%">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a
                                                       @if ($job_sort_by_filter == 'job_title' && $job_filter_order == 'desc') href="{{ route('employer.job-listing', 'sort_by=job_title&order=ASC') }}" @else href="{{ route('employer.job-listing', 'sort_by=job_title&order=DESC') }}" @endif>
                                                        Job Title
                                                        @if ($job_sort_by_filter == 'job_title')
                                                            @if ($job_filter_order == 'desc')
                                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-caret-up" aria-hidden="true"></i>
                                                            @endif
                                                        @endif
                                                    </a>
                                                </th>
                                                <th>
                                                    <a
                                                       @if ($job_sort_by_filter == 'job_date' && $job_filter_order == 'desc') href="{{ route('employer.job-listing', 'sort_by=job_date&order=ASC') }}" @else href="{{ route('employer.job-listing', 'sort_by=job_date&order=DESC') }}" @endif>
                                                        Job Date @if ($job_sort_by_filter == 'job_date')
                                                            @if ($job_filter_order == 'desc')
                                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-caret-up" aria-hidden="true"></i>
                                                            @endif
                                                        @endif </a>
                                                </th>
                                                <th>
                                                    <a
                                                       @if ($job_sort_by_filter == 'created_at' && $job_filter_order == 'desc') href="{{ route('employer.job-listing', 'sort_by=created_at&order=ASC') }}" @else href="{{ route('employer.job-listing', 'sort_by=created_at&order=DESC') }}" @endif>
                                                        Date Posted @if ($job_sort_by_filter == 'created_at')
                                                            @if ($job_filter_order == 'desc')
                                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-caret-up" aria-hidden="true"></i>
                                                            @endif
                                                        @endif </a>
                                                </th>
                                                <th>
                                                    <a
                                                       @if ($job_sort_by_filter == 'job_rate' && $job_filter_order == 'desc') href="{{ route('employer.job-listing', 'sort_by=job_rate&order=ASC') }}" @else href="{{ route('employer.job-listing', 'sort_by=job_rate&order=DESC') }}" @endif>
                                                        Job Rate @if ($job_sort_by_filter == 'job_rate')
                                                            @if ($job_filter_order == 'desc')
                                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-caret-up" aria-hidden="true"></i>
                                                            @endif
                                                        @endif </a>
                                                </th>
                                                <th>Job Status</th>
                                                <th>Locum Names</th>
                                                <th>Action <span class="action-note">(edit/view/cancel/delete)</span><br />
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($jobs as $job)
                                                <tr @if ($job->job_date < today()) class="new-date" @endif>
                                                    <td>
                                                        <a href="javascript:void()" title="{{ $job->job_title }}"> {{ $job->job_title }} </a>
                                                    </td>
                                                    <td>
                                                        <span @if ($job->job_date < today()) class="new-date" @else class="old-date" @endif> {{ $job->job_date->format('d/m/Y') }} </span>
                                                    </td>
                                                    <td> {{ $job->created_at->format('d-m-Y') }} </td>
                                                    <td> {{ set_amount_format($job->job_rate) }} </td>
                                                    <td> {!! $job->job_status_html !!} </td>
                                                    <td> {!! $job->freelancerName !!} </td>
                                                    <td class="table-job-info action-btn">
                                                        <ul>
                                                            <li> {!! $job->job_edit_link !!} </li> {!! $job->job_duplicate_link !!}
                                                            <li> {!! $job->job_view_link !!} </li>
                                                            <li> {!! $job->job_cancel_link !!} </li> {!! $job->job_delete_link !!}
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if (sizeof($jobs) > 0)
                                    <div class="paginator-holder">
                                        {{ $jobs->links() }}
                                    </div>
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

@push('scripts')
    <script type="text/javascript">
        function delete_post(id) {
            $('div#alert-confirm-modal #alert-message').html('Do you really want to delete this job?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#loader-div").show();
                $.ajax({
                    'url': `/ajax/employer/delete-job-listing/${id}`,
                    'type': 'DELETE',
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    'success': function() {
                        $("#loader-div").hide();
                        $('div#alert-confirm-modal').removeClass('in');
                        $('div#alert-confirm-modal').css('display', 'none');
                        messageBoxOpen("Job is deleted.");
                    }
                });
                messageBoxClose();
            });
        }

        function update_action(id, action) {
            $('div#alert-confirm-modal #alert-message').html('Are you sure you want to ' + action + ' posting?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#loader-div").show();
                $.ajax({
                    'url': '/job-listing',
                    'type': 'POST',
                    'data': {
                        j: id,
                        action: action
                    },
                    'success': function(result) {
                        location.reload();
                    }
                });
                messageBoxClose();
            });
        }




        function delete_all_post() {
            var result = confirm("Do you really want to delete post?");
            var checked_inpt = $("#del_job_post:checked").length;
            var result22 = '';
            if (result === true && checked_inpt > 0) {

                var checkboxes = document.getElementsByTagName('input');
                if (checkboxes) {
                    $("#loader-div").show();
                    for (var i = 1; i < checkboxes.length; i++) {
                        if (checkboxes[i].type == 'checkbox') {
                            //checkboxes[i].checked = true;
                            if (checkboxes[i].checked == true && checkboxes[i].value != "") {
                                checkedValue = parseInt(checkboxes[i].value);
                                result22 += checkedValue + ',';
                            }
                        }
                    }
                }
                //alert(result22);
                $.ajax({
                    'url': '/job-listing',
                    'type': 'POST',
                    'data': {
                        job_id_list: result22
                    },
                    'success': function(result) {
                        alert("Job post deleted.");
                        location.reload();
                    }
                });
            }
            if (result === false && checked_inpt > 0) {
                $("#loader-div").hide();
            }
        }
        /*$(function() {
            $("#datepicker").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    dateFormat: "yy/mm/dd",
                    yearRange: '2016:2017'});
          });*/
        function checkAll(ele) {
            if (document.getElementById('delete-all-check').checked) {
                $('#delete-all-title').text('Delete All');
            } else {
                $('#delete-all-title').text('Delete Selected Job');
            }

            var checkboxes = document.getElementsByTagName('input');
            if (ele.checked) {
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = true;
                    }
                }
            } else {
                for (var i = 0; i < checkboxes.length; i++) {
                    console.log(i)
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = false;
                    }
                }
            }
        }

        var url = window.location.href;
        if (url.indexOf('?') == -1) {
            url += '?sort_by=job_date&order=DESC';
            window.location.href = url;
        }
    </script>
@endpush
