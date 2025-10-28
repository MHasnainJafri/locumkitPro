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
@if(session('success'))
    <div class="alert alert-success flash-message">{{ session('success') }}</div>
@endif
<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" id="resourceSearch" class="form-control" placeholder="Search Privileges...">
    </div>
</div>

                <table class="table clickable table-striped table-hover">
                    <colgroup>
                        <col width="30%">
                        <col width="40%">
                        <col width="20%">
                        <col width="5%">
                        <col width="5%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Privileges</th>
                            @cando('packageResource/edit')
                            <th class="text-center">Edit</th>
                            @endcando
                            @cando('packageResource/delete')
                            <th class="text-center">Delete</th>
                            @endcando
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $pkg)
                            <tr>
                                <td>{{ $pkg->id }}</td>
                                <td> {{ $pkg->resource_key }} </td>
                                @cando('packageResource/edit')
                                <td class="text-center">
                                    <a href="{{ route('admin.pkgresource.edit', $pkg) }}" class="edit-line">
                                        <img src="/backend/images/icones/edit.png" alt="Edit">
                                    </a>
                                </td>
                                @endcando

                                @cando('packageResource/delete')
                                <td class="text-center">

                                    <form id="delete_forms_{{$pkg->id}}" action="{{ route('admin.pkgresource.destroy', $pkg->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <!--<button type="submit" class="delete-line">-->
                                        <!--    <img src="/backend/images/icones/delete.png" alt="Delete">-->
                                        <!--</button>-->
                                        <button type="button" class="delete-line" data-toggle="modal" data-target="#exampleModalCenter_{{$pkg->id}}">
                                            <img src="/backend/images/icones/delete.png"
                                                alt="Delete">
                                        </button>
                                    </form>
                                </td>
                                @endcando
                            </tr>
                            <div class="modal fade" id="exampleModalCenter_{{$pkg->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Delete Package Resource</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    Are you sure to delete this Package Resource?
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" onClick="deleteRole({{$pkg->id}})" class="btn btn-danger">Confirm</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                        @endforeach


                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <script>
        function deleteRole(id) {
            $("#delete_forms_"+id).submit()
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('resourceSearch');
    const tableRows = document.querySelectorAll('table tbody tr');

    searchInput.addEventListener('keyup', function () {
        const query = this.value.toLowerCase();

        tableRows.forEach(row => {
            const privilege = row.querySelectorAll('td')[1]?.textContent.toLowerCase() || '';
            if (privilege.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

@endsection
