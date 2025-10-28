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
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">User ID</th>
                            <th class="text-center">Locum</th>
                            <th class="text-center">Jobs Applied</th>
                            <th class="text-center">Jobs Accepted</th>
                            <th class="text-center">Success Rate</th>
                            <th class="text-center">Cancel Rate</th>
                            <th class="text-center">Jobs frozen</th>
                            <th class="text-center">frozen and accepted</th>
                            <th class="text-center">frozen success rate</th>
                            <th class="text-center">Private jobs added</th>
                            <th class="text-center">Detailed reports</th>
                        </tr>
                    </thead>
                    <tbody class="" id="render_locam">
                        @include('admin.reports.getLocumReportPartials')
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
                    var locum = document.getElementsByName("locum[]");
                    var jobs_applied = document.getElementsByName("jobs_applied[]");
                    var jobs_accepted = document.getElementsByName("jobs_accepted[]");
                    var success_rate = document.getElementsByName("success_rate[]");
                    var cancel_rate = document.getElementsByName("cancel_rate[]");
                    var jobs_frozen = document.getElementsByName("jobs_frozen[]");
                    var frozen_and_accepted = document.getElementsByName("frozen_and_accepted[]");
                    var frozen_success_rate = document.getElementsByName("frozen_success_rate[]");
                    var private_jobs_added = document.getElementsByName("private_jobs_added[]");

                    var data = [];
                    
                    for (var i = 0; i < UserIdInputs.length; i++) {
                        var entry = {
                            user_id: UserIdInputs[i].value,
                            locum: locum[i].value,
                            jobs_applied: jobs_applied[i].value,
                            jobs_accepted: jobs_accepted[i].value,
                            success_rate: success_rate[i].value,
                            cancel_rate: cancel_rate[i].value,
                            jobs_frozen: jobs_frozen[i].value,
                            frozen_and_accepted: frozen_and_accepted[i].value,
                            frozen_success_rate: frozen_success_rate[i].value,
                            private_jobs_added: private_jobs_added[i].value,
                        };
                        data.push(entry);
                    }

                    var form = document.createElement('form');
                    form.action = "{{ route('report.locumjobReport.export') }}";
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
                                url: '{{route("report.locumjobReport")}}',
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