@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="{{route('freelancer.finance')}}">Finance</a></li>
                        <li><a href="#">Manage Supplier</a></li>
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
                        <h3>Supplier list</h3>
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
                            <div class="text-capitalize finance-page-head text-center">Supplier List</div>
                        </div>
                        <div class="col-md-12 cash_table" style="margin-top: -25px;">
                            <a class="read-common-btn grad_btn btn-sm pull-right supplier-btn" href="/freelancer/add-supplier">ADD SUPPLIER</a>
                            <a class="read-common-btn grad_btn btn-sm pull-right supplier-btn" href="/freelancer/income-by-supplier">SUPPLIER REPORT</a>
                        </div>
                        <div class="col-md-12 cash_table">

                            <div class="table-responsive">
                                <table class="table-striped income_sum_table table">
                                    <thead>
                                        <tr>
                                            <th class="col-md-1" style="width:15% !important;" >Contact name</th>
                                            <th class="col-md-1" style="width:15% !important;" >Store name</th>
                                            <th class="col-md-1" style="width:15% !important;" >Address</th>
                                            <th class="col-md-1" style="width:15% !important;" >Contact No</th>
                                            <th class="col-md-1" style="width:15% !important;" >Email address</th>
                                            <th class="col-md-1" style="width:15% !important;" >Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($suppliers as $supplier)
                                            <tr>
                                                <td class="col-md-1" style="width:15% !important;" > {{ $supplier->name }} </td>
                                                <td class="col-md-1" style="width:15% !important;" > {{ $supplier->store_name }} </td>
                                                <td class="col-md-1" style="width:15% !important;" >
                                                    <address>
                                                        {{ $supplier->address }} <br />
                                                        @if ($supplier->second_address && $supplier->second_address != '')
                                                            {{ $supplier->second_address }}
                                                        @endif
                                                        {{ $supplier->town }}, {{ $supplier->country }}, {{ $supplier->postcode }}
                                                    </address>
                                                </td>
                                                <td class="col-md-1" style="width:15% !important;" > {{ $supplier->contact_no }} </td>
                                                <td class="col-md-1" style="width:15% !important;" > {{ $supplier->email }} </td>

                                                <td class="col-md-1" style="width:15% !important;" >
                                                    <a class="btn btn-xs btn-info" href="/freelancer/edit-supplier/{{ $supplier->id }}"> <i class="fa fa-fw fa-edit"></i> </a>
                                                    <button type="button" class="btn btn-xs btn-danger" onclick="confirm_delete_in('{{ $supplier->id }}')"><i class="fa fa-fw fa-close"></i></button>

                                                    <form action="/freelancer/delete-supplier/{{ $supplier->id }}" method="post" id="supplier-form-{{ $supplier->id }}" style="display: none;" hidden aria-hidden="true">
                                                        @csrf
                                                        @method('delete')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirm_delete_in(sup_id) {
            $('div#alert-confirm-modal #alert-message').html('Do you really want to delete this supplier?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $('#supplier-form-' + sup_id).submit();
                messageBoxClose();
            });
        };
    </script>
@endpush
