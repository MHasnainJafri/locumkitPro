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
                <section class="add-new-record">
                    <div class="pull-left">
                        <ul>
                            <li><b>C4 - Class 4 Ni</b></li>
                            <li><b>C2 - Class 2 Ni</b></li>
                        </ul>
                    </div>
                    <div class="pull-right">
                        <a href="{{ route('nitax.create') }}" class="btn btn-warning"><i
                                class="glyphicon glyphicon-plus-sign"></i> Add New</a>
                    </div>
                </section>
                <table class="table clickable table-striped table-hover">
                    <colgroup>
                        <col width="1%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Sr.</th>
                            <th>Finance Year</th>
                            <th>C4 Amount 1</th>
                            <th>C4 Amount 1 Tax (%)</th>
                            <th>C4 Amount 2</th>
                            <th>C4 Amount 2 Tax (%)</th>
                            <th>C4 Amount 3</th>
                            <th>C4 Amount 3 Tax (%)</th>
                            <th>C2 min Rate</th>
                            <th>C2 Year Tax Rate</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($niTaxSetting)
                            @foreach ($niTaxSetting as $key => $taxsetting)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $taxsetting->finance_year }}</td>
                                    <td>{{ $taxsetting->c4_min_ammount_1 }}</td>
                                    <td>{{ $taxsetting->c4_min_ammount_tax_1 }}</td>
                                    <td>{{ $taxsetting->c4_min_ammount_2 }}</td>
                                    <td>{{ $taxsetting->c4_min_ammount_tax_2 }}</td>
                                    <td>{{ $taxsetting->c4_min_ammount_3 }}</td>
                                    <td>{{ $taxsetting->c4_min_ammount_tax_3 }}</td>
                                    <td>{{ $taxsetting->c2_min_ammount }}</td>
                                    <td>{{ $taxsetting->c2_tax }}</td>

                                    <td class="text-center">
                                        <a href="{{ route('nitax.edit', $taxsetting->id) }}" class="edit-line">
                                            <!--<img src="https://locumkit.learnunstoppable.com/public/backend/images/icones/edit.png"-->
                                            <img src="{{asset('backend/images/icones/edit.png')}}"
                                                alt="Edit">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <!--<a href="{{ route('nitax.delete', $taxsetting->id) }}" class="delete-line">-->
                                        <!--    <img src="{{asset('backend/images/icones/delete.png')}}"-->
                                        <!--        alt="Delete">-->
                                        <!--</a>-->
                                        <form id="delete_form_{{$taxsetting->id}}" action="{{ route('nitax.delete', $taxsetting->id) }}" method="get">
                                            @csrf
                                            @method('DELETE')
    
                                            <button type="button" class="delete-line" data-toggle="modal" data-target="#exampleModalCenter_{{$taxsetting->id}}">
                                                <img src="/backend/images/icones/delete.png"
                                                    alt="Delete">
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                
                                <div class="modal fade" id="exampleModalCenter_{{$taxsetting->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Delete NiTax Record</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                        </button>
                                      </div>
                                      <div class="modal-body">
                                        Are you sure to delete this NiTax Record?
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" onClick="deleteRole({{$taxsetting->id}})" class="btn btn-danger">Confirm</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                
                            @endforeach
                        @endif

                    </tbody>
                </table>
                <div class="pagination">
                    <link rel="stylesheet"
                        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
                    <p class="clearfix">
                    </p>
                    <ul class="paginator-div">
                    </ul>
                    <p></p>
                </div>
                <script type="text/javascript">
                    Gc.initTableList();
                </script>
                <style type="text/css">
                    table tr th,
                    table tr td {
                        text-align: center;
                    }

                    section.add-new-record {
                        float: left;
                        width: 100%;
                        margin: 10px 0 0px;
                        border-bottom: 2px solid #ccc;
                        padding-bottom: 20px;
                    }
                </style>
            </div>

        </div>
    </div>
        <script>
    function deleteRole(id) {
            $("#delete_form_"+id).submit()
        }
    </script>

@endsection
