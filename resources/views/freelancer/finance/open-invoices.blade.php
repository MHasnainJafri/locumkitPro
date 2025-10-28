@extends('layouts.user_profile_app')
@php
    $days_count_set = ['0-30 days' => 0, '31-60 days' => 0, '61-90 days' => 0, '90 days +' => 0];
@endphp
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
                        <li><a href="#">Open invoices (debtor)</a></li>
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
                        <h3>Open invoices (debtor)</h3>
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
                            <div class="text-capitalize finance-page-head text-center">Open invoices (debtor)</div>
                        </div>
                        <div class="cash_man_chart2 wholeborder">
                            <form action="" class="add_item_form form-inline">
                                @include('components.financial-year-select')
                                <div class="col-md-12 cash_table cash_manag_table mart30">
                                    <div class="table-responsive table-responsive-scroll finance-scroller">
                                        <table class="table" id="datatable">
                                            <thead>
                                                <tr>
                                                    <th class="no-sort">Date</th>
                                                    <th class="no-sort">Amount</th>
                                                    <th class="no-sort">Type</th>
                                                    <th class="no-sort">Job No</th>
                                                    <th class="no-sort">Category</th>
                                                    <th class="no-sort">Store</th>
                                                    <th width="100">0-30 days</th>
                                                    <th width="105">31-60 days</th>
                                                    <th width="105">61-90 days</th>
                                                    <th width="95">90 days +</th>
                                                    <th class="no-sort">Bank</th>
                                                    <th class="no-sort">Invoice</th>
                                                    <th class="no-sort">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($income_records as $record)
                                                    @php
                                                        $days = get_relative_days_for_job($record->job_date);
                                                    @endphp
                                                    <tr>
                                                        <td> {{ $record->job_date }} </td>
                                                        <td> {{ set_amount_format($record->job_rate) }} </td>
                                                        <td> {{ $record->get_job_type() }} </td>
                                                        <td> {{ $record->job_id ? $record->job_id : 'N/A' }} </td>
                                                        <td> {{ $record->get_income_type() }} </td>
                                                        <td> {{ $record->store ? $record->store : 'N/A' }} </td>
                                                        <td>
                                                            @if ($days <= 30)
                                                                @php $days_count_set['0-30 days']++ @endphp
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-close" aria-hidden="true"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($days > 30 && $days <= 60)
                                                                @php $days_count_set['31-60 days']++ @endphp
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-close" aria-hidden="true"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($days > 60 && $days <= 90)
                                                                @php $days_count_set['61-90 days']++ @endphp
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-close" aria-hidden="true"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($days > 90)
                                                                @php $days_count_set['90 days +']++ @endphp
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-close" aria-hidden="true"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a title="Manage Bank Status" href="javascript:void(0);" onclick="managebankincome('{{ $record->id }}')"><i class="fa fa-close" aria-hidden="true"></i></a>
                                                        </td>
                                                        <td>
                                                            @if ($record->is_invoice_required)
                                                                <a title="Manage Invoice" href="javascript:void(0);" onclick="manageInvoiceRequired('{{ $record->id }}', '{{ $record->job_id }}', 0)">
                                                                    <i class="fa fa-check" aria-hidden="true"></i></a>
                                                            @else
                                                                <a title="Manage Invoice" href="javascript:void(0);" onclick="manageInvoiceRequired('{{ $record->id }}', '{{ $record->job_id }}', 1)">
                                                                    <i class="fa fa-close" aria-hidden="true"></i></a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($record->is_invoice_required)
                                                                @if ($record->invoice_id)
                                                                    <button type="button" class="btn btn-block btn-sm btn-info disabled">Invoice Sent</button>
                                                                @else
                                                                    <a type="button" class="btn btn-block btn-sm btn-info" href="/freelancer/send-invoice/{{ $record->id }}">&nbsp; Send Invoice &nbsp;</a>
                                                                @endif
                                                            @else
                                                                <span class='text-danger'>Not Required</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="13" style="text-align: center;"> No data present </td>
                                                    </tr>
                                                @endforelse

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-3"></div>
                                <div class="col-md-6 cash_man_chart2">

                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6 padl0 open-inv-canvas">
                                        <div class="mapdiv">
                                            <canvas id="myChart" height="200" class="well"></canvas>
                                            <div id="myChart-legend" class="chart-legend"></div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <div id="manage-invoice_required" class="modal fade financepopup" role="dialog">
        <div class="modal-dialog">
            <form action="/freelancer/update-invoice" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Manage Invoices For Job Ref : <span id="jobno-ref"></span></h4>
                    </div>
                    <div class="modal-body" style="text-align: center;">
                        <div class="col-md-12 pad0 financeform">
                            <div class="form-group" id="bank_date">
                                <div class="pull-left">
                                    <input type="hidden" name="id_income" id="id_income">
                                    <p>Does this job require an invoice to be sent?</p>
                                    <input name="invoice-req-val" id="invoice-req-val-yes" value="1" type="radio" checked> Yes
                                    <input name="invoice-req-val" id="invoice-req-val-no" value="0" type="radio"> No
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info pull-right" id="invoice-required" name="invoice-required" value="invoice-required">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/frontend/locumkit-template/js/Chart.js"></script>
    <script>
        function manageInvoiceRequired(id, jobno, status) {
            if (status == '1') {
                $('#invoice-req-val-no').attr('checked', true);
            } else {
                $('#invoice-req-val-yes').attr('checked', true);
            }

            $('#id_income').val(id);
            $('#jobno-ref').html(jobno);
            $('#manage-invoice_required').modal('show');
        }
        $(document).ready(function() {
            $('#datatable').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
                "ordering": true,
                order: [],
                columnDefs: [{
                    orderable: false,
                    targets: "no-sort"
                }]
            });
        });

        const data_set = @json($days_count_set);
        const labels = Object.keys(data_set);
        const data_values = Object.values(data_set);

        var data = {
            labels: labels,
            datasets: [{
                label: "Number of invoices outstanding",
                fillColor: "#4F81BD",
                strokeColor: "#4F81BD",
                pointColor: "#4F81BD",
                pointStrokeColor: "#fff",
                data: data_values
            }]
        };
        var options = {
            animation: true,
            datasetFill: false,
            pointDot: true
        };

        var ctx = document.getElementById("myChart").getContext("2d");
        var myChart = new Chart(ctx).Bar(data, options);
        document.getElementById('myChart-legend').innerHTML = myChart.generateLegend();
    </script>
@endpush
