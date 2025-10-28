@extends('admin.layout.app')
@section('content')
<style>
    .d-none {
        display: none !important;
    }

    .d-block {
        display: block !important;
    }
    .active{
        background: #00A9E0 !important;
        border-top: 1px solid #855D10 !important;        
    }
</style>
<div class="main-container container">
    @include('admin.config.sidebar')

    <div class="col-lg-12 main-content">
        <div id="breadcrumbs" class="breadcrumbs">
            <div id="menu-toggler-container" class="hidden-lg">
                <span id="menu-toggler">
                    <i class="glyphicon glyphicon-new-window"></i>
                    <span class="menu-toggler-text">Menu</span>
                </span>
            </div>
            <ul class="breadcrumb">
            </ul>
        </div>

        <div class="page-content" style="margin-top: -10px">
            <div id="tabs">

                <div class="qus-tabs financead">
                        <div class="form-group pull-left">
                            <div class="input-group pull-left">
                            <input type="date" id="startdate" style="padding: 5px; margin-right: 20px; width: 20%;">                            
                            <input type="date" id="enddate" style="padding: 5px; margin-right: 20px; width: 20%;">
                                <button class="btn btn-info" onclick="getPrint()">Export</button>
                            </div>
                        </div>
                </div>
            </div>
            <div id="fre-tab">
                <table class="table clickable table-striped table-hover">
                    <colgroup>
                        <col width="10%">
                        <col width="2%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="20%">
                        <col width="20%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">User ID</th>
                            <th class="text-center">Employer </th>
                            <th class="text-center">Jobs listed</th>
                            <th class="text-center">Jobs accepted</th>
                            <th class="text-center">Success rate</th>
                            <th class="text-center">Cancel rate</th>
                            <th class="text-center">Number of Private job requests sent</th>
                            <th class="text-center">See Detailed reports</th>
                        </tr>
                    </thead>
                    <tbody class="" id="render_locam">
                        @include('admin.reports.employerJobReportPartials')
                        
                    </tbody>                    
                </table>
                <div class="pagination">
                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
                    <p class="clearfix">
                    </p>
                    <ul class="paginator-div">
                    </ul>
                    <p></p>
                </div>
            </div>
            <script>

                function getPrint(){
                    
                    var UserIdInputs = document.getElementsByName("user_id[]");
                    var employer = document.getElementsByName("employer[]");
                    var jobs_listed = document.getElementsByName("jobs_listed[]");
                    var jobs_accepted = document.getElementsByName("jobs_accepted[]");
                    var success_rate = document.getElementsByName("success_rate[]");
                    var cancel_rate = document.getElementsByName("cancel_rate[]");
                    var private_job_sent = document.getElementsByName("private_job_sent[]");

                    var data = [];
                    
                    for (var i = 0; i < UserIdInputs.length; i++) {
                        var entry = {
                            user_id: UserIdInputs[i].value,
                            employer: employer[i].value,
                            jobs_listed: jobs_listed[i].value,
                            jobs_accepted: jobs_accepted[i].value,
                            success_rate: success_rate[i].value,
                            cancel_rate: cancel_rate[i].value,
                            private_job_sent: private_job_sent[i].value,
                        };
                        data.push(entry);
                    }

                    var form = document.createElement('form');
                    form.action = "{{ route('report.EmployerJobReport.export') }}";
                    form.method = 'POST';
                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = "{{ csrf_token() }}";
                    form.appendChild(csrfInput);
                    var dataInput = document.createElement('input');
                    dataInput.type = 'hidden';
                    dataInput.name = 'data';
                    dataInput.value = JSON.stringify(data); 
                    form.appendChild(dataInput);
                    document.body.appendChild(form);
                    form.submit();
                }

                
                $(document).ready(function () {
                    $("#enddate").on("change", function () {
                        var startDateValue = $("#startdate").val();
                        var endDateValue = $(this).val();

                        if (startDateValue && endDateValue) {
                            $.ajax({
                                url: '{{route("report.EmployerJobReport")}}',
                                method: 'GET',
                                data: {
                                    startdate: startDateValue,
                                    enddate: endDateValue
                                },
                                success: function (response) {
                                    $('#render_locam').html(response.html);
                                },
                                error: function (error) {
                                    console.error(error);
                                }
                            });
                        }
                    });
                });
            </script>
            <script type="text/javascript">
                Gc.initTableList();

                function changeUserNameOrder(order) {
                    $.ajax({
                        'url': '/admin/config/user',
                        'type': 'POST',
                        'data': {
                            'setUserNameOrder': order
                        },
                        'success': function(result) {
                            location.reload();
                        }
                    });
                }

                function changeUserFNameOrder(order) {
                    $.ajax({
                        'url': '/admin/config/user',
                        'type': 'POST',
                        'data': {
                            'setUserFNameOrder': order
                        },
                        'success': function(result) {
                            location.reload();
                        }
                    });
                }

                function changeUserLNameOrder(order) {
                    $.ajax({
                        'url': '/admin/config/user',
                        'type': 'POST',
                        'data': {
                            'setUserLNameOrder': order
                        },
                        'success': function(result) {
                            location.reload();
                        }
                    });
                }

                function changeUserEmailOrder(order) {
                    $.ajax({
                        'url': '/admin/config/user',
                        'type': 'POST',
                        'data': {
                            'setUserEmailOrder': order
                        },
                        'success': function(result) {
                            location.reload();
                        }
                    });
                }
            </script>
                        <style>
                div#fre-tab,
                div#emp-tab,
                .financead {
                    float: left;
                    width: 100%;
                }

                .financead {}

                .financead .form-group,
                .financead .input-group {
                    width: 100%;
                }

                .financead label {
                    float: right;
                    font-size: 12px;
                    font-weight: bold;
                    letter-spacing: 1px;
                    padding: 8px 10px;
                }

                .financead select {
                    width: 150px !important;
                }
            </style>

        </div>

    </div>
</div>
@endsection