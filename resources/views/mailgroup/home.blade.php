@extends('mailgroup.layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <a href="#" data-toggle="modal" data-target="#logoutModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-sign-out-alt fa-sm text-white-50"></i> Logout</a>
        </div>
        <!-- Content Row -->
        <div class="row">
            @if (auth()->guard('mail_group_web')->check() &&
                    Gate::forUser(auth()->guard('mail_group_web')->user())->allows('is_mail_group_admin'))
                <div class="col-lg-4 mb-4">
                    <a href="{{ route('email-grouping.users.index') }}">
                        <div class="card bg-primary text-white shadow">
                            <div class="card-body" style="padding:30px 20px; font-size:20px">
                                Users
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            <div class="col-lg-4 mb-4">
                <a href="{{ route('email-grouping.mailing') }}">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body" style="padding:30px 20px; font-size:20px">
                            Send Mails
                        </div>
                    </div>
                </a>
            </div>
            @if (auth()->guard('mail_group_web')->check() &&
                    Gate::forUser(auth()->guard('mail_group_web')->user())->allows('is_mail_group_admin'))
                <div class="col-lg-4 mb-4">
                    <a href="{{ route('email-grouping.mailists.index') }}">
                        <div class="card bg-info text-white shadow">
                            <div class="card-body" style="padding:30px 20px; font-size:20px">
                                Mail List
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            <!-- Content Row -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->
@endsection
