@extends('layouts.user_profile_app')
@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="/freelancer/edit-profile"> View / Edit Profile</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>View / Edit Profile</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div id="primary-content" class="main-content register user-edit-wrapper">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <div class="col-md-12">
                        <div class="one-page-box widget-box no-border col-xs-12 visible">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header">
                                    </h4>
                                    <form id="one-page-form" action="/freelancer/update-profile" method="post" class="user_edit_form" enctype="multipart/form-data" autocomplete="off">
                                        @csrf

                                        <div class="col-md-4 col-sm-4 margin-bottom">
                                            <p style="text-align: center !important;">First Name</p>
                                        </div>
                                        <div style="text-align: center !important;" class="col-md-6 col-sm-8">
                                            <input 
                                                name="firstname" 
                                                type="text" 
                                                class="form-control margin-bottom" 
                                                value="{{ Auth::user()->firstname }}" 
                                                autofocus 
                                                required 
                                                pattern="^[a-zA-Z\s]+$" 
                                                title="Only alphabets are allowed"
                                                minlength="2" 
                                                maxlength="50" 
                                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" 
                                            />
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Last Name</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input 
                                                name="lastname" 
                                                type="text" 
                                                class="form-control margin-bottom" 
                                                value="{{ Auth::user()->lastname }}" 
                                                autofocus 
                                                required 
                                                pattern="^[a-zA-Z\s]+$" 
                                                title="Only alphabets are allowed"
                                                minlength="2" 
                                                maxlength="50" 
                                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" 
                                            />
                                        </div>



                                        <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Email</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input name="email" type="text" readonly class="form-control margin-bottom" value="{{ Auth::user()->email }}" autofocus required />
                                        </div>
                                        <div class="col-md-4 col-sm-4 margin-bottom">
                                            <p style="text-align: center !important;">Role</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input type="text" readonly class="form-control margin-bottom" value="{{ Auth::user()->role->name }}" autofocus required>
                                        </div>
                                        <div class="col-md-4 col-sm-4 margin-bottom">
                                            <p style="text-align: center !important;">Profession</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input type="text" readonly class="form-control margin-bottom" value="{{ Auth::user()->user_acl_profession->name }}" autofocus required id="profession_type">
                                        </div>
                                        <div class="col-md-4 col-sm-4 margin-bottom">
                                            <p style="text-align: center !important;">Package</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input type="text" readonly class="form-control margin-bottom" value="{{ Auth::user()->user_acl_package->name }} (£{{ Auth::user()->user_acl_package->price }})" autofocus required>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Login</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input name="login" type="text" readonly class="form-control margin-bottom" value="{{ Auth::user()->login }}" autofocus required />
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Company / Organization</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input name="company" type="text" class="form-control margin-bottom" value="{{ Auth::user()->user_extra_info->company ?? '' }}" />
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Address</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input name="address" type="text" class="form-control margin-bottom" value="{{ Auth::user()->user_extra_info->address ?? '' }}" id="address" />
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Town/City</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input name="city" type="text" class="form-control margin-bottom" value="{{ Auth::user()->user_extra_info->city ?? '' }}" id="city" />
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Post Code</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input name="zip" type="text" class="form-control margin-bottom" value="{{ Auth::user()->user_extra_info->zip ?? '' }}" id="zip" />
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Home Telephone</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input name="telephone" type="number" oninput="limitInputLength(this)" class="form-control margin-bottom" value="{{ Auth::user()->user_extra_info->telephone ?? '' }}" maxlength="11" id="telephone" />
                                            <div class="css_error" id="telephone_error"></div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Mobile</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8">
                                            <input name="mobile" type="number" class="form-control margin-bottom" 
                                                   value="{{ Auth::user()->user_extra_info->mobile ?? '' }}" 
                                                   maxlength="11" id="mobile" oninput="limitInputLength(this)" />
                                            
                                            <script>
                                            function limitInputLength(element) {
                                                if (element.value.length > 11) {
                                                    element.value = element.value.slice(0, 11);
                                                }
                                            }
                                            </script>
                                        <div class="css_error" id="mobile_error"></div>
                                        </div>
                                                                                <div class="col-md-4 col-sm-4">
                                            <p style="text-align: center !important;">Password</p>
                                        </div>
                                        <div class="col-md-6 col-sm-8 py-md-5">
                                            <p>If you want to change password, please <a href="{{ route('password.request') }}">click here</a>.</p>
                                        </div>


                                        <div class="col-sm-offset-4 col-md-6">
                                            <button class="save-btn-edit-prof btn-info btn btn-small">
                                                <i class="glyphicon glyphicon-edit"></i>
                                                Update </button>
                                        </div>
                                    </form>

                                    <div id="getlist-section" class="modal fade" role="dialog">
                                        <div class="list-popup">
                                            <div class="modal-dialog">

                                                <div class="modal-content">
                                                    <div class="modal-header no-border-bottom">

                                                        <h4 class="modal-title">Town's list</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h3 id="load_list" style="display:none"><img src="public/frontend/locumkit-template/img/loader.gif"> Please wait... </h3>
                                                        <form id="store_list_div" name="store_frm" action="/ajax-request">
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer no-border-top">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function() {
            var availableTags = @json($site_towns_available_tags);
            $("#city").autocomplete({
                source: availableTags
            });
        });

        $('#mobile').on('keyup', function() {
            check_mobile_number();
        });
        $('#telephone').on('keyup', function() {
            check_telephone_number();
        });

        $('button.save-btn-edit-prof').click(function() {
            if (($('#mobile').val() == "" || $('#mobile').val() == null) || !check_mobile_number()) {
                $('#mobile_error').html('Please enter valid mobile number');
                return false;
            }
            if (!check_locum_telephone()) {
                $('#telephone_error').html('Please enter valid telephone number');
                return false;
            };
        });

        function check_mobile_number() {
            var mobile = $('#mobile').val();
            var reg = /^[0-9]+$/;
            var user_type = $("#user_type").val();
            if (user_type == 2) { // for freelancer
                $('#mobile_note').html('This number is used to send you notifications of your jobs and finances.');
            }
            if ((mobile.length) < 11 && mobile.length != 0) {
                $('#mobile_error').html('Mobile number should be 11 digits.');
                $("#mobile").focus();
                //$(".full-process").hide();
                return false;
            } else if (!reg.test(mobile) && user_type == 2) {
                $('#mobile_error').html('Mobile number should be numbers only.');
                $("#mobile").focus();
                //$(".full-process").hide();
                return false;
            } else {
                $('#mobile_error').html('');
                return true;
            }
        }

        function check_telephone_number() {
            var telephone = $('#telephone').val();
            var reg = /^[0-9]+$/;
            if ((telephone.length) < script 5) {
                $('#telephone_error').html('Please enter correct telephone number');
                $("#telephone").focus();
                //$(".full-process").hide();
                return false;
            }
            if ((!reg.test(telephone)) && (telephone.length > 0)) {
                $('#telephone_error').html('Please enter telephone should be numbers only.');
                $("#telephone").focus();
                //$(".full-process").hide();
                return false;
            } else {
                $('#telephone_error').html('');
                return true;
            }
        }

        function check_locum_telephone() {
            if ($('#telephone').val().length > 1 && !check_telephone_number()) {
                $('#telephone_error').html('Please enter valid telephone number');
                return false;
            } else {
                return true;
            }
        }
    </script>
@endpush
