<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Locumkit Mail Groups">
    <meta name="author" content="Nouman Habib">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- include summernote css/js -->
    <link href="{{ asset('frontend/mailgroup/css/summernote.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/mailgroup/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('frontend/locumform/js/sweetalert.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('frontend/mailgroup/css/dataTables.bootstrap4.min.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


</head>

<body id="page-top">


    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        @if (Auth::guard('mail_group_web')->check())
            <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="z-index:2">

                <!-- Sidebar - Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('email-grouping.index') }}">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-laugh-wink"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3">Mailing</div>
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider my-0">

                <!-- Nav Item - Dashboard -->
                <li class="nav-item {{ Route::currentRouteName() == 'email-grouping.index' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('email-grouping.index') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span></a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading">
                    Mail
                </div>

                <li class="nav-item {{ Route::currentRouteName() == 'email-grouping.mailing' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('email-grouping.mailing') }}">
                        <i class="fas fa-fw fa-inbox"></i>
                        <span>Send Mails</span></a>
                </li>
                <!-- Nav Item - Pages Collapse Menu -->
                @if (auth()->guard('mail_group_web')->check() &&
                        Gate::forUser(auth()->guard('mail_group_web')->user())->allows('is_mail_group_admin'))
                    <li class="nav-item {{ Route::currentRouteName() == 'email-grouping.mailists.index' ? 'active' : '' }}">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                            <i class="fas fa-fw fa-cog"></i>
                            <span>Mail Lists</span>
                        </a>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                            <div class="collapse-inner rounded bg-white py-2">
                                <a class="collapse-item" href="{{ route('email-grouping.mailists.index') }}">Mail Lists</a>
                            </div>
                        </div>
                    </li>
                @endif

                <!-- Divider -->
                <hr class="sidebar-divider">
                @if (auth()->guard('mail_group_web')->check() &&
                        Gate::forUser(auth()->guard('mail_group_web')->user())->allows('is_mail_group_admin'))
                    <!-- Heading -->
                    <div class="sidebar-heading">
                        Users
                    </div>
                    <!-- Nav Item - Pages Collapse Menu -->
                    <li class="nav-item {{ Route::currentRouteName() == 'email-grouping.users.index' ? 'active' : '' }}">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                            <i class="fas fa-fw fa-folder"></i>
                            <span>User</span>
                        </a>
                        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                            <div class="collapse-inner rounded bg-white py-2">
                                <a class="collapse-item" href="{{ route('email-grouping.users.index') }}">Users</a>
                            </div>
                        </div>
                    </li>
                    <!-- Divider -->
                    <hr class="sidebar-divider d-none d-md-block">
                @endif

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="d-none d-md-inline text-center">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>

            </ul>
        @endif

        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                @if (Auth::guard('mail_group_web')->check())
                    @include('mailgroup.layouts.header')
                @endif

                @yield('content')

                @include('mailgroup.layouts.footer')

            </div>

            <a class="scroll-to-top rounded" href="#page-top">
                <i class="fas fa-angle-up"></i>
            </a>

            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            <a class="btn btn-primary" href="{{ route('email-grouping.logout') }}">Logout</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('frontend/mailgroup/js/jquery.min.js') }} "></script>
    <script src="{{ asset('frontend/mailgroup/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/mailgroup/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('frontend/mailgroup/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('frontend/mailgroup/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('frontend/mailgroup/js/sb-admin-2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
    @if (session('error'))
        <script>
            swal('Error', `{{ session('error') }}`, 'error');
        </script>
    @endif
    @if (session('success'))
        <script>
            swal('Success', `{{ session('success') }}`, 'success');
        </script>
    @endif

    @if ($errors->any())
        @php
            $errorUlInner = '';
            foreach ($errors->all() as $value) {
                $errorUlInner .= "->{$value}\n";
            }
            $errorHtmlAcl = "Something went wrong\n";
            $errorHtmlAcl .= "\n{$errorUlInner}\n";
        @endphp
        <script>
            swal('Data error', `{!! $errorHtmlAcl !!}`, 'error');
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')

</body>
