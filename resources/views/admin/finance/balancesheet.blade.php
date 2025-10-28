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
                <div class="col-md-1 col-sm-1"></div>
                <div class="col-md-10 col-sm-10 finproloss">
                    <form id="profitlossform" method="post">
                        <table width="50%" border="1" cellspacing="0" cellpadding="0" id="table_wrapper"
                            align="center">
                            <tbody>
                                <tr>
                                    <td colspan="2" align="center" class="col-md-12">
                                        <h3 class="mar0" style="font-weight: 600;">BALANCE SHEET</h3>
                                    </td>
                                </tr>
                                <input type="hidden" id="financial_year" value=" {{$data['year']}} ">
                                <input type="hidden" id="user_id" value=" {{$data['user_id']}} ">
                                <tr>
                                    <td colspan="2" class="pad0">
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                            <tbody>
                                                <tr>
                                                    <!-- <td class="col-md-3" align="center">Year: 2023-2024</td> -->
                                                    <!-- <td class="col-md-3" align="center">User No. 75</td> -->
                                                    <!-- <td class="col-md-3" align="center">testing</td> -->
                                                    <td class="col-md-3" style="font-weight: 600;" align="center">Year: {{$data['year']}} </td>
                                                    <td class="col-md-3" style="font-weight: 600;" align="center">User No. {{$data['user_id']}} </td>
                                                    <td class="col-md-3" style="font-weight: 600;" align="center"> {{ $data['login'] }} </td>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" bgcolor="#E7E7E7">Non-current assets</td>
                                </tr>
                                <tr>
                                    <td class="col-md-4">Property, plant and equipment</td>
                                    <td class="col-md-4 put1">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input id="put1" name="put1" type="number" value="{{$balance_sheet->profit_plan_equip ?? '' }}"
                                                placeholder="Enter" class="form-control" required="">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" bgcolor="#E7E7E7">Current assets</td>
                                </tr>
                                <tr>
                                    <td>Trade and other receivables</td>
                                    <td class="put2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input id="put2" name="put2" type="number" value="{{$balance_sheet->trade_other ?? ''}}"
                                                placeholder="Enter" class="form-control" required="">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cash and cash equivalents</td>
                                    <td class="put3">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input id="put3" name="put3" type="number" value="{{$balance_sheet->cash_equp ?? ''}}"
                                                placeholder="Enter" class="form-control" required="">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td id="old_put2_put3" class="put2_put3">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input title="Trade and other receivables + Cash and cash equivalents"
                                                type="text" id="put2_put3" class="form-control" disabled="" value="{{$balance_sheet->total_cash_trade ?? ''}}"
                                                placeholder="N/A">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="Non-current assets + Current assets">Total Assets</td>
                                    <td id="put1_td_old" class="put1_td">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input title="Non-current assets + Current assets" type="text" id="put1_td" value="{{$balance_sheet->total_assets ?? ''}}"
                                                class="form-control" placeholder="N/A" disabled="">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" bgcolor="#E7E7E7">Non-current liabilities</td>
                                </tr>
                                <tr>
                                    <td>Current liabilities</td>
                                    <td class="put4">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input id="put4" name="put4" type="number" value="{{$balance_sheet->current_liability ?? ''}}"
                                                placeholder="Enter" class="form-control" required="">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Taxation</td>
                                    <td id="taxation"> {{ floatval($data['income_tax']) }} </td>
                                    <!-- <td>2073.46</td> -->
                                </tr>
                                <tr>
                                    <td title="Current liabilities + Taxation">Total</td>
                                    <td id="old_put4_html" class="put4_html">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input title="Current liabilities + Taxation" type="text" id="put4_html"
                                                class="form-control" placeholder="N/A" disabled="" >
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" bgcolor="#E7E7E7">Net Assets / Liabilities</td>
                                </tr>
                                <tr>
                                    <td title="Total assets - Non-current liabilities"></td>
                                    <td id="put1_td_put4_old" class="put1_td_put4">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span> <input
                                                title="Total assets - Non-current liabilities" type="text"
                                                id="put1_td_put4" class="form-control" placeholder="N/A" disabled="">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" bgcolor="#E7E7E7">Equity</td>
                                </tr>
                                <tr>
                                    <td>Equity</td>
                                    <td class="put6">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span><input id="put6" name="put6"
                                                type="number" value="{{$balance_sheet->equity ?? ''}}" placeholder="Enter" class="form-control" 
                                                required="">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Retained earnings </td>
                                    <td class="put7">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input id="put7" name="put7" type="number" value="{{$balance_sheet->retained_earning ?? ''}}"
                                                placeholder="Enter" class="form-control" required="">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <input type="hidden" value="2073.46" name="totaltax">
                            <input type="hidden" value="2023-2024" name="year">
                            <input type="hidden" value="75" name="fre_id">
                            <div class="col-md-12 mart15 marb15 text-right pad0">
                                <button type="button" onclick="saveToDb()"
                                    class="btn btn-info">Update &amp; Continue</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-1 col-sm-1"></div>
            </div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>
            <script>

                function saveToDb(){
                    var pro_pla = document.getElementById('put1').value;
                    var trade_other = document.getElementById('put2').value;
                    var cash_equp = document.getElementById('put3').value;
                    var total_cash_trade = document.getElementById('put2_put3').value;
                    var total_assets = document.getElementById('put1_td').value;
                    var current_liability = document.getElementById('put4').value;
                    var taxation = document.getElementById('taxation').innerText;
                    var total_tax_liab = document.getElementById('put4_html').value;
                    var net_assests_liab = document.getElementById('put1_td_put4').value;
                    var equity = document.getElementById('put6').value
                    var retained_earning = document.getElementById('put7').value
                    var financial_year = document.getElementById('financial_year').value
                    var user_id = document.getElementById('user_id').value

                    pro_pla = !isNaN(pro_pla) ? pro_pla : 0;
                    trade_other = !isNaN(trade_other) ? trade_other : 0;
                    cash_equp = !isNaN(cash_equp) ? cash_equp : 0;
                    total_cash_trade = !isNaN(total_cash_trade) ? total_cash_trade : 0;
                    total_assets = !isNaN(total_assets) ? total_assets : 0;
                    current_liability = !isNaN(current_liability) ? current_liability : 0;
                    taxation = !isNaN(taxation) ? taxation : 0;
                    total_tax_liab = !isNaN(total_tax_liab) ? total_tax_liab : 0;
                    net_assests_liab = !isNaN(net_assests_liab) ? net_assests_liab : 0;
                    equity = !isNaN(equity) ? equity : 0;
                    retained_earning = !isNaN(retained_earning) ? retained_earning : 0;

                    var data ={
                        'pro_plan':pro_pla,
                        'trade_other':trade_other,
                        'cash_equp':cash_equp,
                        'total_cash_trade':total_cash_trade,
                        'total_assets':total_assets,
                        'current_liability': current_liability,
                        'taxation':taxation,
                        'total_tax_liab':total_tax_liab,
                        'net_assests_liab':net_assests_liab,
                        'equity':equity,
                        'retained_earning':retained_earning,
                        'finance_year':financial_year,
                        'user_id':user_id,
                    }
                    $.ajax({
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('save.balancesheet.data') }}",
                        data: data,
                        success: function(response) {
                            response.status == 200;
                            window.location.href = "{{ route('finance.record') }}";
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });

                }
                
                var curr = '';

                calculation1();
                calculation2();

                $("#put2 , #put3 , #put1").keyup(function() {
                    calculation1();
                });

                function calculation1() {
                    // var totaltax = '2073.46';
                    var totaltax = document.getElementById('taxation').innerText;

                    var put2 = $("#put2").val();
                    var put3 = $("#put3").val();

                    if (put2 != '' && put3 != '') {
                        var put2put3 = parseFloat(put2) + parseFloat(put3);
                        $("#put2_put3").val(put2put3);
                    } else {
                        $("#put2_put3,#put1_td").val('N/A');
                    }
                    var put2_put3 = $("#put2_put3").val();
                    var put1 = $("#put1").val();

                    if (put1 != '' && put2 != '' && put3 != '') {
                        var put1_td = parseFloat(put2_put3) + parseFloat(put1);
                        $("#put1_td").val(put1_td);
                    } else {
                        $("#put1_td").val('N/A');
                    }
                }

                $("#put4").keyup(function() {
                    calculation2();
                });

                function calculation2() {
                    // var totaltax = '2073.46';
                    var totaltax = document.getElementById('taxation').innerText;
                    var put4 = $("#put4").val();
                    if (put4 != '') {
                        var put4_html = parseFloat(totaltax) + parseFloat(put4);
                        $("#put4_html").val(put4_html);
                        var put1_td = $("#put1_td").val();
                        var put1_td_put4 = parseFloat(put1_td) - parseFloat(put4_html);
                        $("#put1_td_put4").val(put1_td_put4);
                    } else {
                        $("#put4_html").val('N/A');
                        $("#put1_td_put4").val('N/A');
                    }

                }

                function aftersave() {

                    $(".put2_put3").html(curr + parseFloat($("#put2_put3").val()).toFixed(2));
                    $(".put1_td").html(curr + parseFloat($("#put1_td").val()).toFixed(2));
                    $(".put4_html").html(curr + parseFloat($("#put4_html").val()).toFixed(2));
                    $(".put1_td_put4").html(curr + parseFloat($("#put1_td_put4").val()).toFixed(2));

                    $(".put1").html(curr + parseFloat($("#put1").val()).toFixed(2));
                    $(".put2").html(curr + parseFloat($("#put2").val()).toFixed(2));
                    $(".put3").html(curr + parseFloat($("#put3").val()).toFixed(2));
                    $(".put4").html(curr + parseFloat($("#put4").val()).toFixed(2));
                    $(".put6").html(curr + parseFloat($("#put6").val()).toFixed(2));
                    $(".put7").html(curr + parseFloat($("#put7").val()).toFixed(2));
                    $("#put1,#put2,#put3,#put4,#put6,#put7").remove();
                    $("#btnExport,#cmd,#reset").show();
                    $("#freeze").hide();
                }

                function fnExcelReport(filename = 'BalanceSheet') {
                    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
                    var textRange;
                    var j = 0;
                    tab = document.getElementById('table_wrapper'); // id of table

                    for (j = 0; j < tab.rows.length; j++) {
                        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
                        //tab_text=tab_text+"</tr>";
                    }

                    tab_text = tab_text + "</table>";
                    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
                    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
                    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

                    var ua = window.navigator.userAgent;
                    var msie = ua.indexOf("MSIE ");

                    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
                    {
                        txtArea1.document.open("txt/html", "replace");
                        txtArea1.document.write(tab_text);
                        txtArea1.document.close();
                        txtArea1.focus();
                        link = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
                    } else {
                        //other browser not tested on IE 11
                        var isChrome = !!window.chrome && !!window.chrome.webstore;
                        if (isChrome == true) {
                            var link = document.createElement('a');
                            link.download = filename + ".xls";
                            link.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
                            link.click();
                        } else {
                            link = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
                            // sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
                        }
                        return (link);
                        // return (sa);

                    }

                }
            </script>
            <style>
                td {
                    padding: 10px;
                    font-size: 14px;
                }

                .mar0 {
                    margin: 0;
                }

                .pad0 {
                    padding: 0;
                }

                .mart15 {
                    margin-top: 15px;
                }

                .marb15 {
                    margin-bottom: 15px;
                }

                .finproloss table {
                    width: 100% !important;
                }

                .bglightgrey {
                    background: #E7E7E7;
                }
            </style>
        </div>
    </div>
@endsection
