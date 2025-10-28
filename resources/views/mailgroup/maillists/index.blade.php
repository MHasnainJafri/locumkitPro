@extends('mailgroup.layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Mail Lists</h1>
            <a href="#" data-toggle="modal" data-target="#addUserModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Add New Mail List</a>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-12">
                <!-- DataTales Example -->
                <div class="card mb-4 shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-bordered table" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mailists as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>
                                                <button class="editBtn btn btn-primary btn-circle btn-sm" data-user="{{ $user }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('email-grouping.mailists.delete', ['id' => $user->id]) }}" class="d-inline-block" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-circle btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <button title="Mails" class="editMailsBtn btn btn-info btn-circle btn-sm" data-user="{{ $user }}">
                                                    <i class="fas fa-inbox"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Content Row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->

    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New Mail List</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('email-grouping.mailists.save') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name*</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Mail List</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('email-grouping.mailists.update') }}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label for="name">Name*</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mailListMailsModal" tabindex="-1" role="dialog" aria-labelledby="mailListMailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mailListMailsModalLabel">Edit List Mails</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('email-grouping.mailists.update.mails') }}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label for="emails">Emails*</label>
                            <div>
                                <select class="form-control form-select" name="emails[]" id="emails">

                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-success" type="submit">Update Mails</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on("click", ".editBtn", function() {
            let user = $(this).data("user");
            let modal = $("#editUserModal");

            if (user) {
                modal.find("input[name=id]").val(user.id);
                modal.find("input[name=name]").val(user.name);

                modal.modal("show");
            }
        })
        $(document).on("click", ".editMailsBtn", function() {
            let user = $(this).data("user");
            let modal = $("#mailListMailsModal");
            if (user) {
                let data = user.mail_group_mails.map(m => ({
                    id: m.mail,
                    text: m.mail,
                    selected: true,
                }));
                console.log("User:", user, "Emails: ", data);
                modal.find("input[name=id]").val(user.id);
                if (modal.find("select#emails").data('select2')) {
                    modal.find("select#emails").select2('destroy');
                }
                modal.find("select#emails").empty();
                modal.find("select#emails").select2({
                    tags: true,
                    data: data,
                    width: "100%",
                    multiple: true,
                    placeholder: "Enter email and hit enter to add new values",
                    createTag: function(params) {
                        // Check if the entered term contains a valid email address
                        if (/^\S+@\S+\.\S+$/.test(params.term)) {
                            return {
                                id: params.term,
                                text: params.term
                            };
                        }
                        return null;
                    }
                });

                modal.modal("show");
            }
        })
    </script>
@endpush
