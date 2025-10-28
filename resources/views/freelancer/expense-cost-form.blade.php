@extends('layouts.user_profile_app')
@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Add EXPENSE</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <section>
                        <div class="welcome-heading">
                            <h1><span>Add Today's Expense</span></h1>
                            <hr class="shadow-line">
                        </div>
                        <div clas="content mart30">
                            @if ($count > 0)
                                <h2 id="aleardy-submited" style=" color:#ff8d00"> You have already Submited expenses for this job. </h2>
                            @endif
                        </div>
                        @if ($count == 0)
                            <form method="post" id="expense-form">
                                @csrf

                                <div class="input_fields_wrap expense-form-wrapp">

                                    <div class="col-md-12 mart30">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">

                                                <select class="form-control" name="cat[]" id="cat0" required="">
                                                    <option value="">Select Category</option>
                                                    @foreach ($expense_types as $expense_type)
                                                        <option value="{{ $expense_type->id }}">{{ $expense_type->expense }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="cost[]" value="" placeholder="Amount" id="ex_rate0" onkeyup="onlyisnan(0)" required="" />
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="col-md-12">
                                                <button type="button" class="add_field_button btn btn-info btn-sm pull-right"><i class="fa fa-fw fa-plus"></i></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="submit-btn row">
                                    <div class="col-md-12">
                                        <input type="submit" name="submit" value="Submit" class="common-btn">
                                    </div>
                                </div>

                            </form>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $('input.form-control.width-100.input-text.margin-bottom').keypress(function(event) {
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

        $(document).ready(function() {
            var category = @json($expense_types);
            var r_data = category;
            var otpt1 = '<option value="">Select Category</option>';
            $.each(r_data, function(index, value) {
                otpt1 += '<option value="' + value.id + '">' + value.expense + '</option>';
            });
            var max_fields = 10;
            var wrapper = $(".input_fields_wrap");
            var add_button = $(".add_field_button");
            var x = 1;
            $(add_button).click(function(e) {
                e.preventDefault();
                if (x < max_fields) {
                    var catrr = '<div class="col-md-2"></div><div class="col-md-4"><div class="form-group">';
                    catrr += '<select class="form-control" name="cat[]" id="cat' + x + '" required="">' + otpt1 + '</select>';
                    catrr += '</div></div>';
                    var catval = '<div class="col-md-4"><div class="form-group">';
                    catval += '<input type="text" class="form-control" name="cost[]"  value=""  id="ex_rate' + x + '" onkeyup="onlyisnan(' + x + ')" placeholder="Amount" required=""/>';
                    catval += '</div></div>';
                    var remove = '<div class="col-md-1"><div class="col-md-12"><a href="#" class="remove_field btn-sm btn btn-danger pull-right remove_field-btn"><i class="fa fa-fw fa-remove"></i></a></div></div>';
                    $(wrapper).append('<div class="col-md-12">' + catrr + catval + remove + '</div>'); //add input box
                    x++; //text box increment
                }
            });

            $(wrapper).on("click", ".remove_field", function(e) { //user click on remove text
                e.preventDefault();
                $(this).parent().parent().parent('div').remove();
                x--;
            })
        });



        function onlyisnan(val) {
            var ex_rate = $("#ex_rate" + val).val();
            if (isNaN(ex_rate)) {
                $("#ex_rate" + val).val('');
            }
        }
    </script>
@endpush
