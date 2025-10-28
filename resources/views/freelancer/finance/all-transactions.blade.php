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
                        <li><a href="#">All Transactions</a></li>
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
                        <h3>All Transactions </h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <section>
                        <div class="col-md-12 pad0">
                            <div class="finance-page-head marb0 text-center">All transactions</div>
                        </div>
                    </section>
                    <div class="col-md-12 in_ex_pr_box emply-finance">
                        <div class="">
                            <div class="col-sm-4 col-md-4 text-center">
                                <h1 class="mar0" id="register_head_blue">Income</h1>
                                <h2 class="mar0">{{ set_amount_format($total_income) }}</h2>
                            </div>
                            <div class="col-sm-4 col-md-4 text-center">
                                <h1 class="mar0" id="register_head_blue">Expenses</h1>
                                <h2 class="mar0"> {{ set_amount_format($total_expense) }} </h2>
                            </div>
                            <div class="col-sm-4 col-md-4 text-center">
                                <h1 class="mar0" id="register_head_blue">Profit</h1>
                                <h2 class="mar0"> {{ set_amount_format($total_income - $total_expense) }} </h2>
                            </div>
                        </div>
                    </div>
                    <form class="add_item_form form-inline">
                        <input type="hidden" name="show" value="{{ $show }}">
                        @include('components.financial-year-select')
                    </form>

                    <section id="latest-expense" class="latest-expense-half">
                        <div class="col-md-12 pad0 income_tra_sc all_transection_sc">
                            <div class="col-md-12 col-sm-12 income padl0">
                                <div class="col-md-12 income-btn income-btn-scroll">
                                    <div class="blue-tab-scroll">
                                        <div class="col-xs-4 col-sm-4 col-md-4 pad0 {{ $show == 'all' ? 'active' : '' }}">
                                            <div class="profile-edit-btn incomee-btn">
                                                <a class="gradient-threeline incomm" href="/freelancer/all-transaction">ALL</a>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-4 pad0 {{ $show == 'income' ? 'active' : '' }}">
                                            <div class="profile-edit-btn incomee-btn">
                                                <a class="gradient-threeline incomm" href="/freelancer/all-transaction?show=income">INCOME</a>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-4 pad0 {{ $show == 'expense' ? 'active' : '' }}">
                                            <div class="profile-edit-btn incomee-btn">
                                                <a class="gradient-threeline incomm" href="/freelancer/all-transaction?show=expense">EXPENSES</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 all_tra_tab">
                                    <div class="table-responsive table-responsive-scroll finance-scroller">
                                        <table class="table-striped table" id="all-transaction">
                                            <thead>
                                                <tr>
                                                    <th>Tran&nbsp;No</th>
                                                    <th>Job&nbsp;No</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th style="width:30%;">Description</th>
                                                    <th>Category</th>
                                                    <th>Bank</th>
                                                    <th>Bank&nbsp;Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($records as $record)
                                                    @php
                                                        if (get_class($record) == 'App\Models\FinanceIncome') {
                                                            $type = 'income';
                                                            $descHTML = "Store: $record->store; Location: $record->location; Supplier: $record->supplier";
                                                        } else {
                                                            $type = 'expense';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td data-order="{{ $record->getTransactionNumber() }}"> {{ $record->getTransactionNumber() }}</td>
                                                        <td>{{ $record->job_id ? $record->job_id : 'N/A' }}</td>
                                                        <td data-order="{{ $record->job_date }}">
                                                            {{ $record->job_date ? \Carbon\Carbon::parse($record->job_date)->format('d/m/Y') : 'N/A' }}
                                                        </td>

                                                        <td> {{ set_amount_format($record->job_rate) }} </td>
                                                        <td style="width: 30%;">
                                                            @if ($type == 'income')
                                                                <a href="javascript:void(0);" onclick="detailDescription('{{ $descHTML }}')">{{ substr($descHTML, 0, 30) }}...</a>
                                                            @else
                                                                @if ($record->description && $record->description != '')
                                                                    <a href="javascript:void(0);" onclick="detailDescription('{{ $record->description }}')">{{ substr($record->description, 0, 30) }}...</a>
                                                                @else
                                                                    N/A
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (get_class($record) == 'App\Models\FinanceIncome')
                                                                {{ $record->get_income_type() }}
                                                            @else
                                                                {{ $record->expense_type?->expense }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($record->is_bank_transaction_completed)
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                            @else
                                                                @if ($type == 'income')
                                                                    <a title="Manage Bank Status" href="javascript:void(0);" onclick="managebankincome('{{ $record->id }}')">
                                                                        <i class="fa fa-close" aria-hidden="true"></i></a>
                                                                @else
                                                                    <a title="Manage Bank Status" href="javascript:void(0);" onclick="managebankexpanse('{{ $record->id }}')">
                                                                        <i class="fa fa-close" aria-hidden="true"></i></a>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td data-order="{{ $record->bank_transaction_date }}">
                                                            {{ $record->bank_transaction_date ? \Carbon\Carbon::parse($record->bank_transaction_date)->format('d/m/Y') : 'N/A' }}
                                                        </td>

                                                        <td>
                                                            @if ($type == 'income')
                                                                <a href="/freelancer/edit-income/{{ $record->id }}" class="btn btn-xs btn-info"><i class="fa fa-fw fa-edit"></i></a>
                                                                <form role="form" action="/freelancer/delete-income/{{ $record->id }}" method="post" id="delete-in{{ $record->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn btn-xs btn-danger" onclick="confirm_delete_in('{{ $record->id }}')"><i class="fa fa-fw fa-close"></i></button>
                                                                </form>
                                                            @else
                                                                <a href="/freelancer/edit-expense/{{ $record->id }}" class="btn btn-xs btn-info"><i class="fa fa-fw fa-edit"></i></a>
                                                                <form role="form" action="/freelancer/delete-expense/{{ $record->id }}" method="post" id="delete-ex{{ $record->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn btn-xs btn-danger" onclick="confirm_delete_ex('{{ $record->id }}')"><i class="fa fa-fw fa-close"></i></button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="action-btn d-flex" style="justify-content: end; gap: 1rem;flex-wrap: wrap; display: flex; align-items: center;">
                                        <a href="/freelancer/add-expense" class="read-common-btn grad_btn">ADD EXPENSE</a>
                                        <a href="/freelancer/add-income" class="read-common-btn grad_btn">ADD INCOME</a>
                                        <form action="/freelancer/all-transactions/export" method="post" style="justify-content: end; gap: 1rem; display: flex; align-items: center;">
                                            @csrf
                                            <input type="hidden" name="type" value="{{ $show }}" style="display: none;">
                                            <select class="form-control" name="export_type" id="export_type">
                                                <option value="" disabled selected>Choose export type</option>
                                                <option value="xlsx">Excel</option>
                                                <option value="csv">CSV</option>
                                                <option value="pdf">Pdf</option>
                                            </select>
                                            <button class="read-common-btn grad_btn">Export</button>
                                        </form>
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
    <script>
        $(document).ready(function() {
            $('#all-transaction').DataTable({
                searching: false,
                paging: false,
                "bInfo": false,
                "order": [
                    [2, "desc"]
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [4, 5, 6, 7, 8]
                }]
            });
        });

        function confirm_delete_in(id) {
            $('div#alert-confirm-modal #alert-message').html('Do you really want to delete this transaction?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#delete-in" + id).submit();
                messageBoxClose();
            });
        }

        function detailDescription(description) {
            $('#detail-description .modal-body').html('<p>' + description + '</p>');
            $('#detail-description').modal('show');
        }

        function confirm_delete_ex(id) {
            $('div#alert-confirm-modal #alert-message').html('Do you really want to delete this transaction?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#delete-ex" + id).submit();
                messageBoxClose();
            });
        }
    </script>
@endpush
