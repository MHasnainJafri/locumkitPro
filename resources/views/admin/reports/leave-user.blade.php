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
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">User ID</th>
                            <th class="text-center">Leave Date</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Reason of leaving</th>
                        </tr>
                    </thead>
                    <tbody class="" id="render_locam">
                        @include('admin.reports.leaver-user-partials')
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
                    var leavedate = document.getElementsByName("leave_date[]");
                    var name = document.getElementsByName("name[]");
                    var emailInputs = document.getElementsByName("email[]");
                    var reason = document.getElementsByName("reason[]");

                    var data = [];
                    
                    for (var i = 0; i < UserIdInputs.length; i++) {
                        var entry = {
                            user_id: UserIdInputs[i].value,
                            leave_date: leavedate[i].value,
                            name: name[i].value,
                            email: emailInputs[i].value,
                            reason: JSON.parse(reason[i].value),
                        };
                        data.push(entry);
                    }

                    var form = document.createElement('form');
                    form.action = "{{ route('report.leaverUser.export') }}";
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
                                url: '{{route("report.leaverUser")}}',
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