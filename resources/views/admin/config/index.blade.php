@extends('admin.layout.app')
@section('content')
 
        <div class="main-container container">
            @include('admin.config.sidebar')

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
                            General </li>
                    </ul>
                </div>
                <div class="page-content">
                    <form method="post" class="relative form-horizontal"
                        action="{{route('admin.config.store')}}">
                        @csrf
                        <div id="accordion">
                            <h3>General</h3>
                            <div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="site_name">Site
                                        name</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="site_name" id="site_name" class="form-control"
                                            value="{{$coreConfigData->where('identifier','site_name')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-2" for="site_addr">Site Address</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="site_addr" id="site_addr" class="form-control"
                                            value="{{$coreConfigData->where('identifier','site_addr')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="site_mobile">Site
                                        Contact Number</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="site_mobile" id="site_mobile" class="form-control"
                                            value="{{$coreConfigData->where('identifier','site_mobile')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="site_email">Site
                                        Eamil</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="site_email" id="site_email" class="form-control"
                                            value="{{$coreConfigData->where('identifier','site_email')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="site_currency">Site
                                        Currency</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="site_currency" id="site_currency"
                                            class="form-control" value="{{$coreConfigData->where('identifier','site_currency')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="email_currency">Email
                                        Currency Symbol</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="email_currency" id="email_currency"
                                            class="form-control" value="{{$coreConfigData->where('identifier','email_currency')->first()->value??''}}">
                                    </div>
                                </div>
                            </div>
                            <h3>Payment Paypal Setting</h3>
                            <div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="payment_mode">Payment
                                        Mode</label>
                                    <div class="col-lg-10">
                                        <select name="payment_mode" id="payment_mode" class="form-control">
                                            <option value="sandbox">Sandbox</option>
                                            <option value="live" @if($coreConfigData->where('identifier','payment_mode')->first()->value??''=="live") selected="selected" @endif>Live</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2"
                                        for="payment_email">Email</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="payment_email" id="payment_email"
                                            class="form-control" value="{{$coreConfigData->where('identifier','payment_email')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2"
                                        for="payment_api_user_name">Api User Name</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="payment_api_user_name" id="payment_api_user_name"
                                            class="form-control" value="{{$coreConfigData->where('identifier','payment_api_user_name')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="payment_api_pass">API
                                        Password</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="payment_api_pass" id="payment_api_pass"
                                            class="form-control" value="{{$coreConfigData->where('identifier','payment_api_pass')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="payment_api_key">API
                                        Key</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="payment_api_key" id="payment_api_key"
                                            class="form-control" value="{{$coreConfigData->where('identifier','payment_api_key')->first()->value??''}}">
                                    </div>
                                </div>
                            </div>
                            <h3>Admin Mail Setting</h3>
                            <div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="mail_from">From
                                        E-mail</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="mail_from" id="mail_from" class="form-control"
                                            value="{{$coreConfigData->where('identifier','mail_from')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="mail_from_name">From
                                        name</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="mail_from_name" id="mail_from_name"
                                            class="form-control" value="{{$coreConfigData->where('identifier','mail_from_name')->first()->value??''}}">
                                    </div>
                                </div>
                            </div>
                            <h3>Social Media Setting</h3>
                            <div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="fb">Facebook
                                        Link</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="fb" id="fb" class="form-control"
                                            value="{{$coreConfigData->where('identifier','fb')->first()->value}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="gp">Google
                                        Link</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="gp" id="gp" class="form-control" value="{{$coreConfigData->where('identifier','gp')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="li">LinkedIn
                                        Link</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="li" id="li" class="form-control"
                                            value="{{$coreConfigData->where('identifier','li')->first()->value}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="tw">Twitter
                                        Link</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="tw" id="tw" class="form-control" value="{{$coreConfigData->where('identifier','tw')->first()->value??''}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="pi">Pinterest
                                        Link</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="pi" id="pi" class="form-control" value="{{$coreConfigData->where('identifier','pi')->first()->value??''}}">
                                    </div>
                                </div>
                            </div>
                            <h3>Locum Search Setting</h3>
                            <div>
                                <div class="form-group">
                                    <label class="required control-label col-lg-2" for="qusMatch">Question match
                                        % </label>
                                    <div class="col-lg-10">
                                        <input type="text" name="qusMatch" id="qusMatch" class="form-control"
                                            value="{{$coreConfigData->where('identifier','qusMatch')->first()->value??''}}" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="margin-top-30">
                            <input id="input-save" type="submit" class="btn btn-warning" value="Save"
                                name="submit">
                        </div>
                    </form>
                </div>
                    <script type="text/javascript">
                        $(function() {
                            Gc.saveCommand();
                            Gc.checkDataChanged();
                            $('#accordion').accordion({
                                heightStyle: "content",
                                collapsible: true
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
@endsection
