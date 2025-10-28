@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\FinanceController')
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

            <div class="page-content" style="margin-top: -10px;">
                <section class="add-new-record">
                    <div class="pull-right">
                        <a href="{{route('Blog.Create')}}" class="btn btn-warning"><i
                                class="glyphicon glyphicon-plus-sign"></i> Add Blogs</a>
                    </div>
                </section>
                <table class="table clickable table-striped table-hover" style="font-size: 13px;">
                        <colgroup>
                            <col width="5%">
                            <col width="15%">
                            <col width="35%">
                            <col width="15%">
                            <col width="15%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Id.</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($blogs as $key => $blog)
                                <tr>
                                    <td style="text-transform: capitalize;">{{ $blog->id }}</td>
                                    <td style="text-transform: capitalize;">
                                        <img src="{{ asset('storage/' . $blog->image_path) }}" height="50px" width="50px" alt="Blog Image">
                                    </td>
                                    <td style="text-transform: capitalize;">{{ $blog->title ?? ''}}</td>
                                    <td style="text-transform: capitalize;">{{ $blog->getBlogcategory -> name ?? '' }}</td>
                                    <td style="text-transform: capitalize;">{{ $blog->status == '1' ? 'Active' : 'Disable' }}</td>
                                    <td style="text-transform: capitalize;">
                                        <a href="edit/{{$blog->id}}">
                                            <img src="/backend/images/icones/edit.png" alt="img">
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
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
                        text-align: start;
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
@endsection
