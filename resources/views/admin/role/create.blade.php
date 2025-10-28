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
                    <li>
                        <i class="glyphicon glyphicon-home home-icon"></i>
                        <a href="/admin/dashboard">Dashboard</a>
                    </li>
                    <li>
                        <a href="/admin/config">Config</a>
                    </li>
                    <li>
                        <a href="/admin/users">User</a>
                    </li>
                    <li>
                        <a href="/admin/roles">Role</a>
                    </li>
                    <li class="active">
                        Create </li>
                </ul>
            </div>
            <div class="page-content">
                <form id="role-form" class="relative form-horizontal" action="{{ route('admin.roles.store') }}" method="post"
                    enctype="application/x-www-form-urlencoded">
                    @csrf
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="name">Name</label>
                        <div class="col-lg-10">
                            <input type="text" required name="name" class="form-control" id="name" value="{{old('name')}}"
                                   minlength="3" maxlength="50" placeholder="Enter your name">
                            <span class="text-danger" id="nameError"></span>
                        </div>
                        @if ($errors->has('name'))
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif

                    </div>
                    

                    <div class="form-group">
                        <label class="optional control-label col-lg-2" for="description">Description</label>
                        <div class="col-lg-10">
                            <input type="text" name="description" class="form-control" id="description" value="{{old('description')}}"
                                   maxlength="255" placeholder="Enter a description (optional)">
                            <span class="text-danger" id="descriptionError"></span>
                            @if ($errors->has('description'))
                                <span class="text-danger">{{ $errors->first('description') }}</span>
                            @endif
                        </div>
                    </div>
                    <div id="role-list">
                        <div class="settings">
                            <h2>Permissions</h2>
                        </div>
                         @if ($errors->has('permissions'))
                            <span class="text-danger">{{ $errors->first('permissions') }}</span>
                        @endif
                        @foreach ($permissions as $resource => $resourcePermissions)
                            <div class="settings" style="width: 100%">
                                <div class="{{ $resource }}">
                                    <h3>{{ ucfirst($resource) }}</h3>
                                </div>
                                <div class="row">
                                    @foreach ($resourcePermissions as $permission)
                                        @if($permission[1] == '58')
                                        
                                        @else
                                            <div class="col-md-3" style="display:flex;">
                                                <div class="mx-2">
                                                    <input type="checkbox" class=""
                                                        value="{{ $permission[1] }}" id="permissions-{{ $permission[0] }}"
                                                        name="permissions[]">
    
                                                    <label for="permissions-{{ $permission[0] }}"></label>
                                                </div>
                                                <label class="optional" for="permissions-{{ $permission[0] }}">
                                                    {{ $permission[0] }}
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        

                    </div>

                    <br />
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save &amp; add new" name="submit">
                </form>
                <script>
document.getElementById('role-form').addEventListener('submit', function (e) {
    const clickedButton = document.activeElement;

    if (clickedButton.type === 'submit') {
        // Let the form submit first, then disable
        setTimeout(() => {
            clickedButton.disabled = true;
            clickedButton.value = 'Saving...';
        }, 10); // 10 ms delay
    }
});



</script>




                <script type="text/javascript">
                    $(function() {
                        Gc.saveCommand();
                        Gc.checkDataChanged();
                        Gc.initRoles();
                    });
                </script>
                <style type="text/css">
                    .content,
                    .development,
                    .modules,
                    .stats {
                        display: none;
                    }
                </style>
            </div>
        </div>
    </div>
@endsection
