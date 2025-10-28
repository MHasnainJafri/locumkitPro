@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="/freelancer/finance">Finance</a></li>
                        <li><a href="/freelancer/reports">REPORTS</a></li>
                        <li><a href="#">Weekly Job</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-gbp" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Cash flow</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <section class="text-left">
                        <div class="col-md-12 pad0">
                            <div class="text-capitalize finance-page-head text-center">Weekly report</div>
                        </div>
                        <div class="cash_man_chart2 wholeborder padb0 wekky-reprt">
                            <form action="" class="add_item_form form-inline desktop clearfix">
                                @include('components.financial-year-select')

                                <div class="col-md-6 col-sm-12 col-xs-12 pad0 mart30 desktop income">
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <h1 class="mar0 text-capitalize" id="register_head_blue">Weekly income</h1>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-6">

                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 col-xs-12 pad0 mart30 desktop jobs hidden-xs hidden-sm">
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <h1 class="mar0 text-capitalize" id="register_head_blue">No. of jobs</h1>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-6">

                                    </div>
                                </div>
                            </form>
                            <div class="col-md-6">
                                <div class="pad0 marb20 canvas-scroll-x">
                                    <div class="cash_man_chart canvas-scroll-x">
                                        <div style="border:1px solid #dedede;height:315px">
                                            <canvas id="myChart" height="200" class="well"></canvas>
                                            <div id="myChart-legend" class="chart-legend"></div>
                                        </div>
                                    </div>

                                </div>
                                <div class="cash_table">
                                    <div class="table-responsive table-responsive-scroll">
                                        <table class="table-striped income_sum_table table" id="example">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-1">&nbsp;</th>
                                                    <th>S</th>
                                                    <th>M</th>
                                                    <th>T</th>
                                                    <th>W</th>
                                                    <th>T</th>
                                                    <th>F</th>
                                                    <th>S</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><b>{{ $filter_year }}-{{ $filter_year + 1 }}</b></td>
                                                    <td> {{ set_amount_format($income_by_day_data['Sun']) }} </td>
                                                    <td> {{ set_amount_format($income_by_day_data['Mon']) }} </td>
                                                    <td> {{ set_amount_format($income_by_day_data['Tue']) }} </td>
                                                    <td> {{ set_amount_format($income_by_day_data['Wed']) }} </td>
                                                    <td> {{ set_amount_format($income_by_day_data['Thu']) }} </td>
                                                    <td> {{ set_amount_format($income_by_day_data['Fri']) }} </td>
                                                    <td> {{ set_amount_format($income_by_day_data['Sat']) }} </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form action="" class="add_item_form form-inline mobile hidden-md hidden-lg clearfix">
                                    <div class="col-md-6 col-sm-12 col-xs-12 pad0 mart30 mobile jobs">
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <h1 class="mar0 text-capitalize" id="register_head_blue">Weekly Jobs</h1>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <div class="form-group mar0">
                                                <select name="weekly-job-filter" class="form-control pull-right" onchange="this.form.submit()">
                                                    <option value="year" selected>Yearly</option>
                                                    <option value="month">Monthly </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="pad0 marb20 canvas-scroll-x">
                                    <div class="cash_man_chart">
                                        <div style="border:1px solid #dedede;height:315px">
                                            <canvas id="myChart2" height="200" class="well"></canvas>
                                            <div id="myChart2-legend" class="chart-legend"></div>
                                        </div>
                                    </div>

                                </div>
                                <div class="cash_table">
                                    <div class="table-responsive table-responsive-scroll">
                                        <table class="table-striped income_sum_table table" id="weekly-job-records">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-1">&nbsp;</th>
                                                    <th>S</th>
                                                    <th>M</th>
                                                    <th>T</th>
                                                    <th>W</th>
                                                    <th>T</th>
                                                    <th>F</th>
                                                    <th>S</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="col-md-1"><b>{{ $filter_year }}-{{ $filter_year + 1 }}</b></td>
                                                    <td> {{ $job_count_by_day_data['Sun'] }} </td>
                                                    <td> {{ $job_count_by_day_data['Mon'] }} </td>
                                                    <td> {{ $job_count_by_day_data['Tue'] }} </td>
                                                    <td> {{ $job_count_by_day_data['Wed'] }} </td>
                                                    <td> {{ $job_count_by_day_data['Thu'] }} </td>
                                                    <td> {{ $job_count_by_day_data['Fri'] }} </td>
                                                    <td> {{ $job_count_by_day_data['Sat'] }} </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/frontend/locumkit-template/js/Chart.js"></script>

    <script>
        const site_currency = `{{ get_site_currency_symbol() }}`;
        $(document).ready(function() {
            const job_count_by_day_data = @json($job_count_by_day_data);

            var data = {
                labels: Object.keys(job_count_by_day_data),
                datasets: [{
                    label: `{{ $filter_year }}-{{ $filter_year + 1 }}`,
                    fillColor: "#4F81BD",
                    strokeColor: "#4F81BD",
                    pointColor: "#4F81BD",
                    pointStrokeColor: "#4F81BD",
                    data: Object.values(job_count_by_day_data)
                }, ]
            };

            var options = {
                animation: true,
                datasetFill: false,
                pointDot: true,
                multiTooltipTemplate: "Job : <%= value %>",
                bezierCurve: false,
                tooltipTemplate: "<%= label %> Job :  <%= value %>"
            };
            var c = $('#myChart2');
            var ct = c.get(0).getContext('2d');
            var ctx = document.getElementById("myChart2").getContext("2d");
            var myChart = new Chart(ctx).Bar(data, options);
            document.getElementById('myChart2-legend').innerHTML = myChart.generateLegend();
        });

        function detailDescription(description) {
            $('#detail-description .modal-body').html('<p>' + description + '</p>');
            $('#detail-description').modal('show');
        }
        $(document).ready(function() {
            const income_by_day_data = @json($income_by_day_data);

            var data = {
                labels: Object.keys(income_by_day_data),
                datasets: [{
                    label: `{{ $filter_year }}-{{ $filter_year + 1 }}`,
                    fillColor: "#4F81BD",
                    strokeColor: "#4F81BD",
                    pointColor: "#4F81BD",
                    pointStrokeColor: "#4F81BD",
                    data: Object.values(income_by_day_data)
                }, ]
            };

            var options = {
                animation: true,
                datasetFill: false,
                pointDot: true,
                multiTooltipTemplate: `${site_currency} <%= value %>.00`,
                bezierCurve: false,
                tooltipTemplate: `<%= label %> : ${site_currency} <%= value %>.00`,
            };
            var c = $('#myChart');
            var ct = c.get(0).getContext('2d');
            var ctx = document.getElementById("myChart").getContext("2d");
            var myChart = new Chart(ctx).Bar(data, options);
            document.getElementById('myChart-legend').innerHTML = myChart.generateLegend();
        });
    </script>
@endpush
