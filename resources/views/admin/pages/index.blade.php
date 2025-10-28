@extends('admin.layout.app')
@section('content')
<div class="main-container container">

    <div class="main-container container">
        @include('admin.pages.sidebar')
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
                    <li class="active">


                        Content </li>
                </ul>
            </div>
            <div class="page-content">

                <div class="page-header">
                    <h1>Create your own Pages</h1>
                </div>

                <section>
                    <article>
                        <h2>Page</h2>
                        <p>You can create, edit, copy, cut and paste Page by right-clicking on Website or children Page.</p>
                    </article>


                </section>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            Gc.setOption('routes', $.parseJSON('\x7B\x22edit\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fedit\x5C\x2FitemId\x22,\x22new\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fcreate\x22,\x22delete\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fdelete\x5C\x2FitemId\x22,\x22copy\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fcopy\x5C\x2FitemId\x22,\x22cut\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fcut\x5C\x2FitemId\x22,\x22paste\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fpaste\x5C\x2FitemId\x22,\x22publish\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Fpublish\x5C\x2FitemId\x22,\x22unpublish\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Funpublish\x5C\x2FitemId\x22,\x22refresh\x22\x3A\x22\x5C\x2Fadmin\x5C\x2Fcontent\x5C\x2Fdocument\x5C\x2Frefresh\x2Dtreeview\x5C\x2FitemId\x22\x7D'));
            Gc.initDocumentMenu(0, '\x2Fadmin\x2Fcontent\x2Fdocument\x2Fsort');
        });
    </script>

    <script type="text/javascript">
        Gc.keepAlive('/admin/keep-alive');
    </script>

    <a class="btn-scroll-up btn btn-small btn-inverse" id="btn-scroll-up" href="#">
        <i class="glyphicon glyphicon-open"></i>
    </a>
</div>

@endsection
