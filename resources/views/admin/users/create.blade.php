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
                        <li>
                            <i class="glyphicon glyphicon-home home-icon"></i>
                            <a href="/admin/dashboard">Dashboard</a>
                        </li>
                        <li>
                            <a href="/admin/config">Config</a>
                        </li>
                        <li>
                            <a href="/admin/config/user">User</a>
                        </li>
                        <li class="active">
                            Create </li>
                    </ul>
                </div>
                <div class="page-content">
                <form class="relative form-horizontal" id="userForm" action="{{ route('admin.users.store') }}" method="post" enctype="application/x-www-form-urlencoded">
                    @csrf
                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="email">Email</label>
                            <div class="col-lg-10">
                                <input type="text" name="email" class="form-control" id="email" value>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="login">Login</label>
                            <div class="col-lg-10">
                                <input type="text" name="login" class="form-control" id="login" value>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="password">Password</label>
                            <div class="col-lg-10">
                                <input type="password" name="password" class="form-control" autocomplete="off" id="password" value>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="password_confirmation">Password Confirm</label>
                            <div class="col-lg-10">
                                <input type="password" name="password_confirmation" class="form-control" autocomplete="off" id="password_confirmation" value>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="firstname">Firstname</label>
                            <div class="col-lg-10">
                                <input type="text" name="firstname" class="form-control" id="firstname" value>
                                <small class="text-danger" id="firstname-error"></small>
                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="lastname">Lastname</label>
                            <div class="col-lg-10">
                                <input type="text" name="lastname" class="form-control" id="lastname" value>
                                <small class="text-danger" id="lastname-error"></small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="active">User Status</label>
                            <div class="col-lg-10">
                                <select name="active" class="form-control" id="active">
                                    <option value="0">Disable</option>
                                    <option value="1">Active</option>
                                    <option value="2">Block</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="role_select">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="user_acl_role_id">Role</label>
                            <div class="col-lg-10">
                                <select name="user_acl_role_id" class="form-control">
                                    <option value="0">-- Select the Role --</option>

                                    @foreach (\App\Models\UserAclRole::where('is_public', 1)->get() as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="if_freelancer">
                            <div class="form-group" id="if_emp">
                                <label class="required&#x20;control-label&#x20;col-lg-2"
                                    for="user_acl_profession_id">Category</label>
                                <div class="col-lg-10">
                                    <select name="user_acl_profession_id" class="form-control">
                                        <option value="0">-- Select the category --</option>

                                        @foreach (\App\Models\UserAclProfession::where('is_active', 1)->get() as $profession)
                                         <option value="{{$profession->id}}">{{$profession->name}}</option>

                                        @endforeach
                                        
                                    </select>
                                </div>
                            </div>

                            <div class="form-group if_fre">
                                <label class="required&#x20;control-label&#x20;col-lg-2"
                                    for="user_acl_package_id">Package</label>
                                <div class="col-lg-10">
                                    <select name="user_acl_package_id" class="form-control">
                                        <option value="0">-- Select the package --</option>
                                        <option value="1">Gold</option>
                                        <option value="2">Silver</option>
                                        <option value="3">Bronze</option>
                                        <option value="4">Free Subscription</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                        <input id="input-save-new" type="submit" class="btn btn-warning" value="Save &amp; add new" name="addNew">
                        <input id="input-save-list" type="submit" class="btn btn-warning" value="Save &amp; return to list" name="returnToList">
                    </form>
                    <script type="text/javascript">
                        $(function() {
                            Gc.saveCommand();
                            Gc.checkDataChanged();
                        });
                        var role = $("#role_select select").val();
                        if (role && role == 2) {
                            $("#if_freelancer").show(700);
                            $(".if_fre").show(700);
                        }
                        if (role && role == 3) {
                            $("#if_freelancer").show(700);
                            $(".if_fre").hide(700);
                        }
                        if (role && role != 3 && role != 2) {
                            $("#if_freelancer").hide(700);
                        }
                        $("#role_select select").change(function() {
                            var newRole = $("#role_select select").val();
                            if (newRole && newRole == 2) {
                                $("#if_freelancer").show(700);
                                $(".if_fre").show(700);
                            }
                            if (newRole && newRole == 3) {
                                $("#if_freelancer").show(700);
                                $(".if_fre").hide(700);
                            }
                            if (newRole && newRole != 3 && newRole != 2) {
                                $("#if_freelancer").hide(700);
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
      
        <script>
        document.getElementById('userForm').addEventListener('submit', function (event) {
            if (!validateForm()) {
                event.preventDefault();
            }
        });

        function validateForm() {
            var email = document.getElementById('email').value;
            var login = document.getElementById('login').value;
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password_confirmation').value;
            var lastname = document.getElementById('lastname').value;
            var firstname = document.getElementById('firstname').value;

            if (email.trim() === '') {
                alert('Email is required');
                return false;
            }

            if (login.trim() === '') {
                alert('Login is required');
                return false;
            }

            if (password.trim() === '') {
                alert('Password is required');
                return false;
            }

            if (confirmPassword.trim() === '' || confirmPassword !== password) {
                alert('Password confirmation does not match');
                return false;
            }

            if (lastname.trim() === '') {
                alert('Lastname is required');
                return false;
            }

            if (firstname.trim() === '') {
                alert('Firstname is required');
                return false;
            }
            return true;
        }
    </script>
document.addEventListener('DOMContentLoaded', function () {
    const nameRegex = /^[A-Za-z]{2,}(?: [A-Za-z]{2,})?$/;
    const firstname = document.getElementById('firstname');
    const lastname = document.getElementById('lastname');

    // Get both submit buttons
    const submitBtns = [
        document.getElementById('input-save'),
        document.getElementById('input-save-new')
    ];

    const allowOnlyLettersAndOneSpace = (e) => {
        const key = e.key;
        const value = e.target.value;

        const isLetter = /^[a-zA-Z]$/.test(key);
        const isSpace = key === ' ';

        if (isLetter) return;
        if (isSpace && value.length > 0 && !value.includes(' ')) return;

        e.preventDefault();
    };

    const validateField = (input, errorElementId) => {
        const value = input.value.trim();
        const errorEl = document.getElementById(errorElementId);

        if (!nameRegex.test(value) || value.length < 4 || value.length > 30) {
            errorEl.textContent = "Must be 4–30 letters, alphabet only.";
            input.classList.add('is-invalid');
            return false;
        } else {
            errorEl.textContent = "";
            input.classList.remove('is-invalid');
            return true;
        }
    };

    const checkFormValidity = () => {
        const isFirstnameValid = validateField(firstname, 'firstname-error');
        const isLastnameValid = validateField(lastname, 'lastname-error');
        const enable = isFirstnameValid && isLastnameValid;

        submitBtns.forEach(btn => {
            btn.disabled = !enable;
        });
    };

    firstname.addEventListener('keypress', allowOnlyLettersAndOneSpace);
    lastname.addEventListener('keypress', allowOnlyLettersAndOneSpace);

    firstname.addEventListener('input', checkFormValidity);
    lastname.addEventListener('input', checkFormValidity);

    checkFormValidity(); // Initial check
});
    </body>

    </html>
@endsection
