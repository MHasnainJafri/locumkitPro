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
                        <li><a href="#">Net Income</a></li>
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
                        <h3>Net Income</h3>
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
                            <div class="marb0 text-capitalize finance-page-head text-center"><span> Net Income </span>
                            </div>
                        </div>
                        <div class="col-md-12 in_ex_pr_box emply-finance">
                            <div>
                                <div class="col-sm-3 col-md-3 text-center">
                                    <h1 class="mar0" id="register_head_blue">Income</h1>
                                    <h2 class="mar0">{{ set_amount_format($total_income) }} </h2>
                                </div>
                                <div class="col-sm-3 col-md-3 text-center">
                                    <h1 class="mar0" id="register_head_blue">Expenses</h1>
                                    <h2 class="mar0">{{ set_amount_format($total_expense) }} </h2>
                                </div>
                                <div class="col-sm-3 col-md-3 text-center">
                                    <h1 class="mar0" id="register_head_blue">Profit</h1>
                                    <h2 class="mar0">{{ set_amount_format($total_income - $total_expense) }} </h2>
                                </div>
                                <div class="col-sm-3 col-md-3 text-center">
                                    <h1 class="mar0" id="register_head_blue">Income tax</h1>
                                    <h2 class="mar0">{{ set_amount_format($user_total_tax) }} </h2>
                                </div>
                            </div>
                        </div>
                        <div class="cash_man_chart2 wholeborder padb0">
                            <form action="" class="add_item_form form-inline">

                                @include('components.financial-year-select')

                                <div class="col-md-12 pad0 mart30">
                                    <div class="col-md-6">
                                        <h1 class="mar0 text-capitalize" id="register_head_blue">Net Income</h1>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mar0">
                                            <select name="filter" class="form-control pull-right" onchange="this.form.submit()">
                                                <option @if ($filter == 'month') selected @endif value="month">Monthly </option>
                                                <option @if ($filter == 'year') selected @endif value="year">Yearly</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="col-md-12 pad0 marb20">

                                <div class="col-md-7 cash_man_chart">
                                    <div style="border:1px solid #dedede;height:315px">
                                        <canvas id="myChart2" height="200" class="well"></canvas>
                                        <div id="myChart2-legend" class="chart-legend"></div>
                                    </div>
                                </div>
                                <div class="col-md-5 cash_man_chart">
                                    <div class="cash_table table-responsive finance-scroller" style="border:1px solid #dedede;height:315px">
                                        <table class="table-striped income_sum_table table">
                                            <thead>
                                                <tr>
                                                    @if ($filter == 'month')
                                                        <th class="col-md-6">Month</th>
                                                    @else
                                                        <th class="col-md-6">Year</th>
                                                    @endif
                                                    <th class="col-md-6">Total net income</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($net_chart_data as $key => $record)
                                                    <tr>
                                                        <td> {{ $key }} </td>
                                                        <td> {{ set_amount_format($record) }} </td>
                                                    </tr>
                                                @endforeach
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

    <div id="detail-description" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Expense Description</h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/frontend/locumkit-template/js/Chart.js"></script>
    <script>
        const site_currency = `{{ get_site_currency_symbol() }}`;
        const net_chart_data = @json($net_chart_data);

        function detailDescription(description) {
            $('#detail-description .modal-body').html('<p>' + description + '</p>');
            $('#detail-description').modal('show');
        }

        var options = {
            animation: true,
            datasetFill: false,
            pointDot: true,
            multiTooltipTemplate: function(label) {
                return site_currency + parseFloat(label.value).toFixed(2);
            },
            bezierCurve: false,
            tooltipTemplate: function(label) {
                return site_currency + parseFloat(label.value).toFixed(2);
            },
        };

        var net_data = {
            labels: Object.keys(net_chart_data),
            datasets: [{
                label: "Net cash movement",
                fillColor: "#4BACC6",
                strokeColor: "#4BACC6",
                pointColor: "#4BACC6",
                pointStrokeColor: "#4BACC6",
                data: Object.values(net_chart_data)
            }]
        };

        var ctx2 = document.getElementById("myChart2").getContext("2d");
        var myChart2 = new Chart(ctx2).Line(net_data, options);
        document.getElementById('myChart2-legend').innerHTML = myChart2.generateLegend();
    </script>
@endpush
