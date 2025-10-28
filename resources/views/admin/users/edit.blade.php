@extends('admin.layout.app')
@section('content')
<style>
    .input-group {
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .input-group-append {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .toggle-password {
        border: none;
        background: transparent;
        cursor: pointer;
    }

</style>
    <div class="main-container container">
        <!-- Sidebar code here -->
        @include('admin.layout.sidebar')

        <div class="col-lg-12 main-content">
            <!-- Breadcrumbs code here -->
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
                    <li class="">
                        User </li>
                    <li class="active">
                        Edit </li>
                </ul>
            </div>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <div class="page-content">
                <form class="relative form-horizontal form" action="{{ route('admin.users.update', $user->id) }}" method="post"
                    enctype="application/x-www-form-urlencoded">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="email">Email</label>
                        <div class="col-lg-10">
                            <input type="text" name="email" readonly class="form-control" id="email"
                                value="{{ $user->email }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="login">Login</label>
                        <div class="col-lg-10">
                            <input type="text" readonly name="login" class="form-control" id="login"
                                value="{{ $user->login }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="password">Password</label>
                        <div class="col-lg-10">
                            <div class="input-group" style="display:flex; align-items: center; justify-content: end;">
                                <input type="password" name="password" class="form-control w-100 @error('password') is-invalid @enderror" autocomplete="off" id="password">
                                <div class="input-group-append" style="position:absolute;">
                                    <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('password')">
                                        <i class="fa fa-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password')
                                <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror 
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="password_confirmation">Confirm Password</label>
                        <div class="col-lg-10">
                            <div class="input-group" style="display:flex; align-items: center; justify-content: end;">
                                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="off" id="password_confirmation">
                                <div class="input-group-append" style="position:absolute;">
                                    <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('password_confirmation')">
                                        <i class="fa fa-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password_confirmation')
                                <span class="invalid-feedback text-danger" role="alert">
                                    <strong>Password doesn't match</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    
                    <script>
                        function togglePasswordVisibility(id) {
                            const passwordField = document.getElementById(id);
                            const toggleIcon = document.querySelector(`#${id} ~ .input-group-append .toggle-password i`);
                            
                            if (passwordField.type === "password") {
                                passwordField.type = "text";
                                toggleIcon.classList.remove("fa-eye-slash");
                                toggleIcon.classList.add("fa-eye");
                            } else {
                                passwordField.type = "password";
                                toggleIcon.classList.remove("fa-eye");
                                toggleIcon.classList.add("fa-eye-slash");
                            }
                        }
                    </script>





                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="lastname">Lastname</label>
                            <div class="col-lg-10">
                                <input type="text" name="lastname" class="form-control" id="lastname"
                                    value="{{ $user->lastname }}">
                                    <small class="text-danger" id="lastname-error"></small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="firstname">Firstname</label>
                            <div class="col-lg-10">
                                <input type="text" name="firstname" class="form-control" id="firstname"
                                    value="{{ $user->firstname }}">
                                            <small class="text-danger" id="firstname-error"></small>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="active">User Status</label>
                            <div class="col-lg-10">
                                <select name="active" class="form-control" id="active">
                                    <option value="0" @if ($user->active == 0) selected @endif>Disable
                                    </option>
                                    <option value="1" @if ($user->active == 1) selected @endif>Active</option>
                                    <!--<option value="2" @if ($user->active == 2) selected @endif>Block</option>-->
                                </select>
                            </div>
                        </div>
                        <div class="form-group is_disable" id="role_select">
                            <label class="required control-label col-lg-2" for="user_acl_role_id">Role</label>
                            <div class="col-lg-10">
                                <select name="user_acl_role_id" class="form-control" disabled>

                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            @if ($user->user_acl_role_id == $role->id) selected @endif>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="if_freelancer">
                            <div class="form-group is_disable" id="if_emp">
                                <label class="required control-label col-lg-2"
                                    for="user_acl_profession_id">Category</label>
                                <div class="col-lg-10">
                                    <select name="user_acl_profession_id" class="form-control" disabled>
                                        <option value="0">-- Select the category --</option>

                                        @foreach ($professionslist as $profession)
                                            <option value="{{ $profession->id }}"
                                                @if ($user->user_acl_profession_id == $profession->id) selected @endif>{{ $profession->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group if_fre is_disable">
                                <label class="required control-label col-lg-2" for="user_acl_package_id">Package</label>
                                <div class="col-lg-10">
                                    <select name="user_acl_package_id" class="form-control">
                                        <option value="0" disabled>-- Select the package --</option>

                                        @foreach ($packages as $pkg)
                                            <option value="{{ $pkg->id }}"
                                                @if ($user->user_acl_package_id == $pkg->id) selected @endif>{{ $pkg->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                        <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                </form>

                <!-- Additional forms or scripts here -->
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameRegex = /^[A-Za-z]{2,}(?: [A-Za-z]{2,})?$/;
    const firstname = document.getElementById('firstname');
    const lastname = document.getElementById('lastname');
    const submitBtn = document.getElementById('input-save');

    const allowOnlyLettersAndOneSpace = (e) => {
        const key = e.key;
        const value = e.target.value;

        const isLetter = /^[a-zA-Z]$/.test(key);
        const isSpace = key === ' ';

        // Allow letters only
        if (isLetter) return;

        // Allow one space only and not as first character
        if (isSpace && value.length > 0 && !value.includes(' ')) return;

        // Prevent anything else
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
        submitBtn.disabled = !(isFirstnameValid && isLastnameValid);
    };

    // Filter input
    firstname.addEventListener('keypress', allowOnlyLettersAndOneSpace);
    lastname.addEventListener('keypress', allowOnlyLettersAndOneSpace);

    // Validate on input
    firstname.addEventListener('input', checkFormValidity);
    lastname.addEventListener('input', checkFormValidity);

    checkFormValidity(); // Initial check
});
</script>

@endsection
