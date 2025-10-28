@extends('admin.layout.app')
@section('content')
    @inject('controller', 'App\Http\Controllers\admin\FinanceController')
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

            <div class="page-content" style="margin-top: -10px;">
                <form class="relative form-horizontal" action="/admin/tax/store" method="post"
                    enctype="application/x-www-form-urlencoded">
                    @csrf 
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="finance_year">Financial Year</label>
                        <div class="col-lg-10">
                            <input type="text" name="finance_year" class="form-control" id="finance_year" value="">
                        </div>
                        
                        <script>
                            document.getElementById('finance_year').addEventListener('input', function (event) {
                                const financeYearInput = event.target.value;
                                const allowedPattern = /^[0-9!@#$%^&*(),.?":{}|<>-]*$/;
                        
                                if (!allowedPattern.test(financeYearInput)) {
                                    alert("Only numbers, special characters, and hyphens are allowed. Text is not allowed.");
                                    event.target.value = financeYearInput.replace(/[a-zA-Z]/g, '');
                                }
                            });
                        </script>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="personal_allowance_rate">Personal
                            Allowance</label>
                        <div class="col-lg-5">
                            <input type="number" 
                                   name="personal_allowance_rate" 
                                   class="form-control" 
                                   placeholder="Max rate" 
                                   id="personal_allowance_rate" 
                                   value="" 
                                   min="0" 
                                   oninput="validatePositiveNumber(this)">
                        </div>
                        <div class="col-lg-5">
                            <input type="number" name="personal_allowance_rate_tax" class="form-control"
                                placeholder="Tax %" id="personal_allowance_rate_tax" value="" min="0" oninput="validatePositiveNumber(this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="basic_rate">Basic Rate</label>
                        <div class="col-lg-5">
                            <input type="number" name="basic_rate" class="form-control" placeholder="Max rate"
                                id="basic_rate" value="" min="0" oninput="validatePositiveNumber(this)">
                        </div>
                        <div class="col-lg-5">
                            <input type="number" name="basic_rate_tax" class="form-control" placeholder="Tax %"
                                id="basic_rate_tax" value="" min="0"  oninput="validatePositiveNumber(this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="higher_rate">Higher Rate</label>
                        <div class="col-lg-5">
                            <input type="number" name="higher_rate" class="form-control" placeholder="Max rate"
                                id="higher_rate" value="" min="0" oninput="validatePositiveNumber(this)">
                        </div>
                        <div class="col-lg-5">
                            <input type="number" name="higher_rate_tax" class="form-control" placeholder="Tax %"
                                id="higher_rate_tax" value="" min="0" oninput="validatePositiveNumber(this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="additional_rate">Additional Rate</label>
                        <div class="col-lg-5">
                            <input type="number" name="additional_rate" class="form-control" placeholder="Over min rate"
                                id="additional_rate" value="" min="0"  oninput="validatePositiveNumber(this)">
                        </div>
                        <div class="col-lg-5">
                            <input type="number" name="additional_rate_tax" class="form-control" placeholder="Tax %"
                                id="additional_rate_tax" value="" min="0" oninput="validatePositiveNumber(this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required control-label col-lg-2" for="company_limited_tax">Company Limited Tax
                            %</label>
                        <div class="col-lg-10">
                            <input type="number" name="company_limited_tax" class="form-control" id="company_limited_tax"
                                value="" min="0" oninput="validatePositiveNumber(this)">
                        </div>
                    </div>
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                </form>
                <script type="text/javascript">
                    $(function() {
                        Gc.saveCommand();
                        Gc.checkDataChanged();
                        Gc.initRoles();
                    });
                </script>
                <script>
                    function validatePositiveNumber(input) {
                        if (input.value < 0) {
                            input.value = '';
                        }
                    }
                </script>

            </div>

        </div>
    </div>
@endsection
