@extends('admin.layout.guest')


@section('content')

    <body class="one-page">
        <div class="container">
            <div class="main-content">
                <div class="row">
                    <div class="col-sm-10 col-lg-12">
                        <div class="login-container">
                            <div class="text-center">
                                <img src="/backend/images/logo.png" width="80" title="Locumkit" alt="locumkit"
                                    style="margin-top:10px;">
                                <h1>
                                    Locum Kit Admin </h1>
                            </div>
                            <div class="relative">
                                <div class="one-page-box visible widget-box no-border col-xs-12">
                                    <div class="widget-body">
                                        <div class="widget-main">
                                            <h4 class="header">
                                                Log In </h4>
                                            <form id="one-page-form" action="{{ route('login') }}" method="post">
                                                @csrf


                                                @if ($errors->any())
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif


                                                <fieldset class="has-warning">
                                                    <span class="block input-glyphicon input-glyphicon-right">
                                                        <input name="login" type="text" class="form-control"
                                                            placeholder="Login" autofocus required />
                                                        <i class="glyphicon glyphicon-user"></i>
                                                    </span>
                                                    <span class="block input-glyphicon input-glyphicon-right">
                                                        <input name="password" type="password" class="form-control"
                                                            placeholder="Password" required />
                                                        <i class="glyphicon glyphicon-lock"></i>
                                                    </span>
                                                    <div class="clearfix buttons">
                                                        <input name="redirect" id="redirect" type="hidden"
                                                            value="L2FkbWlu">
                                                        <button class="pull-right btn btn-small btn-warning">
                                                            <i class="glyphicon glyphicon-log-in"></i>
                                                            Log In </button>
                                                    </div>
                                                </fieldset>
                                            </form>
                                        </div>
                                        <div class="footer clearfix text-right">
                                            <a href="/admin/config/user/forgot-password">
                                                <i class="glyphicon glyphicon-exclamation-sign"></i>
                                                Forgot your password? </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

@endsection
