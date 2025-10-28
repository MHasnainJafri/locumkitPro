@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="/freelancer/finance">Finance</a></li>
                        <li><a href="#">Bank details</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-credit-card" aria-hidden="true" style="line-height: 53px;"></i>
                    </div>
                    <div class="set-title">
                        <h3>Bank details</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <section class="add_item text-left">
                        <div class="col-md-12 pad0">
                            <div class="finance-page-head marb20 text-center">Bank details</div>
                        </div>
                        <div class="col-md-12 pad0"></div>

                        <form class="add_item_form form-inline" action="{{ route('freelancer.save-bank-details') }}" method="post" id="bank-detail-form">
                            @csrf
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-3"><label for="exampleInputPassword1">Your Account Name </label><i class="fa fa-asterisk required-stars" aria-hidden="true"></i></div>
                                    <div class="col-md-7">
                                        
                                        <input 
                                            id="acc-name" 
                                            minlength="5" 
                                            maxlength="40" 
                                            name="acc_name" 
                                            type="text" 
                                            @if ($detail) 
                                                value="{{ $detail->account_name }}" 
                                            @endif 
                                            placeholder="Enter account name" 
                                            class="form-control" 
                                            required 
                                            oninput="validateText(this)" 
                                        />
                                        
                                        <script>
                                            function validateText(input) {
                                                input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
                                            }
                                        </script>


                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-3"><label for="exampleInputPassword1">Your Account Number </label><i class="fa fa-asterisk required-stars" aria-hidden="true"></i></div>
                                    <div class="col-md-7">
                                        <input id="acc-number" name="acc_number" type="number" minlength="12" maxlength="17" @if ($detail) value="{{ $detail->acccount_number }}" @endif placeholder="0000000000000000"
                                               class="form-control" required>
                                        <div id="acc_number_error"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-3"><label for="exampleInputPassword1">Your Account Sort Code </label><i class="fa fa-asterisk required-stars" aria-hidden="true"></i></div>
                                    <div class="col-md-7">
                                        <input id="acc-sort-code" name="acc_sort_code" type="text" @if ($detail) value="{{ $detail->acccount_sort_code }}" @endif placeholder="XX-XX-XX" class="form-control" required>
                                        <div id="acc_sort_code_error"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <button type="submit" id="supplier-btn" class="read-common-btn grad_btn hide-btn">Save</button>
                                    <button type="button" id="supplier_submit_loding" class="read-common-btn grad_btn" style="display: none" disabled>Loading...</button>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $("#bank-detail-form").submit(function() {
            if ($('input#acc-number').val().length < 12 || $('input#acc-number').val().length > 17 || $('input#acc-sort-code').val().length !== 8) {
                if ($('input#acc-number').val().length < 12 || $('input#acc-number').val().length > 17) {
                    $('#acc_number_error').html(
                        '<div id="user_error" class="css_error">Please Enter Valid Account No .</div>');
                } else {
                    $('#acc_number_error').html('');
                };
                if ($('input#acc-sort-code').val().length !== 8) {
                    $('#acc_sort_code_error').html(
                        '<div id="user_error" class="css_error">Please Enter Valid Sort Code .</div>');
                } else {
                    $('#acc_sort_code_error').html('');
                };
                return false;
            } else {
                $('.hide-btn').hide();
                $('#supplier_submit_loding').show();
            }
        });
        
        $('input#acc-number').keydown(function(e) {
            var key = e.charCode || e.keyCode || 0;
            if ($(this).val().length === 17 && key != 8) {
                return false;
            }
        });
        
        $('#acc-sort-code').keydown(function(e) {
            var key = e.charCode || e.keyCode || 0;
            $acc_sort_code = $(this);

            // Auto-format- do not expose the mask as the user begins to type
            if (key !== 8 && key !== 9) {
                if ($acc_sort_code.val().length === 2) {
                    $acc_sort_code.val($acc_sort_code.val() + '-');
                }
                if ($acc_sort_code.val().length === 5) {
                    $acc_sort_code.val($acc_sort_code.val() + '-');
                }
                if ($acc_sort_code.val().length === 8) {
                    return false;
                }
            }

            // Allow numeric (and tab, backspace, delete) keys only
            return (key == 8 || key == 9 || key == 46 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105));
        }).bind('focus click', function() {
            $acc_sort_code = $(this);

            if ($acc_sort_code.val().length === 0) {
                //$acc_sort_code.val('(');
            } else {
                var val = $acc_sort_code.val();
                $acc_sort_code.val('').val(val); // Ensure cursor remains at the end
            }
        }).blur(function() {
            $acc_sort_code = $(this);

            if ($acc_sort_code.val() === '(') {
                $acc_sort_code.val('');
            }
        });
    </script>
@endpush
