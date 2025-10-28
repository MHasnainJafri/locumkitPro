@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\FinanceController')
    <style>
        .main-container.container > .col-lg-12
        {
            padding-left: 260px !important;
        }
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

            <div class="page-content" style="margin-left: -10px">
                <div class="qus-tabs financead"
                    style="text-align: center;border: 1px solid #ccc;display: -webkit-box;margin-bottom: 10px;">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <h5 style="font-weight:bold;">Year: {{$year}} </h5>
                        </div>
                        <div class="col-md-4">
                            <h5 style="font-weight:bold;">User No. {{$id->id}} </h5>
                        </div>
                        <div class="col-md-4">
                            <h5 style="font-weight:bold;"> {{$id->login}} </h5>
                        </div>
                    </div>
                </div>
                <div id="tabs">
                    <div class="qus-tabs">
                        <ul>
                            <li id="li_income" class="active"><a id="income-tab"
                                    href="#">Income
                                    Transactions</a>
                            </li>
                            <li id="li_expense" class=""><a id="expense-tab"
                                    href="#">Expense
                                    Transactions</a>
                            </li>
                            <button type="button" class="btn btn-info pull-right"
                                onclick="fnExcelReport('Transactions 2023-2024 Umar Khan 28-10-23');">Export To xls
                            </button>
                        </ul>
                    </div>
                    <div id="income-content" class="d-block">
                        <div  id="fre-tab" style="height: 700px; overflow: auto;">
                            <div id="table_wrapper_wrapper" class="dataTables_wrapper no-footer">
                                <!--<div id="table_wrapper_filter" class="dataTables_filter pull-right"><label><input type="search"-->
                                <!--            class="form-control" placeholder="Search For Export"-->
                                <!--            aria-controls="table_wrapper"></label></div>-->
                                <table class="table clickable table-striped table-hover table-responsive dataTable no-footer"
                                    id="table_wrapper" width="100%" role="grid" style="width: 100%;">
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_desc" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-sort="descending"
                                                aria-label="Tran&amp;nbsp;No: activate to sort column ascending"
                                                style="width: 62px;">Tran&nbsp;No</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Job&amp;nbsp;No: activate to sort column ascending"
                                                style="width: 52px;">Job&nbsp;No</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Date: activate to sort column ascending"
                                                style="width: 87px;">Date</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Net (): activate to sort column ascending"
                                                style="width: 53px;">Net ()</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Gross (): activate to sort column ascending"
                                                style="width: 59px;">Gross ()</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Store: activate to sort column ascending"
                                                style="width: 112px;">Store</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Location: activate to sort column ascending"
                                                style="width: 91px;">Location</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Category: activate to sort column ascending"
                                                style="width: 69px;">Category</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Supplier: activate to sort column ascending"
                                                style="width: 108px;">Supplier</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Bank: activate to sort column ascending"
                                                style="width: 40px;">Bank</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1"
                                                aria-label="Bank&amp;nbsp;Date: activate to sort column ascending"
                                                style="width: 87px;">Bank&nbsp;Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($income_tranactions as $key => $value)
                                            <tr role="row" class="odd">
                                                <td data-order=" {{$value->id}} " class="sorting_1"> {{$value->id}} </td>
                                                <td> {{$value->job_id ?? 'N/A'}} </td>
<td> {{ $value->job_date ? \Carbon\Carbon::parse($value->job_date)->format('d/m/y') : 'N/A' }} </td>
                                                <td> {{$value->job_rate ?? 'N/A'}} </td>
                                                <td> {{$value->job_rate ?? 'N/A'}} </td>
                                                <td> {{$value->store ?? 'N/A'}} </td>
                                                <td> {{$value->location ?? 'N/A'}} </td>
                                                <td>
                                                    {{ $value->income_type == '3' ? "Other" : "" }}
                                                    {{ $value->income_type == '2' ? "Bonus" : "" }}
                                                    {{ $value->income_type == '1' ? "Income" : "" }}
                                                </td>
                                                <td> {{$value->supplier ?? 'N/A'}} </td>
                                                <td>
                                                    {{$value->is_bank_transaction_completed == '0' ? "N" : ""}}
                                                </td>
                                                <td> {{$value->bank_transaction_date ?? 'N/A'}} </td>
                                            </tr>
                                        @endforeach
                                        <!-- <tr role="row" class="even">
                                            <td data-order="85" class="sorting_1"># 85</td>
                                            <td>150</td>
                                            <td>07/09/2023</td>
                                            <td>240.00</td>

                                            <td>300.00</td>
                                            <td>VE</td>
                                            <td>Hounslow</td>
                                            <td>
                                                Income </td>
                                            <td>Vision Express</td>
                                            <td>Y</td>
                                            <td>11/09/2023</td>
                                        </tr> -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="expense-content" class="d-none">
                        <div id="fre-tab" style="height: 700px; overflow: auto;">
                            <div id="table_wrapper_wrapper" class="dataTables_wrapper no-footer">
                                <!--<div id="table_wrapper_filter" class="dataTables_filter pull-right"><label><input type="search"-->
                                <!--            class="form-control" placeholder="Search For Export"-->
                                <!--            aria-controls="table_wrapper"></label></div>-->
                                <table class="table clickable table-striped table-hover table-responsive dataTable no-footer"
                                    id="table_wrapper" width="100%" role="grid" style="width: 100%;">
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_desc" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-sort="descending"
                                                aria-label="Tran&amp;nbsp;No: activate to sort column ascending"
                                                style="width: 62px;">Tran&nbsp;No</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Job&amp;nbsp;No: activate to sort column ascending"
                                                style="width: 52px;">Job&nbsp;No</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Date: activate to sort column ascending"
                                                style="width: 87px;">Date</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Net (): activate to sort column ascending"
                                                style="width: 53px;">Net ()</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Gross (): activate to sort column ascending"
                                                style="width: 59px;">Gross ()</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Store: activate to sort column ascending"
                                                style="width: 112px;">Store</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Location: activate to sort column ascending"
                                                style="width: 91px;">Location</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Category: activate to sort column ascending"
                                                style="width: 69px;">Category</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Supplier: activate to sort column ascending"
                                                style="width: 108px;">Supplier</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1" aria-label="Bank: activate to sort column ascending"
                                                style="width: 40px;">Bank</th>
                                            <th class="sorting" tabindex="0" aria-controls="table_wrapper" rowspan="1"
                                                colspan="1"
                                                aria-label="Bank&amp;nbsp;Date: activate to sort column ascending"
                                                style="width: 87px;">Bank&nbsp;Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($expense_transactions as $key => $value)
                                        <tr role="row" class="odd">
                                            <td data-order=" {{$value->id}} " class="sorting_1"> {{$value->id}} </td>
                                            <td> {{$value->job_id ?? 'N/A'}} </td>
                                            <td> {{ $value->job_date ? \Carbon\Carbon::parse($value->job_date)->format('d/m/y') : 'N/A' }} </td>

                                            <td> {{$value->job_rate ?? 'N/A'}} </td>
                                            <td> {{$value->job_rate ?? 'N/A'}} </td>
                                            <td> {{$value->store ?? 'N/A'}} </td>
                                            <td> {{$value->location ?? 'N/A'}} </td>
                                            <td>
                                            {{$value?->expense_type?->expense}}
                                                {{-- {{ $value->income_type == '3' ? "Other" : "" }}
                                                {{ $value->income_type == '2' ? "Bonus" : "" }}
                                                {{ $value->income_type == '1' ? "Income" : "" }} --}}
                                            </td>
                                            <td> {{$value->supplier ?? 'N/A'}} </td>
                                            <td>
                                                {{$value->is_bank_transaction_completed == '0' ? "N" : ""}}
                                            </td>
                                            <td> {{$value->bank_transaction_date ?? 'N/A'}} </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
                <script>
                        $(document).ready(function () {
                            $("#income-tab").on("click", function (e) {
                                e.preventDefault();

                                $("#income-content").removeClass("d-none").addClass("d-block");
                                $("#expense-content").removeClass("d-block").addClass("d-none");
                                $("#li_expense").removeClass("active");
                                $("#li_active").addClass("active");
                            });
                            $("#expense-tab").on("click", function (e) {
                                e.preventDefault();

                                $("#expense-content").removeClass("d-none").addClass("d-block");
                                $("#income-content").removeClass("d-block").addClass("d-none");
                                $("#li_income").removeClass("active");
                                $("#li_expense").addClass("active");
                            });
                        });
                        
                        function fnExcelReport(fileName) {
                            var activeTab = $("#li_income").hasClass("active") ? "income" : "expense";
                            var tableId = activeTab === "income" ? "income-content" : "expense-content";
                            var wb = XLSX.utils.table_to_book(document.querySelector(`#${tableId} table`), {
                                sheet: "Sheet 1"
                            });
                            XLSX.writeFile(wb, fileName + ".xlsx");
                        }
                    //     function fnExcelReport(filename = 'Profitandloss') {
                    //     var tab_text = "<table border='2px'>";
                    //     tab_text +=
                    //         "<tr><td bgcolor='#87AFC6' colspan='12'align='center'><h3>Year: 2023-2024 , User No. 75 , testing<h3></td></tr><tr>";

                    //     var textRange;
                    //     var j = 0;
                    //     tab = document.getElementById('table_wrapper'); // id of table

                    //     for (j = 0; j < tab.rows.length; j++) {
                    //         tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
                    //     }

                    //     tab_text = tab_text + "</table>";
                    //     tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
                    //     tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
                    //     tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

                    //     var ua = window.navigator.userAgent;
                    //     var msie = ua.indexOf("MSIE ");

                    //     if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
                    //     {
                    //         txtArea1.document.open("txt/html", "replace");
                    //         txtArea1.document.write(tab_text);
                    //         txtArea1.document.close();
                    //         txtArea1.focus();
                    //         link = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
                    //     } else {
                    //         //other browser not tested on IE 11
                    //         var isChrome = !!window.chrome && !!window.chrome.webstore;
                    //         if (isChrome == true) {
                    //             var link = document.createElement('a');
                    //             link.download = filename + ".xls";
                    //             link.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
                    //             link.click();
                    //         } else {
                    //             link = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
                    //         }
                    //         return (link);
                    //     }
                    // }
                </script>
                <script type="text/javascript"
                    src="{{asset('frontend/locumkit-template/js/jquery.dataTables.min.js')}}" charset="UTF-8">
                </script>
                <!--<script>-->
                <!--    $(document).ready(function() {-->
                <!--        $('#table_wrapper').DataTable({-->
                <!--            language: {-->
                <!--                search: ""-->
                <!--            },-->
                <!--            searching: true,-->
                <!--            paging: false,-->
                <!--            "bInfo": false,-->
                <!--            "order": [-->
                <!--                [0, "desc"]-->
                <!--            ]-->
                <!--        });-->
                <!--        $('#table_wrapper_filter input').addClass('form-control');-->
                <!--        $('#table_wrapper_filter input').attr("placeholder", "Search For Export");-->
                <!--        $('#table_wrapper_filter').addClass('pull-right');-->
                <!--    });-->
                <!--</script>-->
            </div>

        </div>
    </div>
@endsection
