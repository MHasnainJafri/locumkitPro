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
            @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            <!-- <form class="relative form-horizontal" action="/admin/config/user/question/edit/1" method="post" -->
            <form id="question-form" class="relative form-horizontal" action="{{ route('admin.question.store') }}" method="post"
                enctype="application/x-www-form-urlencoded">
                @csrf
                <div class="form-group">
                    <label class="required&#x20;control-label&#x20;col-lg-2"
                        for="user_acl_profession_id">Category</label>
                    <div class="col-lg-10">
                            <select class="form-control" id="active" name="category" required>
                                <option value="">Select Category</option>
                                @foreach($get_category as $key => $value)
                                    <option value=" {{$value->id}} "> {{$value->name}} </option>
                                @endforeach
                            </select>


                    </div>
                </div>
                <div class="form-group">
                    <label class="required&#x20;control-label&#x20;col-lg-2" for="freelancer_question">Question For
                        Freelancer</label>
                    <div class="col-lg-10">
                        <input type="text" name="freelancer_question" class="form-control @error('freelancer_question') is-invalid @enderror"  id="freelancer_question"
                            value="{{old('freelancer_question')}}">
                            @error('freelancer_question')
            <span class="text-danger">{{ $message }}</span>
        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="required&#x20;control-label&#x20;col-lg-2" for="employer_question">Question For
                        Employer</label>
                    <div class="col-lg-10">
                        <input type="text" name="employer_question" class="form-control @error('employer_question') is-invalid @enderror" id="employer_question"
                        value="{{old('employer_question')}}">
                        @error('employer_question')
            <span class="text-danger">{{ $message }}</span>
        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="required&#x20;control-label&#x20;col-lg-2" for="sort_order">Sort Order</label>
                    <div class="col-lg-10">
                        <input type="number" name="sort_order" class="form-control" min="1" id="sort_order">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-2" for="is_required">Is required</label>
                    <div class="col-lg-10">
                        <select name="is_required" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-2" for="is_required">Is Activated</label>
                    <div class="col-lg-10">
                        <select name="is_activated" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="required&#x20;control-label&#x20;col-lg-2" for="type">Answer Type</label>
                    <div class="col-lg-10">
                        <div id="qus_key">
                            <p><select name="type" class="form-control">
                                    <option value="1">Text Field</option>
                                    <option value="2" selected="selected">Select Option</option>
                                    <option value="3">Multi Select Option</option>
                                    <option value="4">Comparative Option</option>
                                    <option value="5">Range Option</option>
                                    <option value="6">Yes/No Option With Yes</option>
                                </select></p>
                        </div>

                    </div>
                </div>
                <div id="range_qus">
                    <div class="form-group">
                        <label class="required&#x20;control-label&#x20;col-lg-2" for="type_value_range_unit">Range
                            Unit </label>
                        <div class="col-lg-10">
                            <p><input type="text" name="range_type_unit" class="form-control"
                                    id="type_value_range_unit" placeholder="Enter&#x20;Range&#x20;Unit" value></p>
                        </div>
                    </div>
                </div>
                <div id="comp_qus">
                    <div class="form-group">
                        <label class="control-label&#x20;col-lg-2" for="type_value_range_type">Range Type</label>
                        <div class="col-lg-10">
                            <p><select name="range_type_condition" class="form-control" id="type_value_range_type" required>
                                    <option value="0">--Select--</option>
                                    <option value="1">Greater than</option>
                                    <option value="2">Greater than OR equal to</option>
                                    <option value="3">Less than </option>
                                    <option value="4">Less than OR equal to</option>
                                    <option value="5">Equal to</option>
                                </select></p>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="field_qus">
                    <label class="required&#x20;control-label&#x20;col-lg-2" for="values">Option Value</label>
                    <div class="col-lg-10">
                        <div id="qus_field">
                            <div>
                                <p id="edit1"><input type="text" name="values&#x5B;&#x5D;" class="form-control"
                                        id="values" value></p>
                                <div id="editVal"></div>
                            </div>
                            <a href="javascript:void(0)" id="add_qus">Add</a> | <a href="javascript:void(0)"
                                id="remove_qus">Remove</a>
                        </div>
                        <div id="range_field">
                            <div class="block-margin" id="range_field_div">
                                <div class="col-md-6" style="padding-left:0px;">
                                    <input type="number" class="form-control value_1" id="min_value_1"
                                        placeholder="Enter min value">
                                </div>
                                <div class="col-md-6" style="padding-right:0px;">
                                    <input type="number" class="form-control value_1" id="max_value_1"
                                        placeholder="Enter max value">
                                </div>
                                <p>
                                    <input type="hidden" name="values[]" class="form-control" id="values_1"
                                        placeholder="Enter value" value>
                                </p>
                                <div id="editRangeVal"></div>
                            </div>
                            <div style="clear:both"></div>
                            <div style="margin-top: 10px;">
                                <a href="javascript:void(0)" id="range_add_qus">Add</a> | <a
                                    href="javascript:void(0)" id="range_remove_qus">Remove</a>
                            </div>
                        </div>
                    </div>
                </div>
                <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                <input id="input-save" type="submit" class="btn btn-warning" value="Save &amp; add new"
                    name="submit">
            </form>
            <script>
document.getElementById('question-form').addEventListener('submit', function (e) {
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
            <script type="text/javascript">
                $(function () {
                    Gc.saveCommand();
                    Gc.checkDataChanged();
                    Gc.initRoles();
                });
                var i = 1;
                var j = 1;

                if ($('#qus_key select').val() == 2 || $('#qus_key select').val() == 3) {
                    $('#comp_qus').hide(500);
                    $('#range_qus').hide(500);
                    $('#range_field').hide(500);
                    $('#field_qus').show(1000);
                    $('#qus_field').show(1000);
                } else if ($('#qus_key select').val() == 4) {
                    $('#comp_qus').show(1000);
                    $('#range_qus').show(1000);
                    $('#range_field').hide(1000);
                    $('#field_qus').show(1000);
                    $('#qus_field').show(1000);
                } else if ($('#qus_key select').val() == 5) {
                    $('#comp_qus').hide(1000);
                    $('#field_qus').show(1000);
                    $('#qus_field').hide(1000);
                    $('#range_field').show(1000);
                    $('#range_qus').show(1000);
                } else {
                    $('#comp_qus').hide(1000);
                    $('#range_qus').hide(1000);
                    $('#field_qus').hide(1000);
                    $('#range_field').hide(1000);
                }



                $('#qus_key select').on('change', function () {
                    if (this.value == 2 || this.value == 3) {
                        $('#comp_qus').hide(500);
                        $('#range_qus').hide(500);
                        $('#range_field').hide(500);
                        $('#field_qus').show(1000);
                        $('#qus_field').show(1000);

                    } else if (this.value == 4) {
                        $('#comp_qus').show(1000);
                        $('#range_qus').show(1000);
                        $('#range_field').hide(1000);
                        $('#field_qus').show(1000);
                        $('#qus_field').show(1000);
                    } else if (this.value == 5) {
                        $('#comp_qus').hide(1000);
                        $('#field_qus').show(1000);
                        $('#qus_field').hide(1000);
                        $('#range_field').show(1000);
                        $('#range_qus').show(1000);
                    } else {
                        $('#comp_qus').hide(1000);
                        $('#range_qus').hide(1000);
                        $('#field_qus').hide(1000);
                        $('#range_field').hide(1000);
                    }
                });
                $("#add_qus").click(function () {
                    var nEle = '<p><input type="text" name="values&#x5B;&#x5D;" class="form-control" id="values" value=""></p>';
                    $("#editVal").append(nEle).show(1000);
                    i++;
                });
                $("#remove_qus").click(function () {
                    var lenghtEle = $("input#values").length;
                    if (lenghtEle > 1) {
                        $("input#values").last().remove();
                    } else {
                        alert("cant remove this");
                    }
                });


                $("#range_add_qus").click(function () {
                    j++;
                    var nEle =
                        '<div class="block-margin"><div class="col-md-6" style="padding-left:0px;"><input type="number" class="form-control value_' +
                        j + '" id="min_value_' + j +
                        '" placeholder="Enter min value"></div><div class="col-md-6" style="padding-right:0px;"><input type="number"  class="form-control value_' +
                        j + '" id="max_value_' + j +
                        '" placeholder="Enter max value"></div><p><input type="hidden" name="values[]" class="form-control" id="values_' +
                        j + '" placeholder="Enter value" value=""></p></div>';
                    $("#range_field_div").append(nEle).show(1000);
                    i++;
                    OnKeyUp();
                });
                $("#range_remove_qus").click(function () {
                    var lenghtEle = $(".block-margin").length;
                    if (lenghtEle > 1) {
                        $(".block-margin").last().remove();
                    } else {
                        alert("cant remove this");
                    }
                });
                OnKeyUp();

                function OnKeyUp() {
                    $(".block-margin input").keyup(function (e) {
                        var field_class = $(this).attr("class");
                        var field_number = field_class.replace("form-control value_", "")
                        var full_value = $("#min_value_" + field_number).val() + '-' + $("#max_value_" + field_number).val();
                        $("#values_" + field_number).val(full_value);
                    });
                }
            </script>
            <style type="text/css">
                .block-margin {
                    margin-top: 10px;
                    display: block;
                    float: left;
                    width: 100%;
                }
            </style>
            <script type="text/javascript">
                $('#edit1 input#values').val('opton1');
            </script>
            <script type="text/javascript">
                $('#editVal').append(
                    '<p><input type="text" name="values[]" class="form-control" id="values" value="option2" required></p>')
            </script>
            <script type="text/javascript">
                $('#editVal').append(
                    '<p><input type="text" name="values[]" class="form-control" id="values" value="option3" required></p>')
            </script>
            <script type="text/javascript">
                $('#editVal').append(
                    '<p><input type="text" name="values[]" class="form-control" id="values" value="option4" required></p>')
            </script>
            <script type="text/javascript">
                $('#editVal').append(
                    '<p><input type="text" name="values[]" class="form-control" id="values" value="option5" required></p>')
            </script>
            <script type="text/javascript">
                $('#editVal').append(
                    '<p><input type="text" name="values[]" class="form-control" id="values" value="option6" required></p>')
            </script>
            <script type="text/javascript">
                $('#editVal').append(
                    '<p><input type="text" name="values[]" class="form-control" id="values" value="option7" required></p>')
            </script>
            <script type="text/javascript">
                $('#editVal').append(
                    '<p><input type="text" name="values[]" class="form-control" id="values" value="op8" required></p>')
            </script>
            <script type="text/javascript">
                $("#range_type_unit").val("");
            </script>
            <script type="text/javascript">
                $("#range_type_condition").val("");
            </script>
            <script type="text/javascript">
//$('#qus_key select').attr('disabled',true);
            </script>
        </div>
    </div>
</div>
@endsection
