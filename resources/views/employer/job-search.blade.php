@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/employer/job-listing">Job List</a></li>
                        <li><a href="#">Search List</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-search" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Search List</h3>
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
                        <h1><span>Locum Search Listing</span></h1>
                        <hr class="shadow-line">
                    </div>
                    <div class="job-details">

                        <form action="/employer/invite-for-job/{{ $job->id }}" method="post">
                            @csrf
                            <div class="profile-edit-scroll">
                                <div class="profile-edit">
                                    <div class="col-md-6 searc-re-sult">
                                        <div class="col-md-4 normal_user_list no-padding-left">
                                            <h5>Search Result (<span id="searchCount">{{ count($freelancers) }}</span>)</h5>
                                        </div>

                                        <div class="col-md-6">
                                            <input type="text" id="freelancerSearch" class="form-control" placeholder="Search by ID or Name..." onkeyup="filterFreelancers()">
                                        </div>
                                        <br>
                                    </div>
                                    <div class="col-md-6" align="right">
                                        <div class="job-edit-btn">
                                            <a style="display: inline-block;" href="/employer/managejob/{{ $job->id }}" id="edit_current_job">Edit Job</a>
                                            <a style="display: inline-block;" href="#add_puser" id="add_puser_div">Add Private User</a>

                                            @if (sizeof($freelancers) > 0 || sizeof($private_freelancers) > 0)
                                                <input type="submit" name="invitation_send" class="invite-user-btn" style="padding: 10px 8px !important;" value="Invite">
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-md-12 job_listing_details_table">

                                        <table class="table-hover table" id="freelancerTable">
                                            <colgroup>
                                                <col width="10%">
                                                <col width="20%">
                                                <col width="20%">
                                                <col width="20%">
                                                <col width="10%">
                                            </colgroup>
                                            <thead class="job_listing_heading title-by-search">
                                                <tr>
                                                    <th>
                                                        <h5>
                                                            <input type="checkbox" name="checkinvite_1[]" onclick="checkAll(this);">
                                                        </h5>

                                                    </th>
                                                    <th>
                                                        <h5>
                                                            <a href="/employer/job-search/{{ $job->id }}?sortByID={{ $sort_id }}" title="Sort by User ID">User ID <small style="color:white"> (Name)</small> <i class="fa {{ $sort_id_icon }}" aria-hidden="true"></i></a>
                                                        </h5>
                                                    </th>
                                                    <th>
                                                        <h5>
                                                            <a href="/employer/job-search/{{ $job->id }}?sortByCancelRate={{ $sort_canrate }}" title="Cancellation Rate">Cancellation Rate <i class="fa {{ $sort_canrate_icon }}"
                                                                   aria-hidden="true"></i> </a>
                                                        </h5>
                                                    </th>
                                                    <th>
                                                        <h5>
                                                            <a href="/employer/job-search/{{ $job->id }}?sortByFeedAvg={{ $sort_feed }}" title="Feedback Avg">Feedback Avg <i class="fa {{ $sort_feed_icon }}" aria-hidden="true"></i> </a>
                                                        </h5>
                                                    </th>

                                                    <th>
                                                        <h5>
                                                            <a href="javascript:void();" title="Feedback Avg">CET Point</a>
                                                        </h5>
                                                    </th>

                                                    <th style="text-align: center;">
                                                        <h5>
                                                            <a href="javascript:void();" title="View Applicant">Action</a>
                                                        </h5>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($freelancers as $freelancer)
                                                    <tr class="">
                                                        <td>
                                                            <input type="checkbox" class="checkinvite" name="checkinvite[]" value="{{ $freelancer->id }}">
                                                        </td>
                                                        <td> {{ $freelancer->id }} - {{$freelancer->firstname.' '.$freelancer->lastname}} </td>
                                                        <td> {{ $freelancer->job_cancellation_rate }}% </td>
                                                        <td>
                                                            <a target="_blank" href="/employer/feedback-report/{{ encrypt($freelancer->id) }}"> {{ $freelancer->overall_feedback_rating }}% </a>
                                                        </td>
                                                        <td> {{ $freelancer->user_extra_info->cet ?? '' ? $freelancer->user_extra_info->cet ?? '' : 'N/A' }} </td>
                                                        <td style="text-align: center">
                                                            <a href="javascript:void(0);" class="color-red" onClick="view_user('{{ $freelancer->id }}')" title="View Locum" alt="View Locum">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if (sizeof($freelancers) == 0)
                                        <div class="col-md-12 no_serach_result" align="center">
                                            <p class="record_not_found">No matching locum.
                                                <a href="/employer/managejob/{{ $job->id }}"><i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="delete_post('{{ $job->id }}')"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="profile-edit-scroll">
                                <div class="profile-edit fs-private-locum">

                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="private_user_head no-padding-left">
                                            <h5>Private User List</h5>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 job_listing_heading">
                                        <div class="user_list_title_bar">
                                            <div class="col-xs-1 col-sm-1 col-md-1">
                                                <h5>
                                                    <p><input type="checkbox" name="checkinvitep2[]" onclick="checkAllPrivateLocum(this);"></p>
                                                </h5>
                                            </div>
                                            <div class="col-xs-3 col-sm-3 col-md-3">
                                                <h5>Name</h5>
                                            </div>
                                            <div class="col-xs-5 col-sm-5 col-md-5">
                                                <h5>Email</h5>
                                            </div>
                                            <div class="col-xs-2 col-sm-2 col-md-2">
                                                <h5>
                                                    <p>Phone</p>
                                                </h5>
                                            </div>
                                            <div class="col-xs-1 col-sm-1 col-md-1">
                                                <h5>
                                                    <p>Action</p>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="private_user_list-item">
                                        @forelse ($private_freelancers as $freelancer)
                                            <div class="col-md-12" id="p-user-{{ $freelancer->id }}">
                                                <div class="col-xs-1 col-sm-1 col-md-1">
                                                    <input type="checkbox" class="checkinvitep" name="checkinvitep[]" value="{{ $freelancer->id }}">
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3"><span> {{ $freelancer->name }} </span>
                                                </div>
                                                <div class="col-xs-5 col-sm-5 col-md-5"> {{ $freelancer->email }} </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2"> {{ $freelancer->mobile }} </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1">
                                                    <a href="javascript:void(0);" onclick="delete_puser('{{ $freelancer->id }}')">
                                                        <i class="fa fa-trash-o" title="Delete private user" alt="Delete private user" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-xs-12 col-sm-12 col-md-12 no_serach_result">
                                                <p class="record_not_found">No private locum found.</p>
                                            </div>
                                        @endforelse

                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                    <div class="private_user margin-top" style="display:none;">
                        <form id="add_puser" name="add_puser" action="/employer/store-private-users" method="post">
                            @csrf
                            <div class="col-md-12 margin-bottom heading_add">
                                <div class="add_private_user_table">
                                    <h5>Add Private User</h5>
                                </div>
                            </div>
                            <div class="col-md-12 list_div margin-top">
                                <div class="col-md-12 add_div">
                                    <div class="col-md-4 margin-bottom no-padding-left">
                                        <div class="col-md-2 no-padding-left" style="line-height: 30px;">
                                            Name
                                        </div>
                                        <div class="col-md-8 no-padding-left-right">
                                            <input type="text" 
                                                   name="private_user_name[]" 
                                                   placeholder="Enter name" 
                                                   class="form-control margin-bottom" 
                                                   minlength="4"
                                                   maxlength="20"
                                                   oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" 
                                                   required>
                                        </div>
                                    </div> 


                                    <div class="col-md-4 margin-bottom">
                                        <div class="col-md-2 no-padding-left" style="line-height: 30px;">
                                            Email
                                        </div> 
                                        <div class="col-md-8 no-padding-left-right">
                                            <input type="email" name="private_user_email[]" placeholder="Enter email" class="form-control margin-bottom" required value="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 margin-bottom">
                                        <div class="col-md-3 no-padding-left" style="line-height: 30px;">
                                            Mobile
                                        </div>
                                        <div class="col-md-8 no-padding-left-right">
                                            <input type="text" name="private_user_mobile[]" placeholder="Enter mobile" class="form-control margin-bottom numbersOnly" maxlength="11" required>
                                        </div>
                                    </div>
                                    <div class="col-md-1"><a href="javascript:void(0);" class="color-red" id="add_privateuser"><i class="fa fa-plus" aria-hidden="true" title="Add Locum"></i></a></div>
                                </div>
                            </div>
                            <div class="col-md-12 privet-save-btn-wrap">
                                <div class="col-md-offset-6 col-md-6" align="right">
                                    <input type="submit" name="add_user" class="gradient-threeline" value="Save">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="detail_box" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header no-border-bottom">
                        <button type="button" class="close" style="color: white; font-weight: bold;opacity: 1;" data-dismiss="modal" onclick="close_dive('detail_box');">&times;</button>
                        <h4 class="modal-title">Locum Details</h4>
                    </div>
                    <div class="modal-body" id="fre_details">
                        <h3 id="load_fre_details" style="display:none"><img src="../public/frontend/locumkit-template/img/loader.gif"> Please wait... </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- private users del form --}}
    @foreach ($private_freelancers as $freelancer)
        <form id="private-user-del-form-{{ $freelancer->id }}" action="/employer/delete-private-user/{{ $freelancer->id }}" method="post">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection

@push('scripts')
    <script type="text/javascript">
        function open_modal(id) {
            if (id != '') {
                //Logic to copy the item
                $("#emp_details").html($("#open_" + id).html())

                $('#detail_box').show();
                $('#detail_box').addClass('in');
                $('#detail_box').css('display', 'block');
            }
        }

        function close_dive(id) {
            $("#" + id).hide();
            $('.modal-backdrop').hide();
            //location.reload();
        }

        function checkAll(ele) {
            var checkboxes = jQuery('table.table input');
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

        function checkAllPrivateLocum(ele) {
            var checkboxes = jQuery('div.fs-private-locum input');
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
    </script>

    <script type="text/javascript">
        function delete_puser(id) {
            $('div#alert-confirm-modal #alert-message').html('Do you really want to delete private user?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#private-user-del-form-" + id).submit();
            });
        }

        function delete_post(id) {
            //var result = confirm("Do you really want to delete post?");
            $('div#alert-confirm-modal #alert-message').html('Do you really want to delete post?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $.ajax({
                    'url': '/job-search',
                    'type': 'POST',
                    'data': {
                        job_id: id
                    },
                    'success': function(result) {
                        //alert("Job post deleted.");
                        messageBoxClose();
                        messageBoxOpen('Job post deleted.');
                        window.setTimeout(function() {
                            window.location = "/job-listing/{{ $job->id }}";
                        }, 2000);
                    }
                });
            });
        }

        function view_user(id) {
            $("#fre_details").html('<h3 id="load_fre_details" ><img src="../public/frontend/locumkit-template/img/loader.gif"> Please wait... </h3>');
            open_modal(id);
            $.ajax({
                'url': `/ajax/employer/view-applicant-information/${id}`,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                },
                'type': 'POST',
                'success': function(result) {
                    $('#load_fre_details').hide();
                    $("#fre_details").html(result.html);
                }
            });

        }
        // for adding private user 

        $("#add_puser_div").click(function() {
            $(".private_user").show();
        });
        /// add private user
        var i = $(".add_div").size() + 1;
        $("#add_privateuser").click(function() {
            //alert('I am here');
            if (i > 1) {
                $('.list_div').append(
                    '<div class="col-md-12 add_div"><div class="col-md-4 margin-bottom no-padding-left"><div class="col-md-2 no-padding-left">Name</div><div class="col-md-8 no-padding-left-right"><input type="text" name="private_user_name[]" placeholder="Enter name" class="form-control margin-bottom" required></div></div><div class="col-md-4 margin-bottom"><div class="col-md-2 no-padding-left">Email</div><div class="col-md-8 no-padding-left-right"><input type="email" name="private_user_email[]" placeholder="Enter email" class="form-control margin-bottom" value="admin@locumkit.com" required onclick="empty_email(this);"></div></div><div class="col-md-3 margin-bottom"><div class="col-md-3 no-padding-left">Mobile</div><div class="col-md-8 no-padding-left-right"><input type="text" name="private_user_mobile[]" placeholder="Enter mobile" class="form-control margin-bottom numbersOnly2" required maxlength="11"></div></div><span class="removeclass small2 remove-p-user"><i class="fa fa-times" aria-hidden="true" title="remove" alt="remove"></i></span></div>'
                );
                i++;
                $('.numbersOnly2').keyup(function() {
                    if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
                        this.value = this.value.replace(/[^0-9\.]/g, '');
                    }
                });
            }
            return false;
        });

        $("body").on("click", ".removeclass", function(e) {
            //event.returnValue = false;  // mozilla giving error rest working in safari ,crome and IE
            if (i > 1) {
                $(this).parent('.add_div').remove();
                i--;
            }
        });

        function empty_email(ele) {
            $(ele).val('');
        }
    </script>

    <script>
        $('.numbersOnly').keyup(function() {
            if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
                this.value = this.value.replace(/[^0-9\.]/g, '');
            }
        });
    </script>
    <script>
        document.getElementById('freelancerSearch').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.job_listing_details_table tbody tr');
            let visibleCount = 0;
    
            rows.forEach(row => {
                const userIdCell = row.cells[1]; // User ID and name cell
                if (!userIdCell) return;
    
                const text = userIdCell.textContent.toLowerCase();
                const match = text.includes(searchValue);
    
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
    
            document.getElementById('searchCount').textContent = visibleCount;
        });
    </script>

@endpush
