@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\FinanceController')
    <div class="main-container container">
        @include('admin.layout.sidebar')

        <div class="col-lg-12 main-content">
            <div id="breadcrumbs" class="breadcrumbs">
                <div id="menu-toggler-container" class="hidden-lg">
                    <span id="menu-toggler">
                        <i class="glyphicon glyphicon-new-window"></i>
                        <span class="menu-toggler-text">Menu</span>
                    </span>
                </div>
                <ul class="breadcrumb">
                </ul>
            </div>
            <div class="page-content">
                <div id="tabs">

                    <div class="qus-tabs financead">
                        <form action>
                            <div class="form-group pull-right">
                                <div class="input-group pull-right">
                                    <select name="y" onchange="this.form.submit();" class="form-control pull-right">
                                        <option value="2023" {{ $controller->year == 2023 ? 'selected' : '' }}><a
                                                href="{{ route('admin.finance.index', ['y' => '2023', 'c' => $controller->year]) }}">2023</a>
                                        </option>
                                        <option value="2022"><a href="/admin/config/user/finance?y=2022&c=3">2022</a>
                                        </option>
                                    </select>
                                    <input type="hidden" value="3" name="c">
                                    <label class="pull-right">SELECT FINANCIAL YEAR</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="fre-tab">
                    <div class="cat-tabs">
                        <ul>
                            @foreach ($controller->professionslist as $profession)
                                <li {{ $controller->profession == $profession->id ? ' class=active' : '' }}>
                                    <a
                                        href="{{ route('admin.finance.index', ['y' => $controller->year, 'c' => $profession->id]) }}">{{ $profession->name }}</a>

                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <table class="table clickable table-striped table-hover">
                        <colgroup>
                            <col width="20%">
                            <col width="20%">
                            <col width="20%">
                            <col width="20%">
                            <col width="20%">
                            <col width="20%">
                            <col width="20%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="text-center">User ID</th>
                                <th class="text-center"><a href="javascript:void(0);"
                                        onclick="changeUserNameOrder(2);">Login</a></th>

                                <th class="text-center">Financial Year</th>
                                <th class="text-center">Profit&nbsp;and&nbsp;loss</th>
                                <th class="text-center">Balance&nbsp;sheet</th>
                                <th class="text-center">All&nbsp;Transactions</th>
                                <th class="text-center">Supplier&nbsp;List</th>
                            </tr>
                        </thead>
                        <tbody style="background-color: red !important;">
                            @foreach ($filteredUsers as $user)
                                 <tr class="bg-white" >
                                <td class="text-center">{{$user->id}}</td>
                                <td class="text-center">{{$user->login}}</td>
                                <td class="text-center">
                                    {{$user->financial_year}}
                                </td>
                                <td class="text-center">
                                    <a href="/admin/config/user/finance/profitloss/75/2023" class="btn btn-xs btn-info">
                                        Create </a>
                                </td>
                                <td class="text-center">
                                    <a href="/admin/config/user/finance/balancesheet/75/2023" class="btn btn-xs btn-info">
                                        Create </a>
                                </td>
                                <td class="text-center"><a href="/admin/config/user/finance/all-transactions/75/2023"
                                        class="btn btn-xs btn-info">All Transactions</a></td>
                                <td class="text-center"><a href="{{ route('listSupplier') }}"
                                        class="btn btn-xs btn-info">Supplier List</a></td>
                            </tr>
                            @endforeach
                           
                           
                        </tbody>
                    </table>
                    <div class="pagination">
                        <link rel="stylesheet"
                            href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
                        <p class="clearfix">
                        <ul class="paginator-div">
                        </ul>
                        </p>
                    </div>
                </div>
                <script type="text/javascript">
                    Gc.initTableList();

                    function changeUserNameOrder(order) {
                        $.ajax({
                            'url': '/admin/config/user',
                            'type': 'POST',
                            'data': {
                                'setUserNameOrder': order
                            },
                            'success': function(result) { //alert('question'+result);
                                //alert("Order change");
                                location.reload();
                            }
                        });
                    }

                    function changeUserFNameOrder(order) {
                        $.ajax({
                            'url': '/admin/config/user',
                            'type': 'POST',
                            'data': {
                                'setUserFNameOrder': order
                            },
                            'success': function(result) { //alert('question'+result);
                                //alert("Order change");
                                location.reload();
                            }
                        });
                    }

                    function changeUserLNameOrder(order) {
                        $.ajax({
                            'url': '/admin/config/user',
                            'type': 'POST',
                            'data': {
                                'setUserLNameOrder': order
                            },
                            'success': function(result) { //alert('question'+result);
                                //alert("Order change");
                                location.reload();
                            }
                        });
                    }

                    function changeUserEmailOrder(order) {
                        $.ajax({
                            'url': '/admin/config/user',
                            'type': 'POST',
                            'data': {
                                'setUserEmailOrder': order
                            },
                            'success': function(result) { //alert('question'+result);
                                //alert("Order change");
                                location.reload();
                            }
                        });
                    }
                </script>
                <style>
                    div#fre-tab,
                    div#emp-tab,
                    .financead {
                        float: left;
                        width: 100%;
                    }

                    .financead {}

                    .financead .form-group,
                    .financead .input-group {
                        width: 100%;
                    }

                    .financead label {
                        float: right;
                        font-size: 12px;
                        font-weight: bold;
                        letter-spacing: 1px;
                        padding: 8px 10px;
                    }

                    .financead select {
                        width: 150px !important;
                    }
                </style>
            </div>
        </div>
    </div>
@endsection
