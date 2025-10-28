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
                        <li><a href="#">Income BY Supplier</a></li>
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
                        <h3>Cash Flow</h3>
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
                            <div class="text-capitalize finance-page-head text-center">Income By Supplier</div>
                        </div>
                        <div class="cash_man_chart2 wholeborder padb0 incomeBycategoryWtdth">
                            <form action="" class="add_item_form form-inline">
                                @include('components.financial-year-select')
                                <div class="col-md-12 pad0 mart30">
                                    <div class="col-md-8 cash_man_chart moreyearpie">
                                        <div class="form-group add_item_form form-inline marb0">
                                            <select name="income-filter" id="finance-year" class="form-control pull-right" onchange="this.form.submit()">
                                                <option @selected($income_filter == 'month') value="month">Monthly</option>
                                                <option @selected($income_filter == 'year') value="year">Yearly</option>
                                            </select>
                                        </div>
                                        <div class="oneyear canvas-scroll-x">
                                            <div class="mapdiv topyear">
                                                <div class="canvascover">
                                                    <div class="col-md-12">Total Income by supplier for year {{ $filter_year }}-{{ $filter_year + 1 }}</div>
                                                    <canvas id="Chart1" height="200" width="500" class="well"></canvas>
                                                    <div id="myChart1-legend" class="chart-legend"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4 cash_man_chart">
                                        <div class="form-group add_item_form form-inline marb0">
                                            <select name="supplier" class="form-control pull-right" onchange="this.form.submit()">
                                                <option value="">Choose Supplier </option>
                                                @foreach ($suppliers as $supplier)
                                                    <option @selected($supplier_filter == $supplier) value="{{ $supplier }}">{{ $supplier }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="canvas-scroll-x">
                                            <div class="canvas-width-scroll">
                                                <div class="mapdiv mapdiv-height-wrap">
                                                    <div class="canvascover">
                                                        <div class="col-md-12 catefullname">
                                                            @if ($income_filter == 'year')
                                                                <b>Year :</b> {{ date('Y') - 3 }}-{{ date('Y') + 1 }}
                                                            @else
                                                                <b>Year :</b> {{ $filter_year }}-{{ $filter_year + 1 }}
                                                            @endif
                                                            <canvas id="myChart2" height="200" class="well"></canvas>
                                                            <div id="myChart2-legend" class="chart-legend"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                            <div class="col-md-12 cash_table_box">
                                <div class="col-md-12 cash_table_form pad0">
                                </div>
                                <div class="col-md-12 cash_table pad0">
                                    <div class="table-responsive table-responsive-scroll finance-scroller">
                                        <table class="table-striped income_sum_table table" id="datatable">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-1">Tran No</th>
                                                    <th class="col-md-1">Job No</th>
                                                    <th class="col-md-1">Date</th>
                                                    <th class="col-md-1">Amount</th>
                                                    <th class="col-md-1">Supplier</th>
                                                    <th class="col-md-1">Bank</th>
                                                    <th class="col-md-1">Bank Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($income_records as $record)
                                                    <tr>
                                                        <td class="col-md-1" data-order="{{ $record->id }}"> #{{ $record->id }}</td>
                                                        <td class="col-md-1"> {{ $record->job_id ? $record->job_id : 'N/A' }} </td>
                                                        <td class="col-md-1" data-order="{{ $record->job_date }}"> {{ $record->job_date ? date('d-m-Y', strtotime($record->job_date)) : 'N/A' }} </td>

                                                        <td class="col-md-1"> {{ set_amount_format($record->job_rate) }} </td>
                                                        <td class="col-md-1"> {{ $record->supplier }} </td>
                                                        <td class="col-md-1">
                                                            @if ($record->is_bank_transaction_completed)
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                            @else
                                                                <a title="Manage Bank Status" href="javascript:void(0);" onclick="managebankincome('{{ $record->id }}')"><i class="fa fa-close" aria-hidden="true"> </i></a>
                                                            @endif
                                                        </td>
                                                        <td class="col-md-1" data-order="{{ $record->bank_transaction_date }}"> {{ $record->bank_transaction_date ? date('d-m-Y', strtotime($record->bank_transaction_date)) : 'N/A' }} </td>
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
        /* Injecting chart data into global javascript */
        const income_chart_data = @json($income_chart_data);
        var pie_supplier_chart_data = @json($pie_supplier_chart_data);
        const labels = income_chart_data["labels"];
        const data_paid = income_chart_data["data_paid"];
        const data_unpaid = income_chart_data["data_unpaid"];
        const site_currency = `{{ get_site_currency_symbol() }}`;

        var data = {
            labels: labels,
            datasets: [{
                    label: "Paid",
                    fillColor: "#85A04C",
                    strokeColor: "#85A04C",
                    pointColor: "#85A04C",
                    pointStrokeColor: "#85A04C",
                    data: data_paid
                },
                {
                    label: "Pending",
                    fillColor: "rgba(147, 176, 87, 0.5)",
                    strokeColor: "rgba(147, 176, 87, 0.5)",
                    pointColor: "rgba(147, 176, 87, 0.5)",
                    pointStrokeColor: "rgba(147, 176, 87, 0.5)",
                    data: data_unpaid
                }
            ]
        }
        var options = {
            responsiveAnimationDuration: 0,
            bezierCurve: false,
            animation: true,
            datasetFill: false,
            segmentShowStroke: true,
            animateRotate: true,
            animateScale: true,
            tooltipTemplate: `<%= label %> : ${site_currency} <%= value %>.00`,
            multiTooltipTemplate: `${site_currency} <%= value %>.00`,
            scaleLabel: "<%= ' ' + value%>",
            tooltipFontSize: 10,
            percentageInnerCutout: 25
        };

        var ctx2 = document.getElementById("myChart2").getContext("2d");
        var myChartr2 = new Chart(ctx2).Line(data, options);
        document.getElementById('myChart2-legend').innerHTML = myChartr2.generateLegend();

        //Pie supplier chart
        if (pie_supplier_chart_data.length == 0) {
            pie_supplier_chart_data = [{
                color: "#f0f0f0",
                highlight: "#fdfdfd",
                label: "No records found",
                value: 100
            }];
            options = {
                bezierCurve: false,
                animation: true,
                datasetFill: false,
                segmentShowStroke: true,
                animateRotate: true,
                animateScale: false,
                tooltipTemplate: "<%= label %>",
                multiTooltipTemplate: `${site_currency} 0.00`,
            };
        }

        var ctx1 = document.getElementById('Chart1').getContext("2d");
        var myChart1 = new Chart(ctx1).Pie(pie_supplier_chart_data, options);
        document.getElementById('myChart1-legend').innerHTML = myChart1.generateLegend();

        //Datatable
        $(document).ready(function() {
            $('#datatable').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
            });
        });
    </script>
@endpush
