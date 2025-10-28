@extends('admin.layout.app')
@section('content')
@inject('controller', 'App\Http\Controllers\admin\IndustryNewsController')
<div class="main-container container">
    @include('admin.pages.sidebar')

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

            <div style=" display: flow-root;">
                <div class="form-group pull-right">
                    <div class="input-group pull-right">
                        <a class="btn btn-warning pull-right" href="{{ route('IndustryNews.Create') }}">Add Industry News</a>
                    </div>
                </div>
            </div>

            <div id="tabs">
                <div class="qus-tabs">

                </div>
                <div id="fre-tab">
                    <div class="cat-tabs">

                    </div>
                    <table class="table clickable table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Sno.</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($values as $key => $value)
                                <tr>
                                    <td>{{$value -> id ?? ''}}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $value->image_path) }}" height="50px" width="50px" alt="img">
                                    </td>
                                    <td> {{$value -> title ?? ''}} </td>
                                    <td> {{$value -> status == 1 ? 'Active':''}}{{$value -> status == 0 ? 'Disabled':''}} </td>
                                    <td>
                                        <a href="edit/{{$value->id}}">
                                            <img src="/backend/images/icones/edit.png" alt="img">
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
