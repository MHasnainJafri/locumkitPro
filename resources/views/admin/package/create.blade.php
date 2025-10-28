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
                    </ul>
                </div>
                <div class="page-content">
                    <form id="package-form" class="relative form-horizontal" action="{{route('admin.package.store')}}" method="post"
                        enctype="application/x-www-form-urlencoded">
                        @csrf
                        <div class="form-group">
                            <label class="required control-label col-lg-2" for="name">Package Name</label>
                            <div class="col-lg-10">
                                <input type="text" required name="name" class="form-control" id="name" value="{{old('name')}}"
                                       minlength="3" maxlength="50" placeholder="Enter package name">
                                @error('name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <script>
                                document.getElementById('name').addEventListener('input', function (event) {
                                    const nameInput = event.target.value;
                                    const specialCharPattern = /[!@#$%^&*(),.?":{}|<>]/g;
                                    
                                    if (/\d/.test(nameInput)) {
                                        alert("The name should not contain numbers.");
                                        event.target.value = nameInput.replace(/\d/g, '');
                                    } else if (specialCharPattern.test(nameInput)) {
                                        alert("The name should not contain special characters.");
                                        event.target.value = nameInput.replace(specialCharPattern, '');
                                    }
                                });
                            </script>
                        </div>
                        <div class="form-group">
                            <label class="optional&#x20;control-label&#x20;col-lg-2" for="price">Price</label>
                            <div class="col-lg-10">
                            <input type="number" name="price" class="form-control" required id="price" min="0" value="{{old('price')}}" oninput="checkValue(this)">
                            <div id="priceError" class="text-danger"></div>
                                @error('price')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="optional&#x20;control-label&#x20;col-lg-2" for="description">Description</label>
                            <div class="col-lg-10">
                                <input type="text" name="description" class="form-control" id="description" value="{{old('description')}}"
                                       maxlength="255" placeholder="Enter a description (optional)">
                                @error('description')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2"
                                for="user_acl_package_resources_ids_list">Resources</label>
                            <div class="col-lg-10 checkbox-resource">
@if ($errors->has('user_acl_package_resources_ids_list'))
                            <span class="text-danger">{{ $errors->first('user_acl_package_resources_ids_list') }}</span>
                        @endif
                                @foreach ($resources as $r)
                                      <label class="required control-label col-lg-2">
                                    <input type="checkbox" name="user_acl_package_resources_ids_list[]"
                                        class="form-control" value="{{$r->id}}" />
                                    {{$r->resource_key}} </label>
                                @endforeach


                            </div>
                        </div>
                        <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                        <input id="input-save" type="submit" class="btn btn-warning" value="Save & add new"
                            name="submit">
                    </form>
                    <script type="text/javascript">
                        $(function() {
                            Gc.saveCommand();
                            Gc.checkDataChanged();
                            Gc.initRoles();
                        });
                    </script>
                </div>
            </div>
        </div>


<script>
document.getElementById('package-form').addEventListener('submit', function (e) {
    const clickedButton = document.activeElement;

    if (clickedButton.type === 'submit') {
        // Let the form submit first, then disable
        setTimeout(() => {
            clickedButton.disabled = true;
            clickedButton.value = 'Saving...';
        }, 10); // 10 ms delay
    }
});



</script>

        <script>
        const MAX_VALUE = 1000000;
        // Function to check the value of the input
        function checkValue(input) {
            const priceError = document.getElementById('priceError');
            if (input.value < 0) {
                priceError.textContent = 'Price cannot be negative.';
                input.value = ''; // Clear the invalid input
            } else if (input.value > MAX_VALUE) {
                priceError.textContent = `Price cannot exceed ${MAX_VALUE}.`;
                input.value = MAX_VALUE; // Reset to the maximum value
            } else {
                priceError.textContent = ''; // Clear the error message
            }
        }

        // Function to prevent invalid input (negative sign or excessively large values)
        function preventInvalidInput(event) {
            const input = event.target;
            // Prevent the negative sign (-)
            if (event.key === '-') {
                event.preventDefault();
            }

            // Prevent typing more digits if the value exceeds the maximum length
            const maxDigits = MAX_VALUE.toString().length;
            if (input.value.length >= maxDigits && input.selectionStart === input.value.length) {
                event.preventDefault();
            }
        }
    </script>









@endsection
