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
                        <!-- <div class="form-group pull-left">
                            <div class="input-group pull-left">
                            <input type="date" id="startdate" style="padding: 5px; margin-right: 20px; width: 20%;">
                            <input type="date" id="enddate" style="padding: 5px; margin-right: 20px; width: 20%;">
                                <button class="btn btn-info" onclick="getPrint()">Export</button>
                            </div>
                        </div> -->
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
                            <th class="text-center">Date</th>
                            <th class="text-center">Freelancer</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Location</th>
                        </tr>
                    </thead>
                    <tbody id="render_locam">
                            @foreach($jobs as $key => $value)
                                <tr>
<td class="text-center">
    {{ $value->job_date ? \Carbon\Carbon::parse($value->job_date)->format('d/m/y') : '' }}
</td>
                                    <td class="text-center"> {{$value -> job_type ?? ''}} </td>
                                    <td class="text-center"> {{$value -> job_type ?? ''}} </td>
                                    <td class="text-center"> {{$value -> job_rate ?? ''}} </td>
                                    <td class="text-center"> {{$value -> job_address ?? ''}} </td>
                                </tr>
                            @endforeach
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
