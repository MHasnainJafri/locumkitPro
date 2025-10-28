@extends('admin.layout.app')
@section('content')
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
                <form class="relative form-horizontal" action="{{route('admin.package.update',$package)}}" method="post"
                    enctype="application/x-www-form-urlencoded">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="required&#x20;control-label&#x20;col-lg-2" for="name">Package
                            Name</label>
                        <div class="col-lg-10">
                            <input type="text" name="name" class="form-control" id="name" value="{{$package->name}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="optional&#x20;control-label&#x20;col-lg-2" for="price">Price</label>
                        <div class="col-lg-10">
                            <input type="number" name="price" class="form-control" id="price" value="{{$package->price}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="optional&#x20;control-label&#x20;col-lg-2" for="description">Description</label>
                        <div class="col-lg-10">
                            <input type="text" name="description" class="form-control" id="description"
                                value="{{$package->description}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required&#x20;control-label&#x20;col-lg-2"
                            for="user_acl_package_resources_ids_list">Resources</label>
                        <div class="col-lg-10 checkbox-resource">
                            @foreach ($resources as $r)
                                <label class="required control-label col-lg-2">
                                    <input type="checkbox" name="user_acl_package_resources_ids_list[]" class="form-control"
                                        value="{{$r->id}}" {{ ($r->silver == 1 || $r->bronze == 1 || $r->gold == 1) ? 'checked' : '' }}>
                                    {{$r->label}}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                </form>
                <script type="text/javascript">
                    $(function() {
                        Gc.saveCommand();
                        Gc.checkDataChanged();
                        Gc.initRoles();
                    });
                </script>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        Gc.keepAlive('/admin/keep-alive');
    </script>
    <a class="btn-scroll-up btn btn-small btn-inverse" id="btn-scroll-up" href="#">
        <i class="glyphicon glyphicon-open"></i>
    </a>
    </body>

    </html>

    </html>
    </div>
@endsection
