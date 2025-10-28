

@extends('admin.layout.app')
@section('content')
@inject('controller','App\Http\Controllers\admin\FinanceController' )
        <div class="main-container container">
            @include('admin.layout.sidebar')
<div class="row">
    <div class="col-lg-2 col-md-2 col-2">
        <!-- Empty column on the left -->
    </div>
    <div class="col-lg-10 col-md-10 col-10">

    <h1>Supplier List</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Store Name</th>
                <th>Address</th>
                <th>Town</th>
                <th>Country</th>
                <th>Postcode</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->store_name }}</td>
                    <td>{{ $supplier->address }}</td>
                    <td>{{ $supplier->town }}</td>
                    <td>{{ $supplier->country }}</td>
                    <td>{{ $supplier->postcode }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->contact_no }}</td>
                    <td>{{ $supplier->created_by_user->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    </div></div>
</div>
@endsection
