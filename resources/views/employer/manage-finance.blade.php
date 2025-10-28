@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/employer/dashboard">My Dashboard</a></li>
                        <li><a href="/employer/finance">Finance</a></li>
                        <li><a href="#">Add Transactions</a></li>
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
                        <h3>Add Transactions</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content register">
        <div class="container">
            <div class="row">
                <div class="white-bg manage-emp-finance-wrapp contents">
                    <section class="add_item pb30 text-left">
                        <div class="col-md-12 pad0">
                            <div class="text-capitalize finance-page-head text-center">Add Transactions</div>
                        </div>
                        <div class="col-md-12 pad0"></div>
                        <div class="col-md-12 pad0">
                            <form role="form" id='income_form' @if ($transaction) action="/employer/finance/manage-finance-transaction/{{ $transaction->id }}" @else action="/employer/finance/manage-finance-transaction" @endif
                                  method="post" class="add_item_form form-inline">
                                @csrf
                                @if ($transaction)
                                    @method('PUT')
                                @endif
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Locum Type <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                            <select name="fre_type" id="fre_type" class="form-control" required>
                                                <option value="">Select</option>
                                                <option @if ($transaction && $transaction->freelancer_type == '1') selected @endif value="1">Website</option>
                                                <option @if ($transaction && $transaction->freelancer_type == '2') selected @endif value="2">Private</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Job No. <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                            <input 
                                                type="number" 
                                                @if ($transaction) value="{{ $transaction->job_id }}" @endif 
                                                class="form-control" 
                                                name="job_id" 
                                                id="job_id" 
                                                placeholder="Job No" 
                                                required 
                                                min="1" 
                                                max="10000"
                                                oninput="validateJobId(this)">
                                        </div>
                                        
                                        <script>
                                            function validateJobId(input) {
                                                const min = parseInt(input.min, 10); // Minimum value: 1
                                                const max = parseInt(input.max, 10); // Maximum value: 1,000,000
                                                const value = parseInt(input.value, 10);
                                                
                                                if (value < min) {
                                                    input.value = min;
                                                } else if (value > max) {
                                                    input.value = max;
                                                }
                                            }
                                        </script>


                                    </div>
                                    <div id="error_div" class="has-error"></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1" id="locum-id-label">Locum ID <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                                <input 
                                                    type="number" 
                                                    @if ($transaction) value="{{ $transaction->freelancer_id }}" @endif 
                                                    class="form-control" 
                                                    name="fre_id" 
                                                    id="fre_id" 
                                                    placeholder="Locum ID" 
                                                    required 
                                                    min="1" 
                                                    max="1000000"
                                                    oninput="validateFreId(this)">
                                            </div>
                                            
                                            <script>
                                                function validateFreId(input) {
                                                    const min = parseInt(input.min, 10); // Minimum value: 1
                                                    const max = parseInt(input.max, 10); // Maximum value: 1,000,000
                                                    const value = parseInt(input.value, 10);
                                                    
                                                    if (value < min) {
                                                        input.value = min;
                                                    } else if (value > max) {
                                                        input.value = max;
                                                    }
                                                }
                                            </script>
                                        </div>
                                    <div id="error_div" class="has-error"></div>
                                </div>
                                <div class="col-md-12 no_field">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Date <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>

                                        <div class="col-md-7">
                                            <div class="input-group date form_date" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                <input class="form-control readonly" size="16" type="date" @if ($transaction) value="{{ $transaction->job_date ?? '' }}" @endif name="in_date"
                                                        placeholder="dd/mm/yyyy" required autocomplete="off">
                                            </div>
                                            <input type="hidden" id="dtp_input2" value="" /><br />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 no_field">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Rate <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                <span class="input-group-addon">£</span>
                                                <input 
                                                    name="rate" 
                                                    id="rate" 
                                                    type="number" 
                                                    oninput="checkRateValue(this)" 
                                                    @if ($transaction) value="{{ $transaction->job_rate }}" @endif 
                                                    class="form-control" 
                                                    placeholder="Rate" 
                                                    required 
                                                    min="1" 
                                                    max="100000">
                                            </div>
                                        </div>
                                        
                                        <script>
                                            function checkRateValue(input) {
                                                const min = parseInt(input.min, 10);
                                                const max = parseInt(input.max, 10);
                                                const value = parseFloat(input.value);
                                                
                                                if (isNaN(value)) {
                                                    input.value = '';
                                                } else if (value < min) {
                                                    input.value = min;
                                                } else if (value > max) {
                                                    input.value = max;
                                                }
                                            }
                                        </script>
                                    </div>
                                </div>
                                <div class="col-md-12 no_field">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Bonus</label></div>
                                        <div class="col-md-7">
                                                <div class="input-group">
                                                    <span class="input-group-addon">£</span>
                                                    <input 
                                                        name="bonus" 
                                                        id="bonus" 
                                                        oninput="checkBonusValue(this)" 
                                                        @if ($transaction) value="{{ $transaction->bonus ?? '' }}" @endif 
                                                        type="number" 
                                                        class="form-control" 
                                                        placeholder="Bonus">
                                                </div>
                                            </div>
                                            
                                            <script>
                                                function checkBonusValue(input) {
                                                    const value = parseFloat(input.value.replace(/[^0-9.]/g, ''));
                                                    const min = 0;
                                                    const max = 10000;
                                                    
                                                    if (isNaN(value) || value < min) {
                                                        input.value = min;
                                                    } else if (value > max) {
                                                        input.value = max;
                                                    } else {
                                                        input.value = value;
                                                    }
                                                }
                                            </script>

                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-7">
                                            <div class="checkbox">
                                                <label><input name="paid" id="paid" value='1' type="checkbox" @if ($transaction && $transaction->is_paid) checked @endif>Paid</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 new_bank_date no_field" @if ($transaction && $transaction->is_paid) style="" @else style="display:none" @endif id="paid_date_div">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Paid Date <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                            <div class="input-group date form_date" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                <input class="form-control" size="16" type="date" value="{{ $transaction->paid_date ?? '' }}" @if ($transaction && $transaction->paid_date) @endif name="paid_date"
                                                       id="" placeholder="Paid Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group text-center">
                                        <button type="submit" name="income_submit" id="income_submit" value="income_submit" class="read-common-btn grad_btn">Submit</button>
                                        <button type="button" id="income_submit_loding" class="read-common-btn grad_btn" style="display: none" disabled>Loading...</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $("#income_form").submit(function() {
                $('#income_submit').hide();
                $('#income_submit_loding').show();

            });

            $("#paid").click(function() {
                var job_type = $('#paid:checked').val();
                if (job_type == '1') {
                    $("input#paid_date").prop('required', true);
                    $('#paid_date_div').show(1000);
                } else {
                    $('#paid_date_div').hide(1000);
                    $('#paid_date').val('');
                    $("input#paid_date").prop('required', false);
                }
            });

            $("#job_id").keyup(function() {
                var job_id = $("#job_id").val();
                if (!isNaN(job_id) && job_id != '' && job_id.toString().indexOf('.') == -1) {} else {
                    $("#job_id").val('');
                }
            });

            $("#rate").keyup(function() {
                var rate = $("#rate").val();
                if (isNaN(rate)) {
                    $("#rate").val('');
                }

            });
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                $('input#paid_date').datepicker({
                    maxDate: '0',
                    dateFormat: 'dd/mm/yy'
                });
            });
            $(document).ready(function() {
                $('input#in_date').datepicker({
                    maxDate: '0',
                    dateFormat: 'dd/mm/yy',
                    minDate: '15/08/2022',
                });

                /* Locum Id required or not */
                $('select#fre_type').change(function() {
                    if ($(this).val() == '1') {
                        $('#locum-id-label i').show();
                        $('input#fre_id').attr('required', 'required');
                    } else {
                        $('#locum-id-label i').hide();
                        $('input#fre_id').removeAttr('required');
                    }
                });

                if ($('select#fre_type').val() == '1') {
                    $('#locum-id-label i').show();
                    $('input#fre_id').attr('required', 'required');
                } else {
                    $('#locum-id-label i').hide();
                    $('input#fre_id').removeAttr('required');
                }


            });
        </script>
        <script>
            function checkValue(input) {
                if (input.value > 100000) {
                    input.value = 100000;
                }
            }
        </script>

    @endpush
