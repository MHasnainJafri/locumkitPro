@extends('admin.layout.app')
@section('content')
<div class="main-container container">
    @include('admin.layout.sidebar')
    <div class="col-lg-12">
        <div id="breadcrumbs" class="breadcrumbs">
            <div id="menu-toggler-container" class="hidden-lg">
                <span id="menu-toggler">
                    <i class="glyphicon glyphicon-new-window"></i>
                    <span class="menu-toggler-text">Menu</span>
                </span>
            </div>
            <ul class="breadcrumb">
                <li class="active">
                    <i class="glyphicon glyphicon-home home-icon"></i>
                    Dashboard
                </li>
            </ul>
        </div>
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="dashbord-short-links">
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="dashbord-btn">
                                <a href="{{route('admin.users.index')}}" class="dashbord-btn-link"><i
                                        class="glyphicon glyphicon-user"></i>   Users</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="dashbord-btn">
                                <a href="{{route('admin.jobs.index')}}" class="dashbord-btn-link"><i
                                        class="glyphicon glyphicon-briefcase"></i>  Jobs</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="dashbord-btn">
                                <a href="{{route('finance.record')}}" class="dashbord-btn-link"><i
                                        class="glyphicon glyphicon-gbp"></i>  Finance</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="dashbord-btn">
                                <a href="{{route('admin.feedback.index')}}" class="dashbord-btn-link"><i
                                        class="glyphicon glyphicon-star-empty"></i>  Feedback</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="dahsbord-graph-div">
                        <h3 class="garph-title">Users / Jobs / Income By Locum ( Current year )</h3>
                        <canvas id="pi-graph" style="height:100px !important;"></canvas>
                        <ul class="bar-legend">
                            <li><span style="background-color:#0498c7"></span>Users</li>
                            <li><span style="background-color:#f75b36"></span>Jobs</li>
                            <li><span style="background-color:#1cdc00"></span>Income</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="dahsbord-graph-div">
                        <h3 class="garph-title">User Register ( Current year ) </h3>
                        <h6 class="garph-title" style="position:relative; z-index:-1;">User Register ( Current year ) </h6>
                        <canvas id="user-register-graph"></canvas>
                        <ul class="bar-legend">
                            <li><span style="background-color:rgba(220,220,220,0.5)"></span>Locums</li>
                            <li><span style="background-color:rgba(151,187,205,0.5)"></span>Employes</li>
                        </ul>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                Gc.initDashBoard($.parseJSON(
                        '\x7B\x22sortable2\x22\x3A\x22stats\x22,\x22sortable1\x22\x3A\x22fast\x2Dlinks,blog\x22\x7D'),
                    '\x2Fadmin\x2Fdashboard\x2Fsave');
            </script>
            <style type="text/css">
                .dashbord-btn {
                    text-align: center;
                }

                .dashbord-btn a.dashbord-btn-link {
                    width: 100%;
                    margin: 15px auto;
                    padding: 50px 0;
                    font-size: 32px;
                    display: block;
                    background: #f0f0f0;
                    color: #555;
                    text-decoration: none;
                    border: 1px solid #e0dfdf;
                }

                .dashbord-btn a.dashbord-btn-link:hover {
                    background: #00A9E0;
                    color: #fff;
                }

                .dahsbord-graph-div {
                    background: #f0f0f0;
                    padding: 20px 25px;
                    width: 100%;
                    border: 1px solid #e0dfdf;
                    margin-bottom: 30px;
                }

                .dahsbord-graph-div h3.garph-title {
                    margin: 0 0 25px;
                    text-transform: uppercase;
                }

                .dashbord-short-links {
                    border: 1px solid #ccc;
                    float: left;
                    padding: 15px;
                    width: 100%;
                    margin-bottom: 30px;
                }

                .dahsbord-graph-div ul.bar-legend li span {
                    width: 20px;
                    height: 20px;
                    display: inline-block;
                    margin: 0 5px 0 10px;
                    border: 1px solid #000;
                }

                ul.bar-legend {
                    margin-bottom: 0;
                    margin-top: 15px;
                }

                .dahsbord-graph-div ul.bar-legend li {
                    display: inline-block;
                }

                ul.bar-legend li span {
                    width: 20px;
                    height: 20px;
                    display: block;
                    margin: 0 5px 0 10px;
                    border: 1px solid #000;
                    float: left;
                }
            </style>
            <script src="https://mdbootstrap.com/wp-content/themes/mdbootstrap4/js/mdb3/mdb.min.js"></script>
            <script type="text/javascript">
                $(function() {
                    var userData = {
                        labels:@json($months),
                        datasets: [{
                                label: "Locums",
                                fillColor: "rgba(220,220,220,0.5)",
                                strokeColor: "rgba(220,220,220,1)",
                                pointColor: "rgba(220,220,220,1)",
                                pointStrokeColor: "#fff",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(220,220,220,1)",
                                data: @json($locumCounts)
                            },
                            {
                                label: "Employer",
                                fillColor: "rgba(151,187,205,0.5)",
                                strokeColor: "rgba(151,187,205,1)",
                                pointColor: "rgba(151,187,205,1)",
                                pointStrokeColor: "#fff",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(151,187,205,1)",
                                data:@json($employeeCounts)
                            }
                        ]
                    };

                    var option = {
                        responsive: true,
                    };
                    var ctx2 = document.getElementById("user-register-graph").getContext('2d');
                    var userRegister = new Chart(ctx2).Line(userData, option); //'Line' defines type of the

                    /*var userData = {
                            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"],
                            datasets: [
                                {
                                    label: "Locums",
                                    fillColor: "rgba(220,220,220,0.2)",
                                    strokeColor: "rgba(220,220,220,1)",
                                    pointColor: "rgba(220,220,220,1)",
                                    pointStrokeColor: "#fff",
                                    pointHighlightFill: "#fff",
                                    pointHighlightStroke: "rgba(220,220,220,1)",
                                    data: [0, 0, 5, 5, 4, 15, 17, 9, 14, 1]
                                },
                                {
                                    label: "Employer",
                                    fillColor: "rgba(151,187,205,0.2)",
                                    strokeColor: "rgba(151,187,205,1)",
                                    pointColor: "rgba(151,187,205,1)",
                                    pointStrokeColor: "#fff",
                                    pointHighlightFill: "#fff",
                                    pointHighlightStroke: "rgba(151,187,205,1)",
                                    data: [0, 0, 1, 4, 1, 2, 1, 1, 2, 0]
                                }
                                ]
                            };
                        var ctx1 = document.getElementById("daily-visitors").getContext('2d');
                        var dailyVisitors = new Chart(ctx1).Bar(userData, option); //'Line' defines type of the chart.
                */


                    var data = [{
                            value: @json($allusersCount),
                            color: "#00A9E0",
                            highlight: "#0498c7",
                            label: "Total Users"
                        },
                        {
                            value: @json($alljobPost),
                            color: "#f75b36",
                            highlight: "#ea4821",
                            label: "Total Jobs"
                        },
                        {
                            value: @json($yearturnover),
                            color: "#1cdc00",
                            highlight: "#1cb306",
                            label: "Total Income ( x1000 ) "
                        }
                    ];

                    var ctx3 = document.getElementById("pi-graph").getContext('2d');
                    var piGraph = new Chart(ctx3).Doughnut(data, option); //'Line' defines type of the chart.
                });
            </script>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    if (!window.location.hash) {
      window.location = window.location + '#loaded';
      window.location.reload();
    }
  });
</script>

@endsection