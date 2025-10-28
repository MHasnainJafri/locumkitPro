
@extends('admin.layout.app')
@section('content')
    <div class="main-container container">

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

            @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <form method="GET" action="{{ route('payment.History') }}" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search name, email, type..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

            <table class="table clickable table-striped table-hover">
                <colgroup>
                    <col width="15%">
                    <col width="20%">
                    <col width="7%">
                    <col width="8%">
                    <col width="30%">
                    <col width="5%">
                    <col width="5%">
                </colgroup>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Information (Card/token)</th>
                        <th>Status</th>
                        @cando('paymentHistory/delete')
                        <th class="text-center">Delete</th>
                        @endcando
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentHistory as $history)
                    <tr>
                        <td>{{$history->user->firstname ?? ''}}</td>
                        <td><a href="/cdn-cgi/l/email-protection" class="_cf_email_"
                                data-cfemail="790b161b100a1617094e483911160d14181015571a1614">{{$history->user->email}}<a>
                        </td>

                        <td>{{$history->price}}</td>
                        <td>{{$history->payment_type}}</td>
                        <td>
                            @if(is_null($history->payment_token))
                                <span class="badge badge-warning">Not Assigned</span>
                            @else
                                <span class="badge badge-success">{{ $history->payment_token }}</span>
                            @endif
                        </td>

                        <td> <a href="/admin/config/user/history/updatepending/70">{{$history->payment_status == 1 ? 'Active' : 'Inactive' }}</a></td>

                        @cando('paymentHistory/delete')
                        <td class="text-center">
                            {{-- <a href="{{route('payment.Delete')}}" class="delete-line">
                                <img src="/backend/images/icones/delete.png"
                                    alt="Delete">
                            </a> --}}

                            <!--<form action="{{route('payment.Delete', $history->id)}}" method="POST">-->
                            <form id="delete_form_{{$history->id}}" action="{{route('payment.Delete', $history->id)}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <!--<button type="submit">-->
                                <!--    <img src="/backend/images/icones/delete.png" alt="Delete">-->
                                <!--</button>-->
                                <button type="button" class="delete-line" data-toggle="modal" data-target="#exampleModalCenter_{{$history->id}}">
                                    <img src="/backend/images/icones/delete.png"
                                        alt="Delete">
                                </button>

                            </form>
                        </td>
                        @endcando
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalCenter_{{$history->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Delete Payment Histroy</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            Are you sure to delete this Payment Histroy?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" onClick="deleteRole({{$history->id}})" class="btn btn-danger">Confirm</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endforeach

                </tbody>
            </table>


            <div class="pagination">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
                <p class="clearfix">
                    {{ $paymentHistory->appends(request()->query())->links() }}
                </p>
            </div>



            <script data-cfasync="false"
                src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
            <script type="text/javascript">
                Gc.initTableList();
            </script>
            <script>
                function deleteRole(id) {
                    $("#delete_form_"+id).submit()
                }
            </script>
        </div>
    </div>
</div>

@endsection
