@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="/freelancer/finance-detail">Finance</a></li>
                        <li><a href="/freelancer/reports">REPORTS</a></li>
                        <li><a href="#">Income by Area</a></li>
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
                            <div class="finance-page-head text-center">Income by area</div>
                        </div>
                        <div class="cash_man_chart2 wholeborder pad0">
                            <form action="" class="">
                                @include('components.financial-year-select')
                                <div class="col-md-12 pad0 mart30">
                                    <div class="col-md-12 cash_man_chart moreyearpie">
                                        <div class="oneyear canvas-scroll-x">
                                            <div class="mapdiv topyear">
                                                <div class="canvascover">
                                                    <div class="col-md-12">Total income by area for the year {{ $filter_year }}-{{ $filter_year + 1 }}</div>
                                                    <canvas id="Chart_0" height="200" width="500" class="well"></canvas>
                                                    <div id="js-legend_0" class="chart-legend"></div>
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
                                    <div class="table-responsive finance-scroller table-responsive-scroll">
                                        <table class="table-striped income_sum_table table" id="datatable">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-1">Tran No</th>
                                                    <th class="col-md-1">Job No</th>
                                                    <th class="col-md-1">Date</th>
                                                    <th class="col-md-1">Amount</th>
                                                    <th class="col-md-1">Location</th>
                                                    <th class="col-md-1">Bank</th>
                                                    <th class="col-md-1">Bank Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($income_records as $record)
                                                    <tr>
                                                        <td class="col-md-1" data-order="{{ $record->id }}"> #{{ $record->id }}</td>
                                                        <td class="col-md-1"> {{ $record->job_id ? $record->job_id : 'N/A' }} </td>
                                                        <!--<td class="col-md-1" data-order="{{ $record->job_date }}"> {{ $record->job_date ? $record->job_date : 'N/A' }} </td>-->
                                                        <td class="col-md-1" data-order="{{ $record->job_date }}">
                                                            {{ $record->job_date ? \Carbon\Carbon::parse($record->job_date)->format('d/m/Y') : 'N/A' }}
                                                        </td>

                                                        <td class="col-md-1"> {{ set_amount_format($record->job_rate) }} </td>
                                                        <td class="col-md-1"> {{ $record->supplier }} </td>
                                                        <td class="col-md-1">
                                                            @if ($record->is_bank_transaction_completed)
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                            @else
                                                                <a title="Manage Bank Status" href="javascript:void(0);" onclick="managebankincome('{{ $record->id }}')"><i class="fa fa-close" aria-hidden="true"> </i></a>
                                                            @endif
                                                        </td>
                                                        <!--<td class="col-md-1" data-order="{{ $record->bank_transaction_date }}"> {{ $record->bank_transaction_date ? $record->bank_transaction_date : 'N/A' }} </td>-->
                                                        <td class="col-md-1" data-order="{{ $record->bank_transaction_date }}">
                                                            {{ $record->bank_transaction_date ? \Carbon\Carbon::parse($record->bank_transaction_date)->format('d/m/Y') : 'N/A' }}
                                                        </td>

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
@endsection

@push('scripts')
    <script src="/frontend/locumkit-template/js/Chart.js"></script>
    <script>
        var pie_location_chart_data = @json($pie_location_chart_data);
        const site_currency = `{{ get_site_currency_symbol() }}`;

        $(document).ready(function() {
            var options = {
                bezierCurve: false,
                animation: true,
                datasetFill: false,
                segmentShowStroke: true,
                animateRotate: true,
                animateScale: false,
                tooltipTemplate: `<%= label %> : ${site_currency} <%= value %>.00`,
                multiTooltipTemplate: `${site_currency} <%= value %>.00`,
            };
            if (pie_location_chart_data.length == 0) {
                pie_location_chart_data = [{
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
            var ctx = document.getElementById('Chart_0').getContext("2d");
            var myChartr = new Chart(ctx).Pie(pie_location_chart_data, options);
            document.getElementById('js-legend_0').innerHTML = myChartr.generateLegend();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
            });
        });
    </script>
@endpush
