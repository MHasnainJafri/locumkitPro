@extends('admin.layout.app')
@section('content')

        <div class="main-container container">
            @include('admin.layout.sidebar')

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
                <div class="page-content">
                    <link rel="stylesheet" type="text/css"
                        href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
                    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $('#jobTable').DataTable({
                                "lengthMenu": [
                                    [50, 100, 150, -1],
                                    [50, 100, 150, "All"]
                                ],
                                "order": [
                                    [0, "desc"]
                                ],
                                "columnDefs": [{
                                    "targets": [6, 7, 8, 9],
                                    "orderable": false,
                                }],
                            });
                        });
                    </script>

                    <br />
                    <table id="jobTable" class="table clickable table-striped table-hover">
                        <colgroup>
                            <col width="5%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="11%">
                            <col width="13%">
                            <col width="10%">
                            <col width="13%">
                            <col width="8%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Rate</th>
                                <th>Employer Id</th>
                                <th>Employer Name</th>
                                <th>Locum Id</th>
                                <th>Locum Name</th>
                                <th>Status</th>

                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($jobs as $job)
                           
                                <tr>
                                <td data-order="{{$job->id}}">#{{$job->id}}</td>
                                <td>{{$job->job_title}}</td>
                                <td>{{$job->category->name}}</td>
                                <td data-order="{{ $job->job_start_time }}">{{ date('d-m-Y', strtotime($job->job_date)) }}</td>
                                <td data-order="{{ $job->job_rate }}">${{ number_format($job->job_rate, 2) }}</td>
                                <td style="text-align:center;">{{$job->employer_id}}</td>
                                <td>{{$job->employer->firstname." ".$job->employer->lastname}}</td>
                                <td data-order="1">{{$job->getAcceptedFreelancerData()['id']}}</td>
                                <td>{{$job->getAcceptedFreelancerData()['name']}}</td>
                                <td>
                                    @switch($job->job_status)
                                        @case(1)
                                            <span style="color: orange; font-weight: 700;">Open/Waiting</span>
                                            @break
                                        @case(2)
                                            <span style="color: red; font-weight: 700;">Close/Expired</span>
                                            @break
                                        @case(3)
                                            <span style="color: gray; font-weight: 700;">Disable</span>
                                            @break
                                        @case(4)
                                            <span style="color: green; font-weight: 700;">Accept</span>
                                            @break
                                        @case(5)
                                            <span style="color: blue; font-weight: 700;">Done/Completed</span>
                                            @break
                                        @case(6)
                                            <span style="color: purple; font-weight: 700;">Freeze</span>
                                            @break
                                        @case(7)
                                            <span style="color: black; font-weight: 700;">Delete</span>
                                            @break
                                        @case(8)
                                            <span style="color: brown; font-weight: 700;">Cancel</span>
                                            @break
                                        @default
                                            <span style="color: darkred; font-weight: 700;">Unknown Status</span>
                                    @endswitch
                                </td>

                            </tr>

                            @endforeach
                            
                            
                        </tbody>
                    </table>
                    <div class="pagination">
                        <link rel="stylesheet"
                            href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
                        <p class="clearfix">
                        <ul class="paginator-div">
                        </ul>
                        </p>
                    </div>
                    <script type="text/javascript">
                        Gc.initTableList();
                    </script>
                    <style type="text/css">
                        div#jobTable_wrapper {
                            background: #f0f0f0;
                        }

                        div#jobTable_filter,
                        div#jobTable_length {
                            padding: 20px 15px;
                        }

                        table#jobTable {
                            background: #fff;
                            border-top: 1px solid;
                        }

                        div#jobTable_info {
                            padding: 15px;
                        }

                        div#jobTable_paginate {
                            padding: 10px 15px;
                        }
                    </style>
                </div>
            </div>
        </div>
      
@endsection
