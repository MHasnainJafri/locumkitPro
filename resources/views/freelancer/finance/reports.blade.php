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
                        <li><a href="">Reports</a></li>
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
                        <h3>Reports</h3>
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
                            <div class="marb0 text-capitalize finance-page-head text-center">Reports</div>
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
                            <section id="transaction-see" class="transaction-see">

                                <div class="col-md-12">
                                    <ul class="report-btn-list-group">
                                        <li><a class="btn btn-default btn-block" href="/freelancer/all-transaction">All transaction</a></li>
                                        <li><a class="btn btn-default btn-block" href="/freelancer/cash-movement-report">Cash flow</a></li>
                                        <li><a class="btn btn-default btn-block" href="/freelancer/open-invoices">Open invoices (Debtor)</a></li>
                                        <li><a class="btn btn-default btn-block" href="/freelancer/weekly-report">Weekly report</a></li>
                                        <li><a class="btn btn-default btn-block" href="/freelancer/income-by-area">Income by area</a></li>
                                        <li><a class="btn btn-default btn-block" href="/freelancer/income-filter">Income by category</a></li>
                                        <li><a class="btn btn-default btn-block" href="/freelancer/income-by-supplier">Income by supplier</a></li>
                                        <li><a class="btn btn-default btn-block" href="/freelancer/expenses-type-filter">Expense by category</a></li>
                                        <li><a class="btn btn-default btn-block" href="/freelancer/net-income">Net income</a></li>
                                    </ul>
                                </div>
                            </section>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
