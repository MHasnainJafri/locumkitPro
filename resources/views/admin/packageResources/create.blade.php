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


                <form class="relative form-horizontal" action="{{ route('admin.pkgresource.store') }}" method="post"
                    enctype="application/x-www-form-urlencoded">
                    @csrf


                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="name">Privilege Key</label>
                        <div class="col-lg-10">
                            <input type="text" name="resource_key" class="form-control" id="name" value="{{old('resource_key')}}">
                            @error('resource_key')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="optional control-label col-lg-2" for="resource_value">Privilege</label>
                        <div class="col-lg-10">
                            <input type="text" name="resource_value" class="form-control" id="resource_value" value="{{old('resource_value')}}">
                            @error('resource_value')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="name">Privilege value</label>
                        <div class="col-lg-10">
                            <input type="number" name="resource_allow_count" class="form-control" id="name" min="0" 
           max="200"  value="{{old('resource_allow_count')}}">
                            @error('resource_allow_count')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save & add new" name="submit">
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
@endsection
