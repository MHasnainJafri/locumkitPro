@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\UserController')

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
                    <li class="active">
                        User </li>
                </ul>
            </div>
            <div class="page-content">
                <div id="tabs">
                    <div class="qus-tabs">
                        <ul>

                            <li class="{{ $controller->role == 'Locum' ? 'active' : '' }}">
                                <a href="{{ route('admin.users.index', ['q' => 'Locum']) }}">Locum</a>
                            </li>
                            <li class="{{ $controller->role == 'Employer' ? 'active' : '' }}">
                                <a href="{{ route('admin.users.index', ['q' => 'Employer']) }}">Employer</a>
                            </li>
                            <li class="{{ $controller->role == 'Administrator' ? 'active' : '' }}">
                                <a href="{{ route('admin.users.index', ['q' => 'Administrator']) }}">Admin</a>
                            </li>

                        </ul>
                    </div>
                    <div id="fre-tab">
                        <div class="cat-tabs">
                            <form method="GET" action="{{ route('admin.users.index') }}">
                                <input type="hidden" name="q" value="{{ $controller->role }}">
                                <input type="hidden" name="c" value="{{ $controller->profession }}">
                            
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="username" class="form-control" placeholder="Search by Username" value="{{ request('username') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="email" name="email" class="form-control" placeholder="Search by Email" value="{{ request('email') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.users.index', ['q' => $controller->role, 'c' => $controller->profession]) }}" class="btn btn-secondary" style="border-color:#000;">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <!--<ul>-->
                            <!--    @foreach ($professions as $profession)-->
                            <!--        <li style="margin-top: 25px;" {{ $controller->profession == $profession->id ? ' class=active' : '' }}>-->
                            <!--            <a-->
                            <!--                href="{{ route('admin.users.index', ['q' => $controller->role, 'c' => $profession->id]) }}">{{ $profession->name }}</a>-->

                            <!--        </li>-->
                            <!--    @endforeach-->
                            <!--</ul>-->
                        </div>
                        <table class="table clickable table-striped table-hover"> 
                            <colgroup>
                                <col width="1%">
                                <col width="20%">
                                <col width="20%">
                                <col width="20%">
                                <col width="20%">
                                <col width="10%">
                                <col width="1%">
                                <col width="1%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th> <a href="#">Id</a> </th>
                                    <th><a href="javascript:void(0);" onclick="changeUserNameOrder(2);">User Name</a>
                                    </th>
                                    <th><a href="javascript:void(0);" onclick="changeUserFNameOrder(2);">Firstname</a>
                                    </th>
                                    <th><a href="javascript:void(0);" onclick="changeUserLNameOrder(2);">Lastname</a>
                                    </th>
                                    <th><a href="javascript:void(0);" onclick="changeUserEmailOrder(2);">Email</a>
                                    </th>
                                    <th> <a href="#">Is active</a> </th>
                                    @cando('user/edit')
                                    <th class="text-center"> <a href="#">Edit</a> </th>
                                    @endcando
                                    @cando('user/delete')
                                    <th class="text-center"> <a href="#">Delete</a> </th>
                                    @endcando
                                </tr>
                            </thead>
                            <tbody>
                                @if ($users->isNotEmpty())
                                    @foreach ($users as $user)
                                        <tr>
    
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->login }}</td>
                                            <td>{{ $user->firstname }}</td>
                                            <td>{{ $user->lastname }}</td>
                                            <td>
                                                {{ $user->email }}
                                            </td>
                                            <td>
    
    
                                                {{ $user->active == 1 ? 'Yes' : 'No' }}
                                            </td>
                                            @cando('user/edit')
                                            <td class="text-center">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="edit-line">
                                                    <img src="/backend/images/icones/edit.png" alt="Edit" />
                                                </a>
                                            </td>
                                            @endcando
                                            @cando('user/delete')
                                            <td class="text-center">
                                                <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="button" class="btn" onclick="confirmDelete({{ $user->id }})">
                                                        <img src="/backend/images/icones/delete.png" alt="Delete">
                                                    </button>
                                                </form>
                                            </td>
                                            @endcando
                                            
                                            <!-- Modal HTML -->
                                            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete this user? This action cannot be undone.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                            <button type="button" class="btn btn-danger" id="confirm-delete">Yes, Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <script>
                                                let deleteFormId = null;
                                            
                                                function confirmDelete(userId) {
                                                    deleteFormId = `delete-form-${userId}`;
                                                    $('#deleteModal').modal('show');
                                                }
                                            
                                                document.getElementById('confirm-delete').addEventListener('click', function () {
                                                    if (deleteFormId) {
                                                        document.getElementById(deleteFormId).submit();
                                                    }
                                                    $('#deleteModal').modal('hide');
                                                    // location.reload();
                                                });
                                            </script>
    
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center">No users found.</td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                        <div class="pagination">
                            <link rel="stylesheet"
                                href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
                            <p class="clearfix">
                                {{ $users->appends(request()->query())->links() }}
                            </p>
                        </div>


                    </div>
                    <script type="text/javascript">
                        Gc.initTableList();

                        function changeUserNameOrder(order) {
                            $.ajax({
                                'url': '/admin/config/user',
                                'type': 'POST',
                                'data': {
                                    'setUserNameOrder': order
                                },
                                'success': function(result) { //alert('question'+result);
                                    //alert("Order change");
                                    location.reload();
                                }
                            });
                        }

                        function changeUserFNameOrder(order) {
                            $.ajax({
                                'url': '/admin/config/user',
                                'type': 'POST',
                                'data': {
                                    'setUserFNameOrder': order
                                },
                                'success': function(result) { //alert('question'+result);
                                    //alert("Order change");
                                    location.reload();
                                }
                            });
                        }

                        function changeUserLNameOrder(order) {
                            $.ajax({
                                'url': '/admin/config/user',
                                'type': 'POST',
                                'data': {
                                    'setUserLNameOrder': order
                                },
                                'success': function(result) { //alert('question'+result);
                                    //alert("Order change");
                                    location.reload();
                                }
                            });
                        }

                        function changeUserEmailOrder(order) {
                            $.ajax({
                                'url': '/admin/config/user',
                                'type': 'POST',
                                'data': {
                                    'setUserEmailOrder': order
                                },
                                'success': function(result) { //alert('question'+result);
                                    //alert("Order change");
                                    location.reload();
                                }
                            });
                        }

                        if (window.location.search.indexOf('page') > -1 || window.location.search.indexOf('c') > -1 || window.location
                            .search.indexOf('q') > -1) {
                            //alert('track present');
                        } else {
                            var url = window.location.href;
                            window.location.href = url + "?page=1&q=Locum";
                        }
                    </script>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const filterForm = document.querySelector('form[action="{{ route('admin.users.index') }}"]');
                    
                        filterForm.addEventListener('submit', function (e) {
                            const username = filterForm.querySelector('input[name="username"]').value.trim();
                            const email = filterForm.querySelector('input[name="email"]').value.trim();
                            const status = filterForm.querySelector('select[name="status"]').value;
                    
                            if (!username && !email && !status) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Filter Required',
                                    text: 'Please apply at least one filter before submitting.',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        popup: 'swal2-border-radius'
                                    }
                                });
                            }
                        });
                    });
                    </script>

                </div>
            </div>
            @include('components.validation-notifications')
        </div>

    </div>
@endsection
