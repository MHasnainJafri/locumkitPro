@extends('layouts.user_profile_app')

@section('content')
<style>
    .toastify {
        background-color: green !important;
    }
    
    .pagination-links nav {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .pagination-links .pagination {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 10px;
        }

        .pagination-links .pagination li {
            display: inline-block;
        }

        .pagination-links .pagination li a,
        .pagination-links .pagination li span {
            display: inline-block;
            padding: 8px 20px;
            color: #31b0d5;
            text-decoration: none;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            transition: background-color 0.3s, color 0.3s;
            min-width: 50px;
            text-align: center;
        }

        .pagination-links .pagination li span.current {
            background-color: #31b0d5;
            color: #fff;
            border-color: #31b0d5;
            font-weight: bold;
            cursor: default;
        }


        .pagination-links .pagination li a:hover,
        .pagination-links .pagination li span.current {
            background-color: #31b0d5;
            color: #fff;
            cursor: pointer;
        }

        .pagination-links .pagination li.disabled span {
            color: #6c757d;
            background-color: #e9ecef;
            border-color: #ddd;
            cursor: not-allowed;
        }
        
        .pagination>.active>a,
        .pagination>.active>a:focus,
        .pagination>.active>a:hover,
        .pagination>.active>span,
        .pagination>.active>span:focus,
        .pagination>.active>span:hover {
            z-index: 3;
            color: #fff;
            cursor: default;
            background-color: #31b0d5;
            border-color: #31b0d5;
        }
        
        .custom-btn {
            box-shadow: none !important;
            border-width: 0;
            position: relative;
            font-size: 16px;
            padding: 8px 10px;
            background-color: #00A9E0;
            border-color: transparent;
        }
        
        .form-control {
            height: 43px;
        }
        
        .user-job-list a {
            padding: 9px 10px;
        }
</style>
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="{{ route('freelancer.dashboard') }}">My Dashboard</a></li>
                        <li><a href="{{ route('freelancer.private-job') }}">Private Job</a></li>
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
                        <h3>PRIVATE JOB INFORMATION </h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="primary-content" class="main-content register">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <div class="col-md-12 manage-private-job">

                        <div class="user-job-list" @if (request()->has('p-date')) style="display: none;" @else style="display: block;" @endif>
                            <div class="col-md-2 no-padding-left save-update-btn"><a href="javascript:void(0);" class="save-store-btn" id="add_job_edit">Add New job</a></div>
                            <div class="col-md-4">
                                <form action="" style="display:flex;" class="gap-x-2" method="GET">
                                    <input type="text" class="form-control" name="search" value="{{ request()->query('search') }}" placeholder="Type here to search..." />
                                    <button type="submit" style="margin-left:5px; border-radius:1px !important;" class="custom-btn btn-info py-2">Search</button>
                                </form>
                            </div>
                        </div>
                        

                        <form id="addjob" action="/freelancer/private-job" method="POST" class="margin-top addjob-new" @if (request()->has('p-date')) style="display: block;" @else style="display: none;" @endif>
                            @csrf
                            <div class="col-md-12 margin-top no-padding-left">
                                <h2>Add Private Job</h2>
                            </div>
                            <div class="col-md-12 no-padding-left">
                                <div class="job_block">
                                    <div class="job-details margin-bottom">
                                        <div class="width-full show_add_btn" id="show_add_button"><a href="javascript:void(0);" class="color-blue float-right" id="add_free_job"><i class="fa fa-plus" aria-hidden="true"
                                                   title="Add Employer store"></i></a></div>
                                        <div class="col-md-3">
                                            <input 
                                                type="text" 
                                                name="emp_name[]"  
                                                required 
                                                minlength="4"
                                                maxlength="50"
                                                placeholder="Employer Name" 
                                                class="input-text width-100 required-field_0" 
                                                pattern="^[a-zA-Z\s]+$" 
                                                title="Only alphabets and spaces are allowed" 
                                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                                            />
                                        </div>

                                        <div class="col-md-2"><input type="text" title="Only alphabets and spaces are allowed"  oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" pattern="^[a-zA-Z\s]+$" minlength="4" maxlength="50" name="priv_job_title[]" required placeholder="Job Title" class="input-text width-100 required-field_0"> </div>
                                        <div class="col-md-2"><input type="text" minlength="2" maxlength="6" name="priv_job_rate[]" required placeholder="Enter Job Rate" class="input-text width-100 required-field_0" id="numrate0"></div>
                                        <div class="col-md-2"><input type="text" name="priv_job_location[]" required placeholder="Enter Job Location" class="input-text width-100 required-field_0"></div>
                                        <div class="col-md-2">
                                            <div id="date-dialog"></div>
                                            <input type="text" name="priv_job_start_date[]" @if (request()->has('p-date') && is_valid_date( request()->input('p-date'), ['Y-m-d','d/m/Y','d-m-Y'])) value="{{ get_date_with_default_format(is_valid_date(request()->input('p-date'), ['Y-m-d','d/m/Y','d-m-Y'], true)) }}" @endif required placeholder="Enter Job Start Date"
                                                   class="datepicker input-text width-100 required-field_0 readonly" style="cursor: text;">
                                        </div>


                                        <div class="css_error2 required-field-no_0" style="clear:both;"></div>
                                    </div>
                                </div>
                                <div class="col-md-4 no-padding-left">
                                    <button class="save-store-btn save-prv-btn">Save Job</button>
                                    <a herf="javascript:void(0);" class="save-store-btn" id="cancel_private_job">Cancel</a>
                                </div>
                            </div>
                        </form>

                        <form class="mamage-private-jobform" id="mamageprivatejob" action="/freelancer/private-job" method="post">
                            @csrf
                            @method('PUT')

                            <div class="user-job-list heading-list">
                                <div class="col-xs-3 col-sm-3 col-md-3">
                                    <p>Employer Name</p>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <p>Job Title</p>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <p>Job Rate</p>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <p>Job Location</p>
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2">
                                    <p>Job Start Date</p>
                                </div>
                                <div class="col-xs-1 col-sm-1 col-md-1">
                                    <p style="text-align:right">Action</p>
                                </div>
                            </div>

                            @forelse ($jobs as $job)
                                <div class="user-job-list22">
                                    <input type="hidden" name="job_id[]" value="{{ $job->id }}">
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <input type="text" class="width-100 input-text margin-bottom" name="emp_name[]" value="{{ $job->emp_name }}" required>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2">
                                        <input type="text" class="width-100 input-text margin-bottom" name="priv_job_title[]" value="{{ $job->job_title }}" required>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2">
                                        <input type="text" class="width-100 input-text margin-bottom" name="priv_job_rate[]" value="{{ $job->job_rate }}" required>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2">
                                        <input type="text" class="width-100 input-text margin-bottom" name="priv_job_location[]" value="{{ $job->job_location }}" required>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2">
                                        <input type="text" class="datepicker width-100 input-text margin-bottom" name="priv_job_start_date[]" value="{{ $job->job_date->format('d/m/Y') }}" required>
                                    </div>
                                    <div class="col-xs-1 col-sm-1 col-md-1">
                                        <span class="deleteclass small2 float-right" id="{{ $job->id }}">
                                            <i class="fa fa-times" title="Remove" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            @empty
                                <div class="user-job-list" align="center">No records found.</div>
                            @endforelse
                            <!-- Pagination Links -->
                            <div class="col-md-12">
                                <div class="pagination-links">
                                    {{ $jobs->links() }}
                                </div>
                            </div>
                            <div class="col-md-2 no-padding-left save-update-btn"><button class="save-store-btn">Update</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        var disableddates = @json($userBlockDates);
        var bookdates = @json($bookedDates);
        $(document).ready(datePickerCaller);

        function datePickerCaller() {
            $('.datepicker').each(function() {
                $(this).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    beforeShowDay: DisableSpecificDates,
                    dateFormat: "dd/mm/yy",
                });
            });
        }


        function DisableSpecificDates(date) {
            var string = $.datepicker.formatDate('yy-mm-dd', date);
            var today = $.datepicker.formatDate('yy-mm-dd', new Date());
            if ($.inArray(string, disableddates) > -1) {
                //$('.ui-state-disabled').addClass('blocked-date');
                return [true, "ui-datepicker-unselectable block-date", ""];
            } else if ($.inArray(string, bookdates) > -1) {
                return [true, " ui-datepicker-unselectable ui-state-disabled booked-date", ""];
            } else if (string >= today) {
                return [true, "available-date", ""];
            } else {
                return [true, "available-date", ""];
            }

        }

        $("#add_job_edit").click(function() {
            $(this).hide();
            $("#addjob").show(1000);
            $('#cancel_private_job').show();
        });

        $("#cancel_private_job").click(function() {
            $("#addjob").hide();
            $(this).hide();
            $("#add_job_edit").show();
        });

        var i = $(".job-details").size() + 1;
        var m = 0;
        $("#add_free_job").click(function() {

            if (i > 1) {
                $('.job_block').append('<div class="job-details margin-bottom"><div class="col-md-3"><input type="text" name="emp_name[]" required placeholder="Employer Name" class="input-text width-100 required-field_' + m +
                    '"> </div><div class="col-md-2"><input type="text" name="priv_job_title[]" required placeholder="Job Title" class="input-text width-100 required-field_' + m +
                    '"> </div><div class="col-md-2"><input type="text" name="priv_job_rate[]" required placeholder="Enter Job Rate" class="input-text width-100 required-field_' + m +
                    '"></div><div class="col-md-2"><input type="text" name="priv_job_location[]" required placeholder="Enter job location" class="input-text width-100 required-field_' + m +
                    '"></div><div class="col-md-2"><input type="text" name="priv_job_start_date[]" required placeholder="Enter job start date" class="datepicker input-text width-100 required-field_' + m +
                    '"></div><span class="removeclass small2 float-right"><i class="fa fa-times" title="Remove" aria-hidden="true"></i></span><div class="css_error2 required-field-no_' + m + '" style="clear:both;"></div></div>');
                i++;
                m++;
                datePickerCaller();
            }
            return false;
        });
        $("body").on("click", ".removeclass", function(e) {
            //event.returnValue = false;  // mozilla giving error rest working in safari ,crome and IE
            if (i > 1) {
                $(this).parent('.job-details').remove();
                i--;
            }
        });
        $(".deleteclass").click(function() {
            var id = $(this).attr('id');
            $('div#alert-confirm-modal #alert-message').html('Are you sure you want to delete this private job entry?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                messageBoxClose();
                $('div#loader-div').show();
                $('#delete_box').show();
                $('#delete_box').addClass('in');
                $('#delete_box').css('display', 'block');
                $.ajax({
                    'url': '/ajax/private-job/' + id + '/delete',
                    'type': 'DELETE',
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    'success': function(result) {
                        messageBoxOpen('Private job deleted.');
                        $('.alert-modal .modal-footer button.btn.btn-default').attr('onclick', "window.location.reload()");
                    },
                    complete: function() {
                        $('div#loader-div').hide();
                    }
                });
            });

        });

        $("#numrate0").keyup(function() {
            var in_rate = $("#numrate0").val();
            if (isNaN(in_rate)) {
                $("#numrate0").val('');
            }

        });
    </script>
@endpush
