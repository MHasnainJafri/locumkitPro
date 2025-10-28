@extends('layouts.user_profile_app')

@section('content')
    <style>
        #ui-datepicker-div {
            width: 420px;
        }
        .financeform .input-group button {
            width: 17%;
        }
        .financeform .input-group input {
            width: 80%;
        }
        
        .btn {
            padding: 8px 30px;
        }
    </style>
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/employer/dashboard">My Dashboard</a></li>
                        <li><a href="#">Employer finance</a></li>
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
                        <h3>Employer finance</h3>
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
                            <div class="finance-page-head text-center">Finance overview</div>
                        </div>
                        <div class="col-md-12 pad0"></div>
                        <div class="cash_man_chart2 wholeborder">
                            <form class="add_item_form form-inline">
                                @include('components.financial-year-select')
                            </form>
                            <div class="col-md-12 pad0 marb20 canvas-scroll-wrapp">
                                <div class="col-md-6 cash_man_chart">
                                    <div class="col-md-6 row margin-top">
                                        <h1 class="mar0 text-capitalize" id="register_head_blue">Cost Per Month</h1>
                                    </div>
                                    <div class="mapdiv">
                                        <canvas id="myChart_emp" height="260" width="490" class="well"></canvas>
                                        <div id="myChart-legend_emp" class="chart-legend"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 cash_man_chart">
                                    <div class="col-md-6 row margin-top">
                                        <h1 class="mar0 text-capitalize" id="register_head_blue">Jobs Per Month</h1>
                                    </div>
                                    <div class="mapdiv">
                                        <canvas id="myChart2_emp" height="260" width="490" class="well"></canvas>
                                        <div id="myChart2-legend_emp" class="chart-legend"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 cash_table cash_table-fiexd-scroll finance-scroller">
                                <div class="">
                                    <table class="table-striped income_sum_table table table-fixed" id="example">
                                        <thead>
                                            <tr>
                                                <th class="col-md-1">Tran No</th>
                                                <th class="col-md-1">Job ID</th>
                                                <th class="col-md-2">Date</th>
                                                <th class="col-md-1">Locum ID &nbsp;&nbsp;</th>
                                                <th class="col-md-2">Rate</th>
                                                <th class="col-md-1">Bonus</th>
                                                <th class="col-md-1">Total</th>
                                                <th class="col-md-1">Paid</th>
                                                <th class="col-md-1">Paid date</th>
                                                <th class="col-md-1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td class="col-md-1" data-order="{{ $transaction->id }}"> #{{ $transaction->id }}</td>
                                                    <td class="col-md-1"> {{ $transaction->job_id }} </td>
                                                    <!--<td class="col-md-2" data-order="{{ $transaction->job_date }}"> {{ get_date_with_default_format($transaction->job_date) }} </td>-->
                                                        <td class="col-md-2" data-order="{{ $transaction->job_date }}"> {{ $transaction->paid_date ? \Carbon\Carbon::parse($transaction->job_date)->format('d-m-Y') : '' }} </td>
                                                    <td class="col-md-1"> {{ $transaction->freelancer_id ? $transaction->freelancer_id : 'N/A' }} </td>
                                                    <td class="col-md-2"> {{ set_amount_format($transaction->job_rate) }} </td>
                                                    <td class="col-md-1"> {{ $transaction->bonus ? set_amount_format($transaction->bonus) : 'N/A' }} </td>
                                                    <td class="col-md-1"> {{ set_amount_format($transaction->job_rate + $transaction->bonus) }} </td>
                                                    <td class="col-md-1">
                                                        @if ($transaction->is_paid)
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                        @else
                                                            <a href="javascript:void(0)" onclick="managebank('{{ $transaction->id }}')"> <i class="fa fa-close" aria-hidden="true"> </i></a>
                                                        @endif
                                                    </td>
                                                    <!--<td class="col-md-1"> {{ $transaction->paid_date ? get_date_with_default_format($transaction->paid_date) : 'N/A' }} </td>-->
                                                    <td class="col-md-1">
                                                        {{ $transaction->paid_date ? \Carbon\Carbon::parse($transaction->paid_date)->format('d-m-Y') : '' }}
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
                                                    <td class="col-md-1" align="center">
                                                        <div style="display: inline-flex; justify-content: center; align-items: center;">
                                                            <a href="/employer/finance/manage-finance-transaction/{{ $transaction->id }}" class="custom-btn btn btn-xs btn-info" style="margin-right: 10px;">
                                                                <i class="fa fa-fw fa-edit"></i>
                                                            </a>
                                                            <form role="form" action="/employer/finance/delete-finance-transaction/{{ $transaction->id }}" method="post" id="delete-form{{ $transaction->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="custom-btn btn btn-xs btn-danger" name="in_data_delete" value="in_data_delete" onclick="confirm_delete_rec('{{ $transaction->id }}')">
                                                                    <i class="fa fa-fw fa-close"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>


                                                    <!--<td class="col-md-1" align="center">-->
                                                    <!--    <a href="/employer/finance/manage-finance-transaction/{{ $transaction->id }}" class="custom-btn btn btn-xs btn-info"><i class="fa fa-fw fa-edit"></i></a>-->
                                                    <!--    <form role="form" action="/employer/finance/delete-finance-transaction/{{ $transaction->id }}" method="post" style="margin-top: 1px;" id="delete-form{{ $transaction->id }}">-->
                                                    <!--        @csrf -->
                                                    <!--        @method('DELETE')-->
                                                    <!--        <button type="button" class="custom-btn btn btn-xs btn-danger" name="in_data_delete" value="in_data_delete" onclick="confirm_delete_rec('{{ $transaction->id }}')"><i-->
                                                    <!--               class="fa fa-fw fa-close"></i></button>-->
                                                    <!--    </form>-->
                                                    <!--</td>-->

                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 add-new">
                                <div class="profile-edit-btn">
                                    <a href="/employer/finance/manage-finance-transaction" class="read-common-btn grad_btn pull-right">Add New</a>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div id="manage-bank" class="modal fade financepopup" role="dialog">
        <div class="modal-dialog">
            <form action="/employer/finance/update-transaction-bank-date" method="post">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Locumkit</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 pad0 financeform">
                            <div class="form-group" id="bank_date">
                                <div class="pull-left" style="display:none">
                                    <input name="in_bank" id="modal-in_bank" value="1" type="checkbox" checked> Paid
                                </div>
                                <div class="input-group" id="fordisplay" style="display: block;">
                                    <p>Please enter the date the transaction hit the bank</p>
                                    <input type="hidden" name="in_bankid" id="in_bankid">
                                    <input type="text" class="form-control financein_bankdate" name="in_bankdate" id="in_bankdate" placeholder="dd/mm/yyyy" required>
                                    <button type="submit" class="btn btn-info" name="update-bank-btn" value="update-bank-btn" id="income-bank-btn">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/frontend/locumkit-template/js/Chart.js"></script>

    <script>
        function managebank(id) {
            //$('#fordisplay').hide();
            $('#in_bankdate').val('');
            $('#modal-in_bank').attr('checked', false);
            $('#in_bankid').val(id);
            $('#manage-bank').modal('show');
        }

        function confirm_delete_rec(id) {
            //     event.preventDefault();
            $('div#alert-confirm-modal #alert-message').html('Are you sure you want to delete this transaction?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#delete-form" + id).submit();
                messageBoxClose();
            });
        }

        $(document).ready(function() {
            $('#example').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
                "order": [
                    [2, "desc"]
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [5, 7, 8, 9]
                }]
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            const employer_finance_cost = @json($employer_finance_cost);
            const currency = `{{ get_site_currency_symbol() }}`;

            var data = {
                labels: Object.keys(employer_finance_cost),
                datasets: [{
                    label: "Expense",
                    fillColor: "#A44442",
                    strokeColor: "#A44442",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    data: Object.values(employer_finance_cost)
                }]
            };

            const employer_finance_job = @json($employer_finance_job);

            var dataExpense = {
                labels: Object.keys(employer_finance_job),
                datasets: [{
                    label: "No. of jobs",
                    fillColor: "#85A04C",
                    strokeColor: "#85A04C",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    data: Object.values(employer_finance_job)
                }]
            }
            var options = {
                animation: true,
                tooltipTemplate: `<%= label %> : ${currency}  <%= value %>.00`
            };
            var options1 = {
                animation: true,
                tooltipTemplate: "<%= label %> : <%= value %>"
            };

            //Get the context of the canvas element we want to select
            var c = $('#myChart_emp');
            var ct = c.get(0).getContext('2d');
            var ctx = document.getElementById("myChart_emp").getContext("2d");
            /*********************/
            var myChart = new Chart(ctx).Bar(data, options);
            document.getElementById('myChart-legend_emp').innerHTML = myChart.generateLegend();
            //Get the context of the canvas element we want to select
            var c = $('#myChart2_emp');
            var ct = c.get(0).getContext('2d');
            var ctx = document.getElementById("myChart2_emp").getContext("2d");
            /*********************/
            var myChart2 = new Chart(ctx).Bar(dataExpense, options1);
            document.getElementById('myChart2-legend_emp').innerHTML = myChart2.generateLegend();
        });
    </script>
@endpush
