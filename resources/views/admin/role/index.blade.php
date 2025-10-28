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
                        <li class="active">
                            Role </li>
                    </ul>
                </div>
                <div class="page-content">
                    @if(session('error'))
    <div class="alert alert-danger flash-message">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success flash-message">{{ session('success') }}</div>
@endif


                    <table class="table clickable table-striped table-hover">
                        <colgroup>
                            <col width="30%">
                            <col width="60%">
                            <col width="5%">
                            <col width="5%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                @cando('role/edit')
                                <th class="text-center">Edit</th>
                                @endcando
                                @cando('role/delete')
                                <th class="text-center">Delete</th>
                                @endcando
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                <td>{{$role->name}}</td>
                                <td>{{ \Illuminate\Support\Str::limit($role->description, 30) }}</td>
                                @cando('role/edit')
                                <td class="text-center">
                                    <a href="{{route('admin.roles.edit',$role->id)}}">
                                        <img src="/backend/images/icones/edit.png"
                                            alt="Edit">
                                    </a>
                                </td>
                                @endcando
                                @cando('role/delete')
                                <td class="text-center">
                                    <form id="delete_form_{{$role->id}}" action="{{route('admin.roles.destroy',$role->id)}}" method="POST">
                                        @csrf
                                        @method('delete') 
                                        <!--<button type="submit" class="delete-line ">-->
                                        <!--</button>-->
                                        <!--    <img src="/backend/images/icones/delete.png"-->
                                        <!--        alt="Delete">-->
                                        <button type="button" class="delete-line" data-toggle="modal" data-target="#exampleModalCenter_{{$role->id}}">
                                            <img src="/backend/images/icones/delete.png"
                                                alt="Delete">
                                        </button>
                                        
                                        
                                    </form>
                                </td>
                                @endcando
                            </tr>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModalCenter_{{$role->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Delete Role</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    Are you sure to delete this Role?
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" onClick="deleteRole({{$role->id}})" class="btn btn-danger">Confirm</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                            @endforeach
                            <!-- Button trigger modal -->
                            

                        </tbody>
                    </table>
                    <script type="text/javascript">
                        Gc.initTableList();
                    </script>
                    <script>
                        function deleteRole(id) {
                            $("#delete_form_"+id).submit()
                        }
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

                </div>
            </div>
        </div>
@endsection
