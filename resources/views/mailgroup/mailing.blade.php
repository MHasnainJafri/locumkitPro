@extends('mailgroup.layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Send Mails By List</h1>
            <a href="#" data-toggle="modal" data-target="#logoutModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-sign-out-alt fa-sm text-white-50"></i> Logout</a>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-12">
                <!-- Area Chart -->
                <div class="card mb-4 shadow">
                    <div class="card-header py-3">
                        <h6 class="font-weight-bold text-primary m-0">Send Mails</h6>
                    </div>
                    <div class="card-body">
                        <form class="user add-user" action="{{ route('email-grouping.mailing.send') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" minlength="5" required name="title" aria-describedby="emailHelp" placeholder="Enter message title...">
                            </div>
                            <div class="form-group">
                                <select class="custom-select" name="list" required>
                                    <option selected value="">Select List</option>
                                    @foreach ($maillists as $list)
                                        <option value="{{ $list->id }}"> {{ $list->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <textarea id="summernote" class="form-control" name="message" aria-describedby="emailHelp" placeholder="Enter Message..." required></textarea>
                            </div>

                            <button class="btn btn-primary btn-user btn-block">
                                Send
                            </button>
                            <hr>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Content Row -->
        </div>
        <!-- /.container-fluid -->
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/mailgroup/ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('message');
    </script>
@endpush
