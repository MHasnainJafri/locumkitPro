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
                <form class="relative form-horizontal" action="{{ route('admin.roles.update', $role) }}" method="post"
                    enctype="application/x-www-form-urlencoded">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="required&#x20;control-label&#x20;col-lg-2" for="name">Name</label>
                        <div class="col-lg-10">
                            <input type="text" name="name" class="form-control" id="name"
                                value="{{ $role->name }}">
                                 @if ($errors->has('name'))
    <span class="text-danger">{{ $errors->first('name') }}</span>
@endif

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="optional&#x20;control-label&#x20;col-lg-2" for="description">Description</label>
                        <div class="col-lg-10">
                            <input type="text" name="description" class="form-control" id="description"
                                value="{{ $role->description }}">
                                 @if ($errors->has('description'))
    <span class="text-danger">{{ $errors->first('description') }}</span>
@endif

                        </div>
                    </div>
                    <div id="role-list">
                        <div class="settings">
                            <h2>Permissions</h2>
                        </div>

                        @foreach ($permissions as $resource => $resourcePermissions)
                            <div class="settings" style="width: 100%">
                                <div class="{{ $resource }}">
                                    <h3>{{ ucfirst($resource) }}</h3>
                                </div>
                                <div class="row">
                                    @foreach ($resourcePermissions as $permission)
                                        <div class="col-md-3">
                                            <label class="optional" for="permissions-{{ $permission[0] }}">
                                                {{ $permission[0] }}
                                            </label>
                                            <div>
                                                <input type="checkbox" class=""
                                                    value="{{ $permission[1] }}" id="permissions-{{ $permission[0] }}"
                                                    name="permissions[]" @if (in_array($permission[1], $rolePermissions)) checked @endif>

                                                <label for="permissions-{{ $permission[0] }}"></label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
 @if ($errors->has('permissions'))
    <span class="text-danger">{{ $errors->first('permissions') }}</span>
@endif

                    </div>

                    <br />

                    <!-- Add more sections for other permissions here -->
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                    <!--<input id="input-save-new" type="submit" class="btn btn-warning" value="Save &amp; add new" name="addNew">-->
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-danger">Cancel</a>

                </form>

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
