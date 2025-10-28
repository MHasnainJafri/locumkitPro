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
                        <li><a href="#">Cash flow report</a></li>
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
                            <div class="marb0 text-capitalize finance-page-head text-center"><span>Cash flow report </span>
                                <p style="font-size: 14px;text-align: center; width: 100%;  margin: -4px 0 0; text-transform: initial;"><small>This is based on all the transactions you have recorded as banked.</small></p>
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
                                        <h1 class="mar0 text-capitalize" id="register_head_blue">Cash Movement Report</h1>
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
                            <div class="col-md-12 pad0 marb20 canvas-scroll-x">
                                <div class="col-md-6 cash_man_chart">
                                    <div style="border:1px solid #dedede;height:315px">
                                        <canvas id="myChart" height="200" class="well"></canvas>
                                        <div id="myChart-legend" class="chart-legend"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 cash_man_chart">
                                    <div style="border:1px solid #dedede;height:315px">
                                        <canvas id="myChart2" height="200" class="well"></canvas>
                                        <div id="myChart2-legend" class="chart-legend"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 cash_table">
                                <div class="table-responsive table-responsive-scroll finance-scroller">
                                    <table class="table-striped income_sum_table table" id="example">
                                        <thead>
                                            <tr>
                                                <th class="col-md-2">Date</th>
                                                <th class="col-md-1">Trans no</th>
                                                <th class="col-md-1">Job No</th>
                                                <th class="col-md-1">Type</th>
                                                <th class="col-md-1">Category</th>
                                                <th class="col-md-2">Amount</th>
                                                <th class="col-md-1">Bank</th>
                                                <th class="col-md-2">Bank date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($all_transactions as $transaction)
                                                <tr>
                                                   <td class="col-md-2">
                                                        {{ $transaction->job_date ? \Carbon\Carbon::parse($transaction->job_date)->format('d/m/Y') : 'N/A' }}
                                                    </td>

                                                    <td class="col-md-1" data-order="{{ $transaction->getTransactionNumber() }}"> {{ $transaction->getTransactionNumber() }}</td>
                                                    <td class="col-md-1"> {{ $transaction->job_id ? $transaction->job_id : 'N/A' }} </td>
                                                    <td class="col-md-1">
                                                        @if (get_class($transaction) == 'App\Models\FinanceIncome')
                                                            Income
                                                        @else
                                                            Expense
                                                        @endif
                                                    </td>
                                                    <td class="col-md-1">
                                                        @if (get_class($transaction) == 'App\Models\FinanceIncome')
                                                            {{ $transaction->get_income_type() }}
                                                        @else
                                                            {{ $transaction->expense_type?->expense }}
                                                        @endif
                                                    </td>
                                                    <td class="col-md-2"> {{ set_amount_format($transaction->job_rate) }} </td>
                                                    <td class="col-md-1"><i class="fa fa-check" aria-hidden="true"></i></td>
                                                    <td class="col-md-2" data-order="{{ $transaction->bank_transaction_date }}">
                                                        {{ $transaction->bank_transaction_date ? \Carbon\Carbon::parse($transaction->bank_transaction_date)->format('d/m/Y') : 'N/A' }}
                                                    </td>

                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
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
        const income_by_months = @json($income_chart_data);
        const expense_chart_data = @json($expense_chart_data);
        const site_currency = `{{ get_site_currency_symbol() }}`;
        const net_chart_data = Object.keys(income_by_months).map(key => {
            return income_by_months[key] - expense_chart_data[key];
        });

        function detailDescription(description) {
            $('#detail-description .modal-body').html('<p>' + description + '</p>');
            $('#detail-description').modal('show');
        }

        $(document).ready(function() {
            $('#example').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
                "order": [
                    [0, "desc"]
                ]
            });
        });

        var data = {
            labels: Object.keys(income_by_months),
            datasets: [{
                    label: "Cash in",
                    fillColor: "#6f8541",
                    strokeColor: "#6f8541",
                    pointColor: "#6f8541",
                    pointStrokeColor: "#6f8541",
                    data: Object.values(income_by_months)
                },
                {
                    label: "Cash out",
                    fillColor: "#A44442",
                    strokeColor: "#A44442",
                    pointColor: "#A44442",
                    pointStrokeColor: "#A44442",
                    data: Object.values(expense_chart_data)
                }
            ]
        };

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

        var ctx = document.getElementById("myChart").getContext("2d");
        var myChart = new Chart(ctx).Bar(data, options);
        document.getElementById('myChart-legend').innerHTML = myChart.generateLegend();

        var net_data = {
            labels: Object.keys(income_by_months),
            datasets: [{
                label: "Net cash movement",
                fillColor: "#4BACC6",
                strokeColor: "#4BACC6",
                pointColor: "#4BACC6",
                pointStrokeColor: "#4BACC6",
                data: net_chart_data
            }]
        };

        var ctx2 = document.getElementById("myChart2").getContext("2d");
        var myChart2 = new Chart(ctx2).Line(net_data, options);
        document.getElementById('myChart2-legend').innerHTML = myChart2.generateLegend();
    </script>
@endpush
