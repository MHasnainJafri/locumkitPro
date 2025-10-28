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
                <div class="qus-tabs financead"
                    style="text-align: center;border: 1px solid #ccc;display: -webkit-box;margin-bottom: 10px;">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <!-- <h5>Year: 2023</h5> -->
                            <h5 style="font-weight:bold;">Year: {{ $data['year'] }} </h5>
                        </div>
                        <div class="col-md-4">
                            <!-- <h5>User No. 75</h5> -->
                            <h5 style="font-weight:bold;"> User No. {{ $data['user_id'] }} </h5>
                        </div>
                        <div class="col-md-4">
                            <!-- <h5>testing</h5> -->
                            <h5 style="font-weight:bold;"> {{ $data['login'] }} </h5>
                        </div>
                    </div>
                </div>
                <div class="qus-tabs financead" style="display: flow-root;margin-bottom: 20px;">
                    <div>
                        <button type="button" class="btn btn-info pull-right"
                            onclick="fnExcelReport('table_wrapper');">Export To xls </button>
                    </div>
                </div>
                <div id="tabs">
                    <div id="fre-tab">
                        <div id="table_wrapper_wrapper" class="dataTables_wrapper no-footer">
                            <!--<div id="table_wrapper_filter" class="dataTables_filter pull-right"><label><input type="search"-->
                            <!--            class="form-control" placeholder="Search For Export"-->
                            <!--            aria-controls="table_wrapper"></label></div>-->
                            <table class="table clickable table-striped table-hover table-responsive dataTable no-footer"
                                id="table_wrapper" width="100%" role="grid" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th class="col-md-1 sorting_desc" tabindex="0" aria-controls="table_wrapper"
                                            rowspan="1" colspan="1" style="width: 131px;" aria-sort="descending"
                                            aria-label="Contact name: activate to sort column ascending">Contact name</th>
                                        <th class="col-md-1 sorting" tabindex="0" aria-controls="table_wrapper"
                                            rowspan="1" colspan="1" style="width: 131px;"
                                            aria-label="Store name: activate to sort column ascending">Store name</th>
                                        <th class="col-md-3 sorting" tabindex="0" aria-controls="table_wrapper"
                                            rowspan="1" colspan="1" style="width: 424px;"
                                            aria-label="Address: activate to sort column ascending">Address</th>
                                        <th class="col-md-1 sorting" tabindex="0" aria-controls="table_wrapper"
                                            rowspan="1" colspan="1" style="width: 131px;"
                                            aria-label="Contact No: activate to sort column ascending">Contact No</th>
                                        <th class="col-md-1 sorting" tabindex="0" aria-controls="table_wrapper"
                                            rowspan="1" colspan="1" style="width: 131px;"
                                            aria-label="Email address: activate to sort column ascending">Email address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suppliers as $key => $supplier)
                                        @if($supplier != null)
                                            <tr>
                                                <td> {{ $supplier->name }} </td>
                                                <td> {{ $supplier->store_name }} </td>
                                                <td> {{ $supplier->address }} </td>
                                                <td> {{ $supplier->contact_no }} </td>
                                                <td> {{ $supplier->email }} </td>
                                            </tr>
                                        @else
                                            <tr class="odd">
                                                <td valign="top" colspan="5" class="dataTables_empty">No data available in
                                                    table</td>
                                            </tr>                                            
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
               <!--<script>-->
               <!--         function fnExcelReport(filename = 'Supplier list') {-->
               <!--                 var tab_text = "<table border='2px'>";-->
                
               <!--                 tab_text +=-->
               <!--                         "<tr><td bgcolor='#87AFC6' colspan='5' align='center'><h3> User No. 75 , testing</h3></td></tr><tr>";-->
                
               <!--                 var j = 0;-->
                                <!--tab = document.getElementById('table_wrapper'); // id of table-->
                
                               
               <!--                 tab_text += '<tr>';-->
               <!--                 for (var i = 0; i < tab.rows[0].cells.length; i++) {-->
               <!--                         tab_text += '<td>' + tab.rows[0].cells[i].textContent + '</td>';-->
               <!--                 }-->
               <!--                 tab_text += '</tr>';-->
                
                                <!--// Add table data-->
                                <!--for (j = 1; j < tab.rows.length; j++) { // Start from 1 to skip header-->
               <!--                         tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";-->
               <!--                 }-->
                
               <!--                 tab_text = tab_text + "</table>";-->
                                <!--tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table-->
                                <!--tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table-->
                                <!--tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params-->
                
               <!--                 var ua = window.navigator.userAgent;-->
               <!--                 var msie = ua.indexOf("MSIE ");-->
                
                                <!--if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer-->
               <!--                 {-->
               <!--                         txtArea1.document.open("txt/html", "replace");-->
               <!--                         txtArea1.document.write(tab_text);-->
               <!--                         txtArea1.document.close();-->
               <!--                         txtArea1.focus();-->
               <!--                         link = txtArea1.document.execCommand("SaveAs", true, filename + ".xls");-->
               <!--                 } else {-->
                                        
               <!--                         var blob = new Blob([tab_text], { type: 'application/vnd.ms-excel' });-->
               <!--                         var link = document.createElement('a');-->
               <!--                         link.href = window.URL.createObjectURL(blob);-->
               <!--                         link.download = filename + ".xls";-->
               <!--                         link.click();-->
               <!--                 }-->
               <!--         }-->
               <!-- </script>-->
                <script type="text/javascript"
                    src="https://admin.locumkit.com/public/frontend/locumkit-template/js/jquery.dataTables.min.js" charset="UTF-8">
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
    <script>
        function fnExcelReport(tableId, filename = 'table_export.xlsx') {
            const table = document.getElementById(tableId);
            if (!table) {
                console.error('Table with ID "${tableId}" not found.');
                return;
            }
        
            const wb = XLSX.utils.book_new(); // Create a new workbook
            const ws = XLSX.utils.table_to_sheet(table); // Convert table to worksheet
        
            // Optional: Formatting (examples)
            const range = XLSX.utils.decode_range(ws['!ref']);
        
            // Example: Bold header row
            for (let col = range.s.c; col <= range.e.c; ++col) {
                ws[XLSX.utils.encode_cell({ r: range.s.r, c: col })].s = { font: { bold: true } };
            }
        
            // Example: Set column widths (adjust values as needed)
            ws['!cols'] = [
                { width: 20 }, // Column A width
                { width: 30 }, // Column B width
                { width: 40 }, // Column C width
                // ... add more column widths
            ];
        
            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1'); // Add worksheet to workbook
        
            XLSX.writeFile(wb, filename); // Download the Excel file
        }

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
@endsection
