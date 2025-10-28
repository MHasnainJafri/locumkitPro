@extends('admin.layout.app')
@section('content')
<style>
    .flash-message {
    transition: opacity 0.5s ease-in-out;
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
            <div class="page-content">
               @if(session('error'))
    <div class="alert alert-danger flash-message">{{ session('error') }}</div>
@endif

                <form class="relative form-horizontal" action="{{ route('admin.pkgresource.update', $resources) }}" method="post"
                enctype="application/x-www-form-urlencoded">
                
                @csrf 
                @method('PUT')
                <div class="form-group" >
                    <label class="required control-label col-lg-2" for="name">Privilege Key</label>
                    <div class="col-lg-10">
                        <input type="text" name="resource_key" class="form-control" id="name"
                            value="{{$resources->resource_key}}">
                            @error('resource_key')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="optional control-label col-lg-2" for="resource_value">Privilege</label>
                    <div class="col-lg-10">
                        <input type="text" name="resource_value" class="form-control" id="resource_value"
                            value="{{$resources->resource_value}}">
                            @error('resource_value')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="required control-label col-lg-2" for="name">Privilege value</label>
                    <div class="col-lg-10">
                        <input type="number" name="allow_count" value="{{$resources->allow_count}}" class="form-control" min="0" 
           max="200">
                        @error('resource_allow_count')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                <!--<input id="input-save" type="submit" class="btn btn-warning" value="Save & add new"
                    name="addNew">-->
                    <a href="{{ route('admin.pkgresource.index') }}" class="btn btn-danger">Cancel</a>
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
    <script>
    // Auto-dismiss flash messages after 5 seconds
    setTimeout(function() {
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(function(msg) {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500); // remove from DOM after fade
        });
    }, 5000); // 5 seconds
</script>
    <a class="btn-scroll-up btn btn-small btn-inverse" id="btn-scroll-up" href="#">
        <i class="glyphicon glyphicon-open"></i>
    </a>
    </body>

    </html>

    </html>
    </div>
@endsection
