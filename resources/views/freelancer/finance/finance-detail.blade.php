@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="#">Finance</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Finance</h3>
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
                            <div class="marb0 finance-page-head text-center">Finance Details</div>
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


                        <div class="col-md-12 pad0 finacedetable">
                            <form class="wholeborder" action="#" method="GET">
                                <section id="Income-tansc" class="Income-tansc">

                                    @include('components.financial-year-select')

                                    <div class="col-md-12 pad0 income_tra_sc">
                                        <div class="col-md-8 col-sm-12 income">
                                            <div class="col-md-12 pad0 head_box">
                                                <h1 class="mar0 text-capitalize" id="register_head_blue">Latest income transactions</h1>
                                            </div>
                                            <div class="col-md-12 pad0">
                                                <div class="table-responsive table-responsive-scroll finance-scroller">
                                                    <table class="table" id="incometable">
                                                        <thead>
                                                            <tr>
                                                                <th>Tran&nbsp;No<span style="position:relative; z-index:-1;">  cc</span></th>
                                                                <th>Job&nbsp;No<span style="position:relative; z-index:-1;">  c</span></th>
                                                                <th>Job&nbsp;Type &nbsp;&nbsp;</th>
                                                                <th>Date</th>
                                                                <th>Amount<span style="position:relative; z-index:-1;">  c</span></th>
                                                                <th>Store</th>
                                                                <th>Location <span style="position:relative; z-index:-1;">  c</span></th>
                                                                <th>Category<span style="position:relative; z-index:-1;">  c</span></th>
                                                                <th>Supplier<span style="position:relative; z-index:-1;">  c</span></th>
                                                                <th>Bank</th>
                                                                <th>Bank&nbsp;Date&nbsp;&nbsp;&nbsp;</th>
                                                                <th>Action<span> </span></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($income_records as $record)
                                                                <tr>
                                                                    <td data-order="{{ $record->id }}">#{{ $record->id }}</td>
                                                                    <td> {{ $record->job_id ? $record->job_id : 'N/A' }} </td>
                                                                    <td>
                                                                        @if($record->job_type == '1')
                                                                            Website Job
                                                                        @elseif($record->job_type == '2')
                                                                            Private Job
                                                                        @elseif($record->job_type == '3')
                                                                            Other Job
                                                                        @endif
                                                                    </td>
                                                                    <td data-order="{{ $record->job_date }}"> {{ \Carbon\Carbon::parse($record->job_date)->format('d-m-y') }} </td>

                                                                    <td> {{ set_amount_format($record->job_rate) }} </td>
                                                                    <td> {{ $record->store ? $record->store : 'N/A' }} </td>
                                                                    <td> {{ $record->location ? $record->location : 'N/A' }} </td>
                                                                    <td> {{ $record->get_income_type() }} </td>
                                                                    <td> {{ $record->supplier ? $record->supplier : 'N/A' }} </td>
                                                                    <td>
                                                                        @if ($record->is_bank_transaction_completed)
                                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                                        @else
                                                                            <a title="Manage Bank Status" href="javascript:void(0);" onclick="managebankincome('{{ $record->id }}')"><i class="fa fa-close" aria-hidden="true"> </i></a>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        {{ \Carbon\Carbon::parse($record->bank_transaction_date)->format('d-m-y') }}
                                                                        <!--{{ $record->bank_transaction_date ? $record->bank_transaction_date : 'N/A' }}-->
                                                                    </td>
                                                                     <style>
                                                                        .custom-btn {
                                                                            box-shadow: none !important;
                                                                            border-width: 0;
                                                                            position: relative;
                                                                            font-size: 10px;
                                                                            padding: 6px 12px;
                                                                            margin: 6px !important;
                                                                        }
                                                                    </style>
                                                                    <td>
                                                                        <a href="/freelancer/edit-income/{{ $record->id }}" class="custom-btn btn-xs btn-info"><i class="fa fa-fw fa-edit"></i></a>
                                                                        <button type="button" class="custom-btn btn-xs btn-danger" onclick="confirm_delete_in('{{ $record->id }}')"><i class="fa fa-fw fa-close"></i></button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-12 pad0">
                                                    <a class="read-common-btn grad_btn pull-left" href="/freelancer/open-invoices">Open Invoice</a>
                                                    <a href="/freelancer/add-income" class="read-common-btn grad_btn pull-right">ADD NEW</a>
                                                    <a href="/freelancer/all-transaction?show=income" class="read-common-btn grad_btn pull-right">Show All</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12 income-chart-div pr0">
                                            <div class="col-md-12 pad0 head_box">
                                                <div class="col-xs-6 col-sm-4 col-md-8 pad0">
                                                    <div class="transaction-section-title">
                                                        <h1 class="mar0 text-capitalize" id="register_head_blue">Income Summary</h1>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 col-sm-4 col-md-4 padl0">
                                                    <div class="filter">
                                                        <select name="income-filter" id="finance-filter" class="filter-selection" onchange="this.form.submit()">
                                                            <option @selected($income_filter == 'month') value="month">Monthly</option>
                                                            <option @selected($income_filter == 'year') value="year">Yearly</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pad0">
                                                <div class="income-graph graph-chart">
                                                    <canvas id="myChart" width="400" height="200" class="well"></canvas>
                                                    <div id="myChart-legend" class="chart-legend"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section id="Income-tansc" class="Income-tansc">
                                    <div class="col-md-12 pad0 income_tra_sc">
                                        <div class="col-md-8 col-sm-12 income">
                                            <div class="col-md-12 pad0 head_box">
                                                <h1 class="mar0 text-capitalize" id="register_head_blue">Latest Expenses Transactions</h1>
                                            </div>
                                            <div class="col-md-12 pad0">
                                                <div class="table-responsive table-responsive-scroll finance-scroller">
                                                    <table class="table" id="expensetable">
                                                        <thead>
                                                            <tr>
                                                                <th>Tran&nbsp;No&nbsp;&nbsp;</th>
                                                                <th>Job&nbsp;No&nbsp;&nbsp;</th>
                                                                <th>Job&nbsp;Type&nbsp;&nbsp;</th>
                                                                <th>Date</th>
                                                                <th>Amount&nbsp;&nbsp;</th>
                                                                <th>Description&nbsp;&nbsp;</th>
                                                                <th>Category&nbsp;&nbsp;</th>
                                                                <th>Receipt&nbsp;</th>
                                                                <th>Bank</th> 
                                                                <th>Bank&nbsp;Date<span style="position:relative; z-index:-1;">spa </span> </th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($expense_records as $record)
                                                                <tr>
                                                                    <td data-order="{{ $record->id }}">#{{ $record->id }}</td>
                                                                    <td> {{ $record->job_id ? $record->job_id : 'N/A' }} </td>
                                                                    <td>
                                                                        @if($record->job_type == '1')
                                                                            Website Job
                                                                        @elseif($record->job_type == '2')
                                                                            Private Job
                                                                        @elseif($record->job_type == '3')
                                                                            Other Job
                                                                        @endif
                                                                    </td>

                                                                    <!--<td data-order="{{ $record->job_date }}"> {{ $record->job_date ? $record->job_date : 'N/A' }} </td>-->
                                                                    <td data-order="{{ $record->job_date }}"> {{ \Carbon\Carbon::parse($record->job_date)->format('d-m-y') }} </td>
                                                                    <td> {{ set_amount_format($record->job_rate) }} </td>
                                                                    <td>
                                                                        @if ($record->description && $record->description != '')
                                                                            <a href="javascript:void(0);" onclick="detailDescription('{{ $record->description }}')">{{ substr($record->description, 0, 8) }}...</a>
                                                                        @else
                                                                            N/A
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($record->expense_type)
                                                                            {{ $record->expense_type->expense }}
                                                                        @else
                                                                            N/A
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($record->receipt)
                                                                            <a href="{{ $record->receipt }}" download>Download</a>
                                                                        @else
                                                                            N/A
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($record->is_bank_transaction_completed)
                                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                                        @else
                                                                            <a title="Manage Bank Status" href="javascript:void(0);" onclick="managebankexpanse('{{ $record->id }}')"><i class="fa fa-close" aria-hidden="true"> </i></a>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        {{ \Carbon\Carbon::parse($record->bank_transaction_date)->format('d-m-y') }}
                                                                        <!--{{ $record->bank_transaction_date ? $record->bank_transaction_date : 'N/A' }}-->
                                                                    </td>
                                                                    <td>
                                                                        <a href="/freelancer/edit-expense/{{ $record->id }}" class="custom-btn btn-xs btn-info"><i class="fa fa-fw fa-edit"></i></a>
                                                                        <button type="button" class="custom-btn btn-xs btn-danger" onclick="confirm_delete_ex('{{ $record->id }}')"><i class="fa fa-fw fa-close"></i></button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-12 pad0">
                                                    <a class="read-common-btn grad_btn pull-right" href="/freelancer/add-expense">ADD NEW</a>
                                                    <a href="/freelancer/all-transaction?show=expense" class="read-common-btn grad_btn pull-right">Show All</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12 income-chart-div pr0">
                                            <div class="col-md-12 pad0 head_box">
                                                <div class="col-xs-6 col-sm-4 col-md-8 pad0">
                                                    <h1 class="mar0 text-capitalize" id="register_head_blue">Expenses Summary</h1>
                                                </div>
                                                <div class="col-xs-6 col-sm-4 col-md-4 padl0">
                                                    <div class="filter">
                                                        <select name="expense-filter" id="ex-finance-filter" class="filter-selection" onchange="this.form.submit()">
                                                            <option @selected($expense_filter == 'month') value="month">Monthly</option>
                                                            <option @selected($expense_filter == 'year') value="year">Yearly</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pad0">
                                                <div class="income-graph graph-chart">
                                                    <canvas id="myChart2" width="400" height="200" class="well"></canvas>
                                                    <div id="myChart2-legend" class="chart-legend"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>


                            </form>

                            @foreach ($income_records as $record)
                                <form role="form" action="/freelancer/delete-income/{{ $record->id }}" id="delete-in-form-{{ $record->id }}" method="post" style="display: none;" hidden>
                                    @csrf
                                    @method('delete')
                                </form>
                            @endforeach
                            @foreach ($expense_records as $record)
                                <form role="form" action="/freelancer/delete-expense/{{ $record->id }}" id="delete-ex-form-{{ $record->id }}" method="post" style="display: none;" hidden>
                                    @csrf
                                    @method('delete')
                                </form>
                            @endforeach
                        </div>

                    </section>
                    <section id="transaction-see" class="transaction-see">
                        <div class="col-md-12 padl0">
                            <ul>
                                <li><a class="read-common-btn grad_btn" href="/freelancer/all-transaction">All Transactions</a></li>
                                <li><a class="read-common-btn grad_btn" href="/freelancer/open-invoices">Open Invoice</a></li>
                                <li><a class="read-common-btn grad_btn" href="/freelancer/reports">Reports</a></li>
                                <li><a class="read-common-btn grad_btn" href="/freelancer/manage-supplier">Manage Supplier</a></li>
                                <li><a class="read-common-btn grad_btn" href="/freelancer/bank-details">Bank Details</a></li>
                            </ul>
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
                    <h4 class="modal-title">Expenses Description</h4>
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
        /* Injecting blade data into javascript as global variable  */
        const income_by_months = @json($income_chart_data);
        const expense_chart_data = @json($expense_chart_data);
        const site_currency = `{{ get_site_currency_symbol() }}`;

        function confirm_delete_in(id) {
            $('div#alert-confirm-modal #alert-message').html('Do you really want to delete this transaction?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#delete-in-form-" + id).submit();
                messageBoxClose();
            });
        };

        function confirm_delete_ex(id) {
            $('div#alert-confirm-modal #alert-message').html('Do you really want to delete this transaction?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#delete-ex-form-" + id).submit();
                messageBoxClose();
            });
        }

        function detailDescription(description) {
            $('#detail-description .modal-body').html('<p>' + description + '</p>');
            $('#detail-description').modal('show');
        }

        $(document).ready(function() {
            $('#incometable , #expensetable').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
                "order": [
                    [0, "desc"]
                ]
            });
        });

        var options = {
            animation: true,
            multiTooltipTemplate: site_currency + " <%= value %>.00",
            scaleLabel: "<%= ' ' + value%>"
        };

        var income_months_labels = Object.keys(income_by_months);
        var income_chart_data = Object.values(income_by_months);
        var data = {
            labels: income_months_labels,
            datasets: [{
                label: "Income",
                fillColor: "#85A04C",
                strokeColor: "#85A04C",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                data: income_chart_data
            }]
        };

        var ctx = document.getElementById("myChart").getContext("2d");
        var myChart = new Chart(ctx).Bar(data, options);
        document.getElementById('myChart-legend').innerHTML = myChart.generateLegend();

        var expense_labels = Object.keys(expense_chart_data);
        var expense_data_values = Object.values(expense_chart_data);
        var expense_data = {
            labels: expense_labels,
            datasets: [{
                label: "Expenses",
                fillColor: "#A44442",
                strokeColor: "#A44442",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                data: expense_data_values
            }]
        }

        var ctx2 = document.getElementById("myChart2").getContext("2d");
        var myChart2 = new Chart(ctx2).Bar(expense_data, options);
        document.getElementById('myChart2-legend').innerHTML = myChart2.generateLegend();
    </script>
@endpush
