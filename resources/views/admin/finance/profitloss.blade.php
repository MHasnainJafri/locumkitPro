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
                <style>
                    td {
                        padding: 10px;
                        font-size: 14px;
                    }
                </style>
                <div class="page-content" style="margin-left: -10px; margin-top: -10px;">

                <input type="hidden" name="financial_year" id="financial_year" value=" {{ $data['financial_year'] ?? '0' }} ">
                <input type="hidden" name="first_limit" id="first_limit" value=" {{ $data['first_limit'] ?? '0' }} ">
                <input type="hidden" name="first_limit_rate" id="first_limit_rate" value=" {{ $data['first_limit_rate'] ?? '0' /100 }} ">
                <input type="hidden" name="second_limit" id="second_limit" value=" {{ $data['second_limit'] ?? '0' }} ">
                <input type="hidden" name="second_limit_rate" id="second_limit_rate" value=" {{ $data['second_limit_rate'] ?? '0' /100 }} ">
                <input type="hidden" name="third_limit" id="third_limit" value=" {{ $data['third_limit'] ?? '0' }} ">
                <input type="hidden" name="third_limit_rate" id="third_limit_rate" value=" {{ $data['third_limit_rate'] ?? '0' /100 }} ">
                <input type="hidden" name="final_limit" id="final_limit" value=" {{ $data['final_limit'] ?? '0' }} ">
                <input type="hidden" name="final_limit_rate" id="final_limit_rate" value=" {{ $data['final_limit_rate'] ?? '0' /100 }} ">
                <input type="hidden" name="user_id" id="user_id" value=" {{ $data['user_id'] ?? '0' }} ">
                <input type="hidden" name="c4_min_ammount_1" id="c4_min_ammount_1" value=" {{ $data['c4_min_ammount_1'] ?? '0' }} ">
                <input type="hidden" name="c4_min_ammount_tax_1" id="c4_min_ammount_tax_1" value=" {{ $data['c4_min_ammount_tax_1'] ?? '0' }} ">
                <input type="hidden" name="c4_min_ammount_2" id="c4_min_ammount_2" value=" {{ $data['c4_min_ammount_2'] ?? '0' }} ">
                <input type="hidden" name="c4_min_ammount_tax_2" id="c4_min_ammount_tax_2" value=" {{ $data['c4_min_ammount_tax_2'] ?? '0' }} ">
                <input type="hidden" name="c4_min_ammount_3" id="c4_min_ammount_3" value=" {{ $data['c4_min_ammount_3'] ?? '0' }} ">
                <input type="hidden" name="c4_min_ammount_tax_3" id="c4_min_ammount_tax_3" value=" {{ $data['c4_min_ammount_tax_3'] ?? '0' }} ">
                <input type="hidden" name="c2_min_amount" id="c2_min_amount" value=" {{ $data['c2_min_amount'] ?? '0' }} ">
                <input type="hidden" name="c2_tax" id="c2_tax" value=" {{ $data['c2_tax'] ?? '0' }} ">

  
                    <div class="col-md-1 col-sm-1"></div>
                    <div class="col-md-10 col-sm-10 finproloss">
                        <form id="profitlossform" method="post">
                            <table width="50%" border="1" cellspacing="0" cellpadding="0" id="table_wrapper"
                                align="center">
                                <tbody>
                                    <tr>
                                        <td colspan="2" align="center" class="col-md-12">
                                            <h3 class="mar0">PROFIT AND LOSS</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="pad0">
                                            <table width="100%" cellspacing="0" cellpadding="0" border="0"
                                                align="center">
                                                <tbody>
                                                    <tr>
                                                        <td class="col-md-4" align="center">Year: 2023-2024</td>
                                                        <td class="col-md-4" align="center">User No. {{ $data['user_id'] }} </td>
                                                        <td class="col-md-4" align="center"> {{ $data['login'] }} </td>
                                                        <!-- <td class="col-md-4" align="center">User No. 75</td> -->
                                                        <!-- <td class="col-md-4" align="center">testing</td> -->

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">Revenues</td>
                                        <!-- <td class="col-md-6">20425.00</td> -->
                                        <td class="col-md-6"> {{ $data['revenue'] ?? '' }} </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">Cost of Sales</td>
                                        <!-- <td class="col-md-6">717.05</td> -->
                                        <td class="col-md-6" id="cos"> {{ $data['cos'] ?? '' }} </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">Gross Profit</td>
                                        <!-- <td class="col-md-6">19,707.95</td> -->
                                        <td class="col-md-6">{{ $data['gross_profit'] ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">GP %</td>
                                        <!-- <td class="col-md-6">0.96%</td> -->
                                        <td class="col-md-6">{{ $data['GP'] ?? '' }}%</td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">Administrative expenses</td>
                                        <!-- <td class="col-md-6">717.05</td> -->
                                        <td class="col-md-6" id="adm_exp">{{ $data['ad_exp'] ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">Profit from operations</td>
                                        <!-- <td class="col-md-6">18,990.90</td> -->
                                        <td class="col-md-6" id="prof_frm_operations">{{ $data['prof_frm_oper'] ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">OP %</td>
                                        <!-- <td class="col-md-6">0.93%</td> -->
                                        <td class="col-md-6">{{ $data['op'] ?? '' }}%</td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">Interest income</td>
                                        <td class="col-md-6" id="inin">
                                            <div class="input-group">
                                                <span class="input-group-addon"></span>
                                                <input id="interestincome" name="interestincome" type="number"
                                                    value="0.00" placeholder="  Interest Income" class="form-control"
                                                    required="" step="0.01">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">Profit before taxs</td>
                                        <td class="col-md-6" id="pbt"> {{ $data['prof_frm_oper'] ?? '' }} </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">Income tax</td>
                                        <td class="col-md-6" id="setIncomeTax"></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6">Profit after tax</td>
                                        <!-- <td class="col-md-6" id="pat">21064.36</td> -->
                                        <td class="col-md-6" id="pat"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" bgcolor="#E7E7E7"><b><u>Tax to date calculation (Sole Trader)
                                                </u></b></td>
                                    </tr>
                                    <tr>
                                        <!-- <td>£20425.00</td> -->
                                        <td id="show_pbt"> £{{ $data['prof_frm_oper'] ?? '' }} </td> <!-- Show here the profit before tax value  -->
                                        <td>Tax Rate</td>
                                    </tr>
                                    @php
                                        $money = floatval($data['prof_frm_oper']);
                                    @endphp
                                    @if($money <= $data['first_limit'])
                                        <tr>
                                            <td>£{{$data['first_limit']}}</td>
                                            <td>£0.00 ({{$data['first_limit_rate']}}%)</td>
                                        </tr>
                                        <tr>
                                            <td><b>Total Normal Tax</b></td>
                                            <td>£0</td>
                                        </tr>
                                    @elseif($money > $data['first_limit'] && $money <= $data['second_limit'])
                                        <tr>
                                            <td>£{{$data['first_limit']}}</td>
                                            <td>£0.00 ({{$data['first_limit_rate']}}%)</td>
                                        </tr>
                                        <tr>
                                            @php
                                                $firstLimit = (float) str_replace(',', '', $data['first_limit']);
                                                $secondLimitRate = (float) $data['second_limit_rate'];
                                                
                                                $first_num = $money - $firstLimit;
                                                $second_num = $first_num * ($secondLimitRate / 100);
                                            @endphp
                                            <td id='show_1'> £{{ $first_num }} </td>
                                            <td id='show_2'> £{{ $second_num }} ({{$secondLimitRate}}%) </td>
                                        </tr>
                                        <tr>
                                            <td><b>Total Normal Tax</b></td>
                                            <td>£{{ $second_num }}</td>
                                        </tr>
                                    @elseif($money > $data['second_limit'] && $money <= $data['third_limit'])
                                        @php
                                                $first_num = $money - $data['first_limit'];
                                                $second_num = $data['second_limit'] - $data['first_limit'];

                                                
                                                $third_num = ($data['second_limit'] - $data['first_limit']) * ($data['second_limit_rate'] / 100);
                                                $fourth_num = $money - $data['second_limit'];

                                                $fifth_num = $fourth_num * ($data['third_limit_rate'] / 100);
                                                $sixth_num = $fifth_num + $third_num;
                                        @endphp
                                        <tr>
                                            <td>£{{$data['first_limit']}}</td>
                                            <td>£0.00 ({{$data['first_limit_rate']}}%)</td>
                                        </tr>
                                        <tr>
                                            <td id='show_3'> £{{ $second_num }} </td>
                                            <td id='show_4'> £{{ $third_num }} ({{$data['second_limit_rate']}}%) </td>
                                        </tr>
                                        <tr>
                                            <td id="show_5"> £{{ $fourth_num }} </td>
                                            <td id="show_6"> £{{ $fifth_num }} ({{$data['third_limit_rate']}}%) </td>
                                        </tr>
                                        <tr>
                                            <td><b>Total Normal Tax</b></td>
                                            <td id="show_7">£{{ $sixth_num }}</td>
                                        </tr>
                                    @elseif($money > $data['final_limit'])
                                        @php
                                            $first_num = intval($money ?? 0) - intval($data['first_limit'] ?? 0);
                                            $second_num = intval($data['second_limit'] ?? 0) - intval($data['first_limit'] ?? 0);
                                            $third_num = (intval($data['second_limit'] ?? 0) - intval($data['first_limit'] ?? 0)) * (intval($data['second_limit_rate'] ?? 0) / 100);
                                            $fourth_num = $data['final_limit'] - $data['second_limit'];
                                            $fifth_num = $fourth_num * ($data['third_limit_rate'] / 100);
                                            $sixth_num = $fifth_num + $third_num;
                                            $seventh_num = $money - $data['final_limit'];
                                            $eight_num = $seventh_num * ($data['final_limit_rate'] / 100);
                                            $ninth_num = $eight_num + $fifth_num + $third_num;
                                        @endphp
                                        <!--please convert all these into the int-->
                                        <tr>
                                            <td>£{{ $data['first_limit'] }}</td>
                                            <td>£0.00 ( {{$data['first_limit_rate']}}%)</td>
                                        </tr>
                                        <tr>
                                            <td id="show_8">£{{$second_num}}  </td>
                                            <td id="show_9">£{{$third_num}} ({{$data['second_limit_rate']}}%)  </td>
                                        </tr>
                                        <tr>
                                            <td id="show_10">£{{$fourth_num}}  </td>
                                            <td id="show_11">£{{$fifth_num}} ({{$data['third_limit_rate']}}%) </td>
                                        </tr>
                                        <tr>
                                            <td id="show_12">£{{$seventh_num}}  </td>
                                            <td id="show_13">£{{$eight_num}} ({{$data['final_limit_rate']}}%) </td>
                                        </tr>
                                        <tr>
                                            <td><b>Total Normal Tax</b></td>
                                            <td id="show_14">£{{ $ninth_num }}</td>
                                        </tr>
                                
                                    @endif
                                    <!-- <tr>
                                        <td>£12570.00</td>
                                        <td> £0.00 ( 0% )</td>
                                    </tr>
                                    <tr>
                                        <td>£7,855.00</td>
                                        <td>£1,571.00 (20%)</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Normal Tax</b></td>
                                        <td id="setIncomeTax"></td>
                                    </tr> -->
                                    <tr>
                                        <td colspan="2" bgcolor="#e6f9ff"><b>NI Tax</b></td>
                                    </tr>
                                    <tr>
                                        <td>Class 4 NI Amount</td>
                                        <td>Tax Rate</td>
                                    </tr>
                                    @php
                                        if($money > $data['c2_min_amount']){
                                            $c2_tax = (intval($data['c2_tax'] ?? 0) * 52);
                                        }
                                        else{
                                            $c2_tax = 0;
                                        }
                                    @endphp

                                    
                                    @if($money <= $data['c4_min_ammount_1'] )
                                        @php
                                            $total_ni_tax = 0;
                                        @endphp
                                        <tr>
                                            <td id="c_show_1"> {{ floatval($data['c4_min_ammount_1']) }} </td>
                                            <td id="c_show_2">£0 ({{$data['c4_min_ammount_tax_1']}}%)</td>
                                        </tr>
                                        <tr>
                                            <td><b>Class 4 NI Total Tax</b></td>
                                            <td id="c_show_3"> £{{$total_ni_tax}} </td>
                                        </tr>
                                        <tr>
                                            <td><b>Class 2 NI Total Tax</b></td>
                                            <td> £{{$c2_tax}} </td>
                                        </tr>
                                        <tr>
                                            <td><b>Total Income Tax</b></td>
                                            <td> £{{$c2_tax + $total_ni_tax}} </td>
                                            <input type="hidden" id="ni_tax_income" value="{{$c2_tax + $total_ni_tax}}">
                                        </tr>
                                    @elseif($money > $data['c4_min_ammount_1'] && $money <= $data['c4_min_ammount_2'])
                                        @php
                                            $first_val = $money - $data['c4_min_ammount_1'];
                                            $first_rate = $first_val * ($data['c4_min_ammount_tax_2'] / 100);
                                            $total_ni_tax = $first_rate;
                                        @endphp
                                        <tr>
                                            <td>£{{$data['c4_min_ammount_1']}} </td>
                                            <td>£0 ({{$data['c4_min_ammount_tax_1']}}%)</td>
                                        </tr>
                                        <tr>
                                            <td id="c_show_4">£{{$first_val}} </td>
                                            <td id="c_show_5">£{{$first_rate}} ({{$data['c4_min_ammount_tax_2']}}%)</td>
                                        </tr>
                                        <tr>
                                            <td><b>Class 4 NI Total Tax</b></td>
                                            <td id="c_show_6"> £{{$total_ni_tax}} </td>
                                        </tr>
                                        <tr>
                                            <td><b>Class 2 NI Total Tax</b></td>
                                            <td> £{{$c2_tax}} </td>
                                        </tr>
                                        <tr>
                                            <td><b>Total Income Tax</b></td>
                                            <td id="c_show_7"> £{{$c2_tax + $total_ni_tax}} </td>
                                            <input type="hidden" id="ni_tax_income" value="{{$c2_tax + $total_ni_tax}}">
                                        </tr>
                                    @elseif($money > $data['c4_min_ammount_3'])
                                        @php
                                            $first_val = intval($data['c4_min_ammount_2'] ?? 0) - intval($data['c4_min_ammount_1'] ?? 0);
                                            $first_rate = $first_val * (intval($data['c4_min_ammount_tax_2'] ?? 0) / 100);

                                            $second_val = intval($money ?? 0) - intval($data['c4_min_ammount_3'] ?? 0);
                                            $second_rate = $second_val * (intval($data['c4_min_ammount_tax_3'] ?? 0) / 100);
                                            
                                            $total_ni_tax = $first_rate + $second_rate;
                                        @endphp
                                        <tr>
                                            <td>£{{$data['c4_min_ammount_1']}} </td>
                                            <td>£0 ({{$data['c4_min_ammount_tax_1']}}%)</td>
                                        </tr>
                                        <tr>
                                            <td>£{{$first_val}} </td>
                                            <td>£{{$first_rate}} ({{$data['c4_min_ammount_tax_2']}}%)</td>
                                        </tr>
                                        <tr>
                                            <td id="c_show_8">£{{$second_val}} </td>
                                            <td id="c_show_9">£{{$second_rate}} ({{$data['c4_min_ammount_tax_3']}}%)</td>
                                        </tr>
                                        <tr>
                                            <td><b>Class 4 NI Total Tax</b></td>
                                            <td id="c_show_10"> £{{$total_ni_tax}} </td>
                                        </tr>
                                        <tr>
                                            <td><b>Class 2 NI Total Tax</b></td>
                                            <td> £{{$c2_tax}} </td>
                                        </tr>
                                        <tr>
                                            <td><b>Total Income Tax</b></td>
                                            <td id="c_show_11"> £{{$c2_tax + $total_ni_tax}} </td>
                                            <input type="hidden" id="ni_tax_income" value="{{$c2_tax + $total_ni_tax}}">
                                        </tr>
                                        
                                    @endif
                                    
                                    <!-- <tr>
                                        <td>tet</td>
                                        <td>tet</td>
                                    </tr>

                                    <tr>
                                        <td>£7,855.00</td>
                                        <td> £706.95 ( 9.00% )</td>
                                    </tr>
                                    <tr>
                                        <td><b>Class 4 NI Total Tax</b></td>
                                        <td> £706.95</td>
                                    </tr>
                                    <tr>
                                        <td><b>Class 2 NI Total Tax</b></td>
                                        <td> £3.45</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Income Tax</b></td>
                                        <td>£2,281.40</td>
                                    </tr> -->
                                </tbody>
                            </table>
                            <div class="form-group">
                                <input type="hidden" value="20425.00" name="revenue">
                                <input type="hidden" value="2073.46" name="totaltax">
                                <input type="hidden" value="2023-2024" name="year">
                                <input type="hidden" value="75" name="fre_id">
                                <input type="hidden" value="717.05" name="cos">
                                <input type="hidden" value="717.05" name="othercost">
                                <input type="hidden"
                                    value="<tr><td>£20425.00</td><td>Tax Rate</td></tr><tr><td>£12570.00</td><td> £0.00    ( 0% )</td></tr><tr><td>£7,855.00</td><td>£1,571.00 (20%)</td></tr><tr><td><b>Total Normal Tax</b></td><td>£1,571.00</td></tr><tr><td colspan=2 bgcolor=#e6f9ff><b>NI Tax</b></td></tr><tr><td>Class 4 NI Amount</td><td>Tax Rate</td></tr><tr><td>£12570.00</td><td>£0 (0%)</td></tr><tr><td>£7,855.00</td><td> £706.95    ( 9.00% )</td></tr><tr><td><b>Class 4 NI Total Tax</b></td><td> £706.95</td></tr><tr><td><b>Class 2 NI Total Tax</b></td><td> £3.45</td></tr><tr><td><b>Total Income Tax</b></td><td>£2,281.40</td></tr>"
                                    name="taxcalculationhelp">
                                <div class="col-md-12 mart15 marb15 text-right pad0">
                                    <!-- <button type="submit" onclick=" return confirm('Are You Sure ?')"
                                        class="btn btn-info ">Update &amp; Continue</button> -->
                                    <button type="button" onclick="saveToDb()"
                                        class="btn btn-info ">Update &amp; Continue</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-1 col-sm-1"></div>
                </div>
                <script>
                    function saveToDb(){
                    
                    
                        var p_a_t = parseFloat(document.getElementById('pat').innerText);
                        var c_o_s = parseFloat(document.getElementById('cos').innerText);
                        var other_cost = parseFloat(document.getElementById('adm_exp').innerText);
                        var interest_income = parseFloat(document.getElementById('interestincome').value);
                        var taxcal = parseFloat(document.getElementById('setIncomeTax').innerText);
                        var financial_year = '2023-2024';
                        var start_month = '3';
                        var user_id = document.getElementById('user_id').value;
                        var ni_total_tax = parseFloat(document.getElementById('ni_tax_income').value);
                        
                        Ni_Tal = !isNaN(ni_total_tax) ? ni_total_tax : 0;
                        p_a_t = !isNaN(p_a_t) ? p_a_t : 0;
                        c_o_s = !isNaN(c_o_s) ? c_o_s : 0;
                        other_cost = !isNaN(other_cost) ? other_cost : 0;
                        interest_income = !isNaN(interest_income) ? interest_income : 0;
                        taxcal = !isNaN(taxcal) ? taxcal : 0;

                        var data = {
                            'cos': c_o_s,
                            'pat': p_a_t, 
                            'other_cost': other_cost,
                            'interest_income': interest_income,
                            'tax_cal': taxcal,
                            'financial_year': financial_year,
                            'start_month': start_month,
                            'user_id': user_id,
                            'ni_tax': Ni_Tal,
                        };

                        $.ajax({
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('save.profitloss.data') }}",
                            data: data,
                            success: function(response) {
                                response.status == 200;
                                window.location.href = "{{ route('finance.record') }}?success=1";
                            },
                            error: function(error) {
                                console.error('Error:', error);
                            }
                        });
                    }
                </script>
                <script>

                    
                    
                    $(document).ready(function() {
                        
                        // avoid negative value for income interest
                        document.getElementById('interestincome').addEventListener('input', function(e) {
                            if (this.value < 0) {
                                this.value = 0; // Reset to 0 if a negative value is entered
                            }
                        });

                        var curr = '';
                        gaeval1();
                        $("#interestincome").keyup(function() {
                            gaeval1();
                        });

                        function gaeval1() {
                            var tax = document.getElementById('pbt').innerHTML;
                            // var tax = 840;

                            var c4_min_ammount_1 = parseFloat(document.getElementById('c4_min_ammount_1').value);
                            var c4_min_ammount_tax_1 = parseFloat(document.getElementById('c4_min_ammount_tax_1').value / 100);

                            var c4_min_ammount_2 = parseFloat(document.getElementById('c4_min_ammount_2').value);
                            var c4_min_ammount_tax_2 = parseFloat(document.getElementById('c4_min_ammount_tax_2').value / 100);

                            var c4_min_ammount_3 = parseFloat(document.getElementById('c4_min_ammount_3').value);
                            var c4_min_ammount_tax_3 = parseFloat(document.getElementById('c4_min_ammount_tax_3').value / 100);

                            var c2_min_amount = parseFloat(document.getElementById('c2_min_amount').value);
                            var c2_tax = parseFloat(document.getElementById('c2_tax').value);

                            if(tax <= c2_min_amount){
                                var class_2_tax = 0
                            }
                            else{
                                var class_2_tax = c2_tax * 52;
                            }

                            var ni_tax = 0;
                            
                            if(tax > 0){
                                if(tax > c4_min_ammount_3){
                                    tax_1 = c4_min_ammount_2 - c4_min_ammount_1;
                                    tax_1_rate = tax_1 * c4_min_ammount_tax_2;

                                    tax_2 = tax - c4_min_ammount_3;
                                    tax_2_rate = tax_2 * c4_min_ammount_tax_3;

                                    var total_income = tax_1_rate + tax_2_rate;
                                    ni_tax = total_income

                                }
                                else if(tax <= c4_min_ammount_2 && tax >= c4_min_ammount_1){
                                    tax_1 = c4_min_ammount_2 - tax;
                                    tax_1_rate = tax_1 * c4_min_ammount_tax_2;
                                    var total_income = tax_1_rate
                                    ni_tax = total_income;
                                }
                                else if(tax < c4_min_ammount_1){
                                    ni_tax = 0;
                                }
                            }

                            var first_limit = parseFloat(document.getElementById('first_limit').value);
                            var first_limit_rate = document.getElementById('first_limit_rate').value;

                            var second_limit = parseFloat(document.getElementById('second_limit').value);
                            var second_limit_rate = document.getElementById('second_limit_rate').value;

                            var third_limit = parseFloat(document.getElementById('third_limit').value);
                            var third_limit_rate = document.getElementById('third_limit_rate').value;

                            var final_limit = parseFloat(document.getElementById('final_limit').value);
                            var final_limit_rate = document.getElementById('final_limit_rate').value;
                            

                            if(tax > 0){
                                var incomeTax = 0;
                                if (tax >= final_limit) {
                                    var first_stop = second_limit - first_limit;
                                    var first_rate = first_stop * second_limit_rate;
                                    
                                    var second_stop = third_limit - second_limit;
                                    var second_rate = second_stop * third_limit_rate;
                                    
                                    var third_stop = tax - final_limit;
                                    var third_rate = third_stop * final_limit_rate;

                                    var total_income = first_rate + second_rate + third_rate;
                                                                        
                                    incomeTax = total_income;
                                }
                                else if(tax > second_limit && tax < third_limit){
                                    var first_stop = second_limit - first_limit;
                                    var first_rate = first_stop * second_limit_rate;
                                
                                    var second_stop = tax - second_limit;
                                    var second_rate = second_stop * third_limit_rate;

                                    var total_income = first_rate + second_rate;
                                    incomeTax = total_income;
                                    
                                }
                                else if(tax >first_limit && tax < second_limit){
                                    var first_stop = tax - first_limit;

                                    var first_rate = first_stop * second_limit_rate;
                                    incomeTax = first_rate;
                                }
                                else if(tax <= first_limit){
                                    incomeTax = tax;
                                }
                            }
                            var totaltax = incomeTax;
                            // var pfo = '18990.9';
                            var pfo = document.getElementById('prof_frm_operations').innerHTML;
                            var inin = $("#interestincome").val();
                            if (inin == '') {
                                $("#pbt,#pat").html('N/A');
                            } else {
                                var income = parseFloat(pfo) + parseFloat(inin);
                                var pat = parseFloat(income) + parseFloat(totaltax);
                                $("#pbt").html(curr + parseFloat(income).toFixed(2));
                                $("#pat").html(curr + parseFloat(pat).toFixed(2));
                                $("#setIncomeTax").html(curr + parseFloat(incomeTax).toFixed(2));
                                var pbtValue = $("#pbt").text();

                                $("#c_show_1").html("£"+parseFloat(tax_1).toFixed(2));
                                $("#c_show_2").html("£"+parseFloat(tax_1_rate).toFixed(2)+" ("+(parseFloat(c4_min_ammount_tax_2).toFixed(2))*100+"%"+")");
                                
                                $("#c_show_3").html("£"+parseFloat(ni_tax).toFixed(2));
                                
                                $("#c_show_4").html("£"+parseFloat(tax_1).toFixed(2));
                                $("#c_show_5").html("£"+parseFloat(tax_1_rate).toFixed(2)+" ("+(parseFloat(c4_min_ammount_tax_2).toFixed(2))*100+"%"+")");
                                
                                $("#c_show_6").html("£"+parseFloat(ni_tax).toFixed(2));
                                $("#c_show_7").html("£"+(parseFloat(ni_tax + class_2_tax).toFixed(2)));
                                
                                $("#c_show_8").html("£"+parseFloat(tax_2).toFixed(2));
                                $("#c_show_9").html("£"+parseFloat(tax_2_rate).toFixed(2)+" ("+(parseFloat(c4_min_ammount_tax_3).toFixed(2))*100+"%"+")");
                                
                                $("#c_show_10").html("£"+parseFloat(ni_tax).toFixed(2));
                                $("#c_show_11").html("£"+(parseFloat(ni_tax + class_2_tax).toFixed(2)));


                                
                                $("#show_1").html("£"+parseFloat(first_stop).toFixed(2));
                                $("#show_2").html("£"+parseFloat(first_rate).toFixed(2)+" ("+second_limit_rate*100+"%"+")");

                                $("#show_3").html("£"+parseFloat(first_stop).toFixed(2));
                                $("#show_4").html("£"+first_rate+" ("+second_limit_rate*100+"%"+")");

                                $("#show_5").html("£"+parseFloat(second_stop).toFixed(2));
                                $("#show_6").html("£"+parseFloat(second_rate).toFixed(2)+" ("+(parseFloat(third_limit_rate).toFixed(2))*100+"%"+")");
                                
                                $("#show_7").html("£"+parseFloat(incomeTax).toFixed(2));
                                
                                $("#show_8").html("£"+parseFloat(first_stop).toFixed(2));
                                $("#show_9").html("£"+first_rate+" ("+second_limit_rate*100+"%"+")");

                                $("#show_10").html("£"+parseFloat(second_stop).toFixed(2));
                                $("#show_11").html("£"+parseFloat(second_rate).toFixed(2)+" ("+(parseFloat(third_limit_rate).toFixed(2))*100+"%"+")");
                                
                                $("#show_12").html("£"+parseFloat(third_stop).toFixed(2));
                                $("#show_13").html("£"+parseFloat(third_rate).toFixed(2)+" ("+(parseFloat(final_limit_rate).toFixed(2))*100+"%"+")");
                                
                                $("#show_14").html("£"+parseFloat(incomeTax).toFixed(2));
                                $("#show_pbt").html(pbtValue);
                                

                            }
                        }

                        function aftersave() {
                            var inin = $("#interestincome").val();
                            $("#inin").html(curr + inin);
                            $("#interestincome").remove();
                        }
                    });


                    function fnExcelReport(filename = 'Profitandloss') {
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

                    /*.finproloss table:first-child {
                        border: 1px solid;
                        border-bottom:0;
                    }
                    .finproloss table:first-child tr:first-child{
                      border-bottom: 1px solid;
                    }
                    */
                </style>
            </div>

        </div>
    </div>
@endsection
