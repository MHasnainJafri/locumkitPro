@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="/freelancer/finance-detail">Finance</a></li>
                        <li><a href="#"> Add Expense</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3> Add Expense </h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg add-expense contents">

                    <section class="add_item text-left">
                        <div class="col-md-12 pad0">
                            <div class="finance-page-head text-center">Add expense</div>
                        </div>
                        <div class="col-md-12 pad0"></div>
                        <div class="col-md-12 pad0">

                            <form role="form" id="expense-form" action="{{ route('freelancer.add-expense-save') }}" method="POST" onsubmit="validexpenseform()" enctype="multipart/form-data" class="add_item_form form-inline">
                                @csrf
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Job Type <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                            <select name="ex_job_type" id="ex_job_type" class="form-control" required="required">
                                                <option value="">Select job type</option>
                                                <option value="1">Website</option>
                                                <option value="2">Private</option>
                                                <option value="3">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Job No</label></div>
                                        <div class="col-md-7"><input type="number" name="ex_job_id" id="ex_job_id" placeholder="Please enter job no." class="form-control"></div>
                                    </div>
                                </div>
                                <div class="col-md-12 no_field">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Date <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                <input type="" id="date-picker" name="ex_job_date" class="form-control" required autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 no_field">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Expense cost <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    £ </span>
                                                <input type="text" name="ex_job_cost" id="ex_job_cost" placeholder="Please Enter Amount" class="form-control" required>
                                                <div id="ex_job_cost_err"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Description</label></div>
                                        <div class="col-md-7">
                                            <textarea type="text" name="ex_job_description" id="ex_job_description" placeholder="Enter description of expense" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Category <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                            <select name="ex_category" id="ex_category" class="form-control" required>
                                                <option value="">Select category</option>
                                                @foreach ($expense_categories as $category)
                                                    <option value="{{ $category->id }}"> {{ $category->expense }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Receipt </label></div>
                                        <div class="col-md-7">
                                            <input type="file" name="receipt" id="receipt" class="form-control form-control-file">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-7">
                                            <div class="checkbox">
                                                <label for="ex_bank">
                                                    <input name="ex_bank" type="checkbox" id="ex_bank">
                                                    Please click if the cash has already left the bank.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 new_bank_date no_field" style="display:none" id="exbank_date">
                                    <div class="form-group">
                                        <div class="col-md-3"><label for="exampleInputPassword1">Bank Date <i class="fa fa-asterisk required-stars" aria-hidden="true"></i></label></div>
                                        <div class="col-md-7">
                                            <div class="input-group date form_date" autocomplete="off">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                <input type="" id="date-picker-1" class="form-control" name="ex_bank_date" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group text-center">
                                        <button type="submit" name="expense_submit" id="expense_submit" value="expense_submit" class="read-common-btn grad_btn">Submit</button>
                                        <button type="button" id="expense_submit_loding" class="read-common-btn grad_btn disabled" style="display: none">Loading...</button>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#date-picker").datepicker({
                dateFormat: "dd/mm/yy" // Change format to match your placeholder
            });
        });
        $(document).ready(function() {
            $("#date-picker-1").datepicker({
                dateFormat: "dd/mm/yy" // Change format to match your placeholder
            });
        });
    </script>
    <script>
    
        $("#expense-form").submit(function() {
            $('#expense_submit').hide();
            $('#expense_submit_loding').show();

        });


        $(document).ready(function() {
            $('input#ex_bank_date').datepicker({
                maxDate: '0',
                dateFormat: 'dd/mm/yy'
            });
            $('input#ex_job_date').datepicker({
                maxDate: '0',
                dateFormat: 'dd/mm/yy'
            });
        });


        $("input#ex_job_cost").keyup(function() {
            var ex_job_cost = $("#ex_job_cost").val();
            if (isNaN(ex_job_cost)) {
                $("#ex_job_cost").val('');
            }
        });



        function validexpenseform() {
            var exCost = $('input#ex_job_cost').val();
            var exCat = $('select#ex_category').val();
            if (exCost == '' || exCost == null) {
                $('#ex_job_cost_err').html('Please enter cost.');
                return false;
            } else {
                $('#ex_job_cost_err').html('');
            }
        }
        $('input#ex_bank').change(function() {
            var c = this.checked ? '1' : '0';
            if (c == 1) {
                $("input#ex_bank_date").prop('required', true);
                $('div#ex_bank_date_div').show(500);
            } else {
                $("input#ex_bank_date").prop('required', false);
                $('div#ex_bank_date_div').hide(300);
                $('input#ex_bank_date').val('');
            }
        });
        $("#ex_bank").click(function() {
            var job_type = $('#ex_bank:checked').val();
            var c = job_type ? '1' : 0;
            if (c == '1') {
                $('#exbank_date').show(1000);
            } else {
                $('#exbank_date').hide(1000);
                $('#ex_bank_date').val('');
            }
        });


        $('input#ex_job_date ,input#ex_bank_date').keydown(function(e) {
            var key = e.charCode || e.keyCode || 0;
            $goc = $(this);

            // Auto-format- do not expose the mask as the user begins to type
            if (key !== 8 && key !== 9) {
                if ($goc.val().length === 2) {
                    $goc.val($goc.val() + '/');
                }
                if ($goc.val().length === 5) {
                    $goc.val($goc.val() + '/');
                }
            }

            // Allow numeric (and tab, backspace, delete) keys only
            return (key == 8 ||
                key == 9 ||
                key == 46 ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        })
    </script>
@endpush
