@extends('admin.layout.app')
@section('content')
@inject('controller', 'App\Http\Controllers\admin\FinanceController')
<style>
    .d-none {
        display: none !important;
    }

    .d-block {
        display: block !important;
    }
    .active{
        background: #00A9E0 !important;
        border-top: 1px solid #855D10 !important;        
    }
</style>
<style>
    .flash-message {
    transition: opacity 0.5s ease-in-out;
}

</style>
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

        <div class="page-content" style="margin-top: -10px">
            @if (request('success'))
    <div class="alert alert-success flash-message">Updated Successfully</div>
@endif

            <div id="tabs">
                        <form method="GET" action="{{ route('finance.record') }}" class="form-inline mb-3">
                            <input type="text" name="search" class="form-control mr-2" placeholder="Search..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                <div class="qus-tabs financead">
                    <form action="#">
                        <div class="form-group pull-right">
                            <div class="input-group pull-right">
                            <select name="y" onchange="this.form.submit();" class="form-control pull-right">
                                <option value="">Select Year</option>
                                @foreach($available_year as $key => $value)
                                    <option value="{{ $value }}" @if(request('y') == $value) selected @endif>{{ $value }}</option>
                                @endforeach
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
                    <ul style="display: flex; flex-direction: row;">
                        @php
                        $iterator = 0;
                        @endphp
                        @foreach($professions as $key => $value)
                        @if($iterator == '0')
                            <li id="All-content" class="d-block" style="margin:5px 0px !important;"><a href="#">All</a>
                            </li>
                            @php
                            $iterator = $iterator + 1;
                            @endphp
                        @endif
                        @if($value->is_active != '0' && $value->name == 'Dentistry')
                        <li id="Dentistry-content" class="d-block" style="margin:5px 0px !important;"><a href="#">Dentistry</a>
                        </li>
                        @endif
                        @if($value->is_active != '0' && $value->name == 'Optometry')
                        <li id="Optometry-content" class="d-block" style="margin:5px 0px !important;"><a href="#">Optometry</a>
                        </li>
                        @endif
                        @if($value->is_active != '0' && $value->name == 'Pharmacy')
                        <li id="Pharmacy-content" class="d-block" style="margin:5px 0px !important;"><a href="#">Pharmacy</a>
                        </li>
                        @endif
                        @if($value->is_active != '0' && $value->name == 'Domiciliary Opticians')
                        <li id="Domiciliary_Opticians-content" class="d-block" style="margin:5px 0px !important;"><a href="#">Domiciliary Opticians</a>
                        </li>
                        @endif
                        @if($value->is_active != '0' && $value->name == 'Audiologists')
                        <li id="Audiologists-content" class="d-block" style="margin:5px 0px !important;"><a href="#">Audiologists</a>
                        </li>
                        @endif
                        @if($value->is_active != '0' && $value->name == 'Dispensing Optician / Contact lens Optician')
                        <li id="Dispensing_Optician-content" class="d-block" style="margin:5px 0px !important;"><a href="#">Dispensing Optician / Contact lens Optician</a>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </div>
                <table class="table clickable table-striped table-hover d-block" id="All-click">
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
                        <tr style="background-color: white !important;">
                            <th class="text-center">User ID</th>
                            <th class="text-center"><a href="javascript:void(0);" onclick="changeUserNameOrder(2);">Login</a></th>

                            <th class="text-center">Financial Year</th>
                            <th class="text-center">Profit&nbsp;and&nbsp;loss</th>
                            <th class="text-center">Balance&nbsp;sheet</th>
                            <th class="text-center">All&nbsp;Transactions</th>
                            <th class="text-center">Supplier&nbsp;List</th>
                        </tr>
                    </thead>
                    <tbody class="">
                      
                        @foreach($year as $key => $numusers)
                            @foreach($numusers as $keys => $numuser)
                                    <tr style="background-color: white !important;">
                                        <td class="text-center">{{ $numuser['user_id'] }}</td>
                                        <td class="text-center">{{ $numuser['login'] }}</td>
                                        <td class="text-center">
                                            {{$numuser['start_month']}} - {{$numuser['end_month']}}({{$key-1}}-{{$key}})
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('finance.profit.loss', ['id' => $numuser['user_id'], 'year' => $key]) }}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('finance.balancesheet', ['id' => $numuser['user_id'], 'year' => $key])}}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center"><a href="{{route('finance.alltransactions', ['id' => $numuser['user_id'], 'year' => $numuser['fin_year']])}}" class="btn btn-xs btn-info">All Transactions</a></td>
                                        <td class="text-center"><a href="{{route('finance.supplierlist', ['id' => $numuser['user_id'] ])}}" class="btn btn-xs btn-info">Supplier List</a></td>
                                    </tr>
                            @endforeach
                        @endforeach
                        
                    </tbody>
                </table>
                <table class="table clickable table-striped table-hover d-none" id="Dentistry">
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
                        <tr style="background-color: white !important;">
                            <th class="text-center">User ID</th>
                            <th class="text-center"><a href="javascript:void(0);" onclick="changeUserNameOrder(2);">Login</a></th>

                            <th class="text-center">Financial Year</th>
                            <th class="text-center">Profit&nbsp;and&nbsp;loss</th>
                            <th class="text-center">Balance&nbsp;sheet</th>
                            <th class="text-center">All&nbsp;Transactions</th>
                            <th class="text-center">Supplier&nbsp;List</th>
                        </tr>
                    </thead>
                    <tbody class="">

                        @foreach($year as $key => $numusers)
                            @foreach($numusers as $keys => $numuser)
                                @if($numuser['user_acl_profession_id'] == '1')
                                    <tr style="background-color: white !important;">
                                        <td class="text-center">{{ $numuser['user_id'] }}</td>
                                        <td class="text-center">{{ $numuser['login'] }}</td>
                                        <td class="text-center">
                                            {{$numuser['start_month']}} - {{$numuser['end_month']}}({{$key-1}}-{{$key}})
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('finance.profit.loss', ['id' => $numuser['user_id'], 'year' => $key]) }}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('finance.balancesheet', ['id' => $numuser['user_id'], 'year' => $key])}}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center"><a href="{{route('finance.alltransactions', ['id' => $numuser['user_id'], 'year' => $numuser['fin_year']])}}" class="btn btn-xs btn-info">All Transactions</a></td>
                                        <td class="text-center"><a href="{{route('finance.supplierlist', ['id' => $numuser['user_id'] ])}}" class="btn btn-xs btn-info">Supplier List</a></td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>                    
                </table>
                <table class="table clickable table-striped table-hover d-none" id="Optometry">
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
                        <tr style="background-color: white !important;">
                            <th class="text-center">User ID</th>
                            <th class="text-center"><a href="javascript:void(0);" onclick="changeUserNameOrder(2);">Login</a></th>

                            <th class="text-center">Financial Year</th>
                            <th class="text-center">Profit&nbsp;and&nbsp;loss</th>
                            <th class="text-center">Balance&nbsp;sheet</th>
                            <th class="text-center">All&nbsp;Transactions</th>
                            <th class="text-center">Supplier&nbsp;List</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($year as $key => $numusers)
                            @foreach($numusers as $keys => $numuser)
                                @if($numuser['user_acl_profession_id'] == '3')
                                    <tr style="background-color: white !important;">
                                        <td class="text-center">{{ $numuser['user_id'] }}</td>
                                        <td class="text-center">{{ $numuser['login'] }}</td>
                                        <td class="text-center">
                                            {{$numuser['start_month']}} - {{$numuser['end_month']}}({{$key-1}}-{{$key}})
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('finance.profit.loss', ['id' => $numuser['user_id'], 'year' => $key]) }}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('finance.balancesheet', ['id' => $numuser['user_id'], 'year' => $key])}}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center"><a href="{{route('finance.alltransactions', ['id' => $numuser['user_id'], 'year' => $numuser['fin_year']])}}" class="btn btn-xs btn-info">All Transactions</a></td>
                                        <td class="text-center"><a href="{{route('finance.supplierlist', ['id' => $numuser['user_id'] ])}}" class="btn btn-xs btn-info">Supplier List</a></td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <table class="table clickable table-striped table-hover d-none" id="Pharmacy">
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
                        <tr style="background-color: white !important;">
                            <th class="text-center">User ID</th>
                            <th class="text-center"><a href="javascript:void(0);" onclick="changeUserNameOrder(2);">Login</a></th>

                            <th class="text-center">Financial Year</th>
                            <th class="text-center">Profit&nbsp;and&nbsp;loss</th>
                            <th class="text-center">Balance&nbsp;sheet</th>
                            <th class="text-center">All&nbsp;Transactions</th>
                            <th class="text-center">Supplier&nbsp;List</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach($year as $key => $numusers)
                            @foreach($numusers as $keys => $numuser)
                                @if($numuser['user_acl_profession_id'] == '4')
                                    <tr style="background-color: white !important;">
                                        <td class="text-center">{{ $numuser['user_id'] }}</td>
                                        <td class="text-center">{{ $numuser['login'] }}</td>
                                        <td class="text-center">
                                            {{$numuser['start_month']}} - {{$numuser['end_month']}}({{$key-1}}-{{$key}})
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('finance.profit.loss', ['id' => $numuser['user_id'], 'year' => $key]) }}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('finance.balancesheet', ['id' => $numuser['user_id'], 'year' => $key])}}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center"><a href="{{route('finance.alltransactions', ['id' => $numuser['user_id'], 'year' => $numuser['fin_year']])}}" class="btn btn-xs btn-info">All Transactions</a></td>
                                        <td class="text-center"><a href="{{route('finance.supplierlist', ['id' => $numuser['user_id'] ])}}" class="btn btn-xs btn-info">Supplier List</a></td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>               
                </table>
                <table class="table clickable table-striped table-hover d-none" id="Domiciliary_Opticians">
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
                        <tr style="background-color: white !important;">
                            <th class="text-center">User ID</th>
                            <th class="text-center"><a href="javascript:void(0);" onclick="changeUserNameOrder(2);">Login</a></th>

                            <th class="text-center">Financial Year</th>
                            <th class="text-center">Profit&nbsp;and&nbsp;loss</th>
                            <th class="text-center">Balance&nbsp;sheet</th>
                            <th class="text-center">All&nbsp;Transactions</th>
                            <th class="text-center">Supplier&nbsp;List</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($year as $key => $numusers)
                            @foreach($numusers as $keys => $numuser)
                                @if($numuser['user_acl_profession_id'] == '8')
                                    <tr style="background-color: white !important;">
                                        <td class="text-center">{{ $numuser['user_id'] }}</td>
                                        <td class="text-center">{{ $numuser['login'] }}</td>
                                        <td class="text-center">
                                            {{$numuser['start_month']}} - {{$numuser['end_month']}}({{$key-1}}-{{$key}})
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('finance.profit.loss', ['id' => $numuser['user_id'], 'year' => $key]) }}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('finance.balancesheet', ['id' => $numuser['user_id'], 'year' => $key])}}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center"><a href="{{route('finance.alltransactions', ['id' => $numuser['user_id'], 'year' => $numuser['fin_year']])}}" class="btn btn-xs btn-info">All Transactions</a></td>
                                        <td class="text-center"><a href="{{route('finance.supplierlist', ['id' => $numuser['user_id'] ])}}" class="btn btn-xs btn-info">Supplier List</a></td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <table class="table clickable table-striped table-hover d-none" id="Audiologists">
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
                        <tr style="background-color: white !important;">
                            <th class="text-center">User ID</th>
                            <th class="text-center"><a href="javascript:void(0);" onclick="changeUserNameOrder(2);">Login</a></th>

                            <th class="text-center">Financial Year</th>
                            <th class="text-center">Profit&nbsp;and&nbsp;loss</th>
                            <th class="text-center">Balance&nbsp;sheet</th>
                            <th class="text-center">All&nbsp;Transactions</th>
                            <th class="text-center">Supplier&nbsp;List</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($year as $key => $numusers)
                            @foreach($numusers as $keys => $numuser)
                                @if($numuser['user_acl_profession_id'] == '9')
                                    <tr style="background-color: white !important;">
                                        <td class="text-center">{{ $numuser['user_id'] }}</td>
                                        <td class="text-center">{{ $numuser['login'] }}</td>
                                        <td class="text-center">
                                            {{$numuser['start_month']}} - {{$numuser['end_month']}}({{$key-1}}-{{$key}})
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('finance.profit.loss', ['id' => $numuser['user_id'], 'year' => $key]) }}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('finance.balancesheet', ['id' => $numuser['user_id'], 'year' => $key])}}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center"><a href="{{route('finance.alltransactions', ['id' => $numuser['user_id'], 'year' => $numuser['fin_year']])}}" class="btn btn-xs btn-info">All Transactions</a></td>
                                        <td class="text-center"><a href="{{route('finance.supplierlist', ['id' => $numuser['user_id'] ])}}" class="btn btn-xs btn-info">Supplier List</a></td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <table class="table clickable table-striped table-hover d-none" id="Dispensing_Optician">
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
                        <tr style="background-color: white !important;">
                            <th class="text-center">User ID</th>
                            <th class="text-center"><a href="javascript:void(0);" onclick="changeUserNameOrder(2);">Login</a></th>

                            <th class="text-center">Financial Year</th>
                            <th class="text-center">Profit&nbsp;and&nbsp;loss</th>
                            <th class="text-center">Balance&nbsp;sheet</th>
                            <th class="text-center">All&nbsp;Transactions</th>
                            <th class="text-center">Supplier&nbsp;List</th>
                        </tr>
                    </thead>
                    <tbody>


                        
                        @foreach($year as $key => $numusers)
                            @foreach($numusers as $keys => $numuser)
                                @if($numuser['user_acl_profession_id'] == '10')
                                    <tr style="background-color: white !important;">
                                        <td class="text-center">{{ $numuser['user_id'] }}</td>
                                        <td class="text-center">{{ $numuser['login'] }}</td>
                                        <td class="text-center">
                                            {{$numuser['start_month']}} - {{$numuser['end_month']}}({{$key-1}}-{{$key}})
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('finance.profit.loss', ['id' => $numuser['user_id'], 'year' => $key]) }}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('finance.balancesheet', ['id' => $numuser['user_id'], 'year' => $key])}}" class="btn btn-xs btn-info">&nbsp; View &nbsp;</a>
                                        </td>
                                        <td class="text-center"><a href="{{route('finance.alltransactions', ['id' => $numuser['user_id'], 'year' => $numuser['fin_year']])}}" class="btn btn-xs btn-info">All Transactions</a></td>
                                        <td class="text-center"><a href="{{route('finance.supplierlist', ['id' => $numuser['user_id'] ])}}" class="btn btn-xs btn-info">Supplier List</a></td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination">
                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
                    <p class="clearfix">
                    </p>
                    <ul class="paginator-div">
                    </ul>
                    <p></p>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    $("#All-content").on("click", function(e) {
                        e.preventDefault();

                        $("#All-click").addClass("active d-block").remove('d-none');
                        $("#Dispensing_Optician").addClass("d-none").removeClass("d-block active");
                        $("#Audiologists").addClass("d-none").removeClass("d-block active");
                        $("#Domiciliary_Opticians").addClass("d-none").removeClass("d-block", "active");
                        $("#Pharmacy").addClass("d-none").removeClass("d-block active");
                        $("#Dentistry").addClass("d-none").removeClass("d-block active");
                        $("#Optometry").addClass("d-none").removeClass("d-block active");
                    });
                    $("#Dispensing_Optician-content").on("click", function(e) {
                        e.preventDefault();

                        $("#Dispensing_Optician").addClass("active d-block").removeClass("d-none");
                        $("#Domiciliary_Opticians").removeClass("d-block active").addClass("d-none");
                        $("#All-click").removeClass("d-block active").addClass("d-none");
                        $("#Audiologists").removeClass("d-block active").addClass("d-none");
                        $("#Optometry").removeClass("d-block active").addClass("d-none");
                        $("#Dentistry").removeClass("d-block active").addClass("d-none");
                        $("#Pharmacy").removeClass("d-block active").addClass("d-none");
                    });
                    $("#Domiciliary_Opticians-content").on("click", function(e) {
                        e.preventDefault();

                        $("#Domiciliary_Opticians").addClass("d-block active").removeClass("d-none");
                        $("#Dispensing_Optician").addClass("active d-block").removeClass("d-none");
                        $("#All-click").removeClass("d-block active").addClass("d-none");
                        $("#Audiologists").removeClass("d-block active").addClass("d-none");
                        $("#Optometry").removeClass("d-block active").addClass("d-none");
                        $("#Dentistry").removeClass("d-block active").addClass("d-none");
                        $("#Pharmacy").removeClass("d-block active").addClass("d-none");                        
                    });
                    $("#Pharmacy-content").on("click", function(e) {
                        e.preventDefault();

                        $("#Pharmacy").addClass("d-block", "active").removeClass("d-none");
                        $("#Dispensing_Optician", "#All-click", "#Audiologists", "#Optometry", "#Dentistry", "#Domiciliary_Opticians").removeClass("d-block active").addClass("d-none");
                    });
                    $("#Dentistry-content").on("click", function(e) {
                        e.preventDefault();

                        $("#Dentistry").addClass("d-block active").removeClass("d-none");
                        $("#Dispensing_Optician, #All-click, #Audiologists, #Optometry, #Pharmacy, #Domiciliary_Opticians").removeClass("d-block active").addClass("d-none");
                    });
                    $("#Optometry-content").on("click", function(e) {
                        e.preventDefault();

                        $("#Optometry").addClass("d-block active").removeClass("d-none");
                        $("#Dispensing_Optician").removeClass("active d-block").addClass("d-none");
                        $("#Domiciliary_Opticians").removeClass("d-block active").addClass("d-none");
                        $("#All-click").removeClass("d-block active").addClass("d-none");
                        $("#Audiologists").removeClass("d-block active").addClass("d-none");
                        $("#Dentistry").removeClass("d-block active").addClass("d-none");
                        $("#Pharmacy").removeClass("d-block active").addClass("d-none"); 
                    });
                    $("#Audiologists-content").on("click", function(e) {
                        e.preventDefault();

                        $("#Audiologists").addClass("d-block active").removeClass("d-none");
                        $("#Dispensing_Optician").removeClass("active d-block").addClass("d-none");
                        $("#Domiciliary_Opticians").removeClass("d-block active").addClass("d-none");
                        $("#All-click").removeClass("d-block active").addClass("d-none");
                        $("#Optometry").removeClass("d-block active").addClass("d-none");
                        $("#Dentistry").removeClass("d-block active").addClass("d-none");
                        $("#Pharmacy").removeClass("d-block active").addClass("d-none"); 
                    });
                });
            </script>

            <script type="text/javascript">
                Gc.initTableList();

                function changeUserNameOrder(order) {
                    $.ajax({
                        'url': '/admin/config/user',
                        'type': 'POST',
                        'data': {
                            'setUserNameOrder': order
                        },
                        'success': function(result) {
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
                        'success': function(result) {
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
                        'success': function(result) {
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
                        'success': function(result) {
                            location.reload();
                        }
                    });
                }
            </script>
                                <script>
    // Auto-dismiss flash messages after 5 seconds
    setTimeout(function() {
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(function(msg) {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500); // remove from DOM after fade
        });
    }, 5000); // 5 seconds
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