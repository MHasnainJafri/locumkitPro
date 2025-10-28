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
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
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
                                <th>Status</th>
                                @cando('category/edit')
                                <th class="text-center">Edit</th>
                                <th class="text-center">Delete</th>
                                @endcando
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($categories as $category)
                                <tr>
                                <td>{{$category->name}}</td>
                                <td>{{$category->description}}</td>
                                <td>
                                    <span class="status-toggle">
                                        <a href="{{ route('admin.category.toggleStatus', $category->id) }}"
                                            class="btn {{ $category->is_active == 1 ? 'btn-success' : 'btn-danger' }}">
                                            {{ $category->is_active == 1 ? 'Active' : 'Inactive' }}
                                        </a>
                                    </span>
                                </td>
                                @cando('category/edit')
                                <td class="text-center">
                                    <a href="{{route('admin.category.edit' , $category->id)}}" class="edit-line">
                                        <img src="/backend/images/icones/edit.png"
                                            alt="Edit">
                                    </a>
                                </td>
                                @endcando
                                @cando('package/delete')
                                <td class="text-center">
                                    <form id="delete_forms_{{$category->id}}" action="{{ route('admin.category.destroy', $category->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <!--<button type="submit" class="delete-line">-->
                                        <!--    <img src="/backend/images/icones/delete.png" alt="Delete">-->
                                        <!--</button>-->
                                        <button type="button" class="delete-line" data-toggle="modal" data-target="#exampleModalCenter_{{$category->id}}">
                                            <img src="/backend/images/icones/delete.png"
                                                alt="Delete">
                                        </button>
                                    </form>
                                </td>
                                @endcando

                            </tr>
                            <div class="modal fade" id="exampleModalCenter_{{$category->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Delete Category</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    Are you sure to delete this Category?
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" onClick="deleteRole({{$category->id}})" class="btn btn-danger">Confirm</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                            @endforeach



                        </tbody>
                    </table>
                        <script>
                            function deleteRole(id) {
                                $("#delete_forms_"+id).submit()
                            }
                        </script>
                @endsection
