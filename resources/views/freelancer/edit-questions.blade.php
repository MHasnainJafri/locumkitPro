@extends('layouts.user_profile_app')
@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/freelancer/dashboard">My Dashboard</a></li>
                        <li><a href="/freelancer/edit-questions">EDIT REGISTERATION</a></li>
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
                        <h3>EDIT REGISTERATION</h3>
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

                                    <form id="one-page-form" action="/freelancer/edit-questions" method="post" class="user_question_edit_form">
                                        @csrf
                                        @method('PUT')

                                        <h3>The following questions will enable us to notify you about job opportunities relevant to your individual needs and clinical competencies.</h3>
                                        <div class='col-md-12 margin-bottom' align='center'><a id="how-to-answer-question" href="/how-to-answer-question-fre" target='_blank' class='tip_font2' style='text-align:center;'>( Please click here for
                                                help on how to answer these questions)</a>
                                        </div>

                                        <div class="col-md-12 margin-bottom">
                                            {!! $user_database_questions_html !!}
                                        </div>

                                        <div id="free_min_rate" class="register-frm register-frm-next-step">
                                            <div class="col-md-11" id="free_min_rate_open">
                                                <div class="col-md-6 text-right">
                                                    <p>Please enter the minimum acceptable rate<i class="fa fa-asterisk required-stars" aria-hidden="true"></i><br><span class="tip_font2">( Please enter a whole number, ie: 250 )</span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="col-md-12 padding-none">
                                                        <div class="col-md-3 padding-none">
                                                            <p class="font-weight-500">Monday</p>
                                                        </div>
                                                        <div class="col-md-9 padding-none"><input type="number" name="min_rate[]" value="{{ isset($minimum_rate['Monday']) ? $minimum_rate['Monday'] : '' }}"
                                                                   class="form-control input-text width-100 req-qus-001 min-rate" placeholder="Enter minimum rate">
                                                            <div id="required-qus-001" style="clear: both;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 padding-none">
                                                        <div class="col-md-3 padding-none">
                                                            <p class="font-weight-500">Tuesday</p>
                                                        </div>
                                                        <div class="col-md-9 padding-none"><input type="number" name="min_rate[]" value="{{ isset($minimum_rate['Tuesday']) ? $minimum_rate['Tuesday'] : '' }}"
                                                                   class="form-control input-text width-100 req-qus-002 min-rate" placeholder="Enter minimum rate">
                                                            <div id="required-qus-002" style="clear: both;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 padding-none">
                                                        <div class="col-md-3 padding-none">
                                                            <p class="font-weight-500">Wednesday</p>
                                                        </div>
                                                        <div class="col-md-9 padding-none"><input type="number" name="min_rate[]" value="{{ isset($minimum_rate['Wednesday']) ? $minimum_rate['Wednesday'] : '' }}"
                                                                   class="form-control input-text width-100 req-qus-003 min-rate" placeholder="Enter minimum rate">
                                                            <div id="required-qus-003" style="clear: both;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 padding-none">
                                                        <div class="col-md-3 padding-none">
                                                            <p class="font-weight-500">Thursday</p>
                                                        </div>
                                                        <div class="col-md-9 padding-none"><input type="number" name="min_rate[]" value="{{ isset($minimum_rate['Thursday']) ? $minimum_rate['Thursday'] : '' }}"
                                                                   class="form-control input-text width-100 req-qus-004 min-rate" placeholder="Enter minimum rate">
                                                            <div id="required-qus-004" style="clear: both;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 padding-none">
                                                        <div class="col-md-3 padding-none">
                                                            <p class="font-weight-500">Friday</p>
                                                        </div>
                                                        <div class="col-md-9 padding-none"><input type="number" name="min_rate[]" value="{{ isset($minimum_rate['Friday']) ? $minimum_rate['Friday'] : '' }}"
                                                                   class="form-control input-text width-100 req-qus-005 min-rate" placeholder="Enter minimum rate">
                                                            <div id="required-qus-005" style="clear: both;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 padding-none">
                                                        <div class="col-md-3 padding-none">
                                                            <p class="font-weight-500">Saturday</p>
                                                        </div>
                                                        <div class="col-md-9 padding-none"><input type="number" name="min_rate[]" value="{{ isset($minimum_rate['Saturday']) ? $minimum_rate['Saturday'] : '' }}"
                                                                   class="form-control input-text width-100 req-qus-006 min-rate" placeholder="Enter minimum rate">
                                                            <div id="required-qus-006" style="clear: both;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 padding-none">
                                                        <div class="col-md-3 padding-none">
                                                            <p class="font-weight-500">Sunday</p>
                                                        </div>
                                                        <div class="col-md-9 padding-none"><input type="number" name="min_rate[]" value="{{ isset($minimum_rate['Sunday']) ? $minimum_rate['Sunday'] : '' }}"
                                                                   class="form-control input-text width-100 req-qus-007 min-rate" placeholder="Enter minimum rate">
                                                            <div id="required-qus-007" style="clear: both;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        
                                                        <div id="error-message" style="color: red; font-weight: bold; display: none; text-align: right;"></div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div id="storeinfo_div">
                                            <div class="col-md-11 margin-bottom">
                                                <div class="col-md-6 text-right">
                                                    <p>How far are you willing to travel?<i class="fa fa-asterisk required-stars" aria-hidden="true"></i></p>
                                                    <p><em style="font-style: italic;font-size: 12px;"><a href="/maps" style="color:#00a8dd;">Click here</a> to view a map of UK boroughs/counties.</em></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <input name="city" type="hidden" class="form-control margin-bottom" value="{{ Auth::user()->user_extra_info->city }}" id="city" />
                                                    <input name="address" type="hidden" class="form-control margin-bottom" value="{{ Auth::user()->user_extra_info->address }}" id="address" />
                                                    <input name="zip" type="hidden" class="form-control margin-bottom" value="{{ Auth::user()->user_extra_info->zip }}" id="zip" />
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 5) checked @endif value="5"
                                                                   onclick="get_list(this.value);">5 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 10) checked @endif value="10"
                                                                   onclick="get_list(this.value);">10 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 15) checked @endif value="15"
                                                                   onclick="get_list(this.value);">15 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 20) checked @endif value="20"
                                                                   onclick="get_list(this.value);">20 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 25) checked @endif value="25"
                                                                   onclick="get_list(this.value);">25 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 30) checked @endif value="30"
                                                                   onclick="get_list(this.value);">30 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 35) checked @endif value="35"
                                                                   onclick="get_list(this.value);">35 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 40) checked @endif value="40"
                                                                   onclick="get_list(this.value);">40 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 45) checked @endif value="45"
                                                                   onclick="get_list(this.value);">45 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 50) checked @endif value="50"
                                                                   onclick="get_list(this.value);">50 miles</span></div>
                                                    <div class="dist_list"><span><input type="radio" name="max_distance" class="input-text margin-right" @if ($max_distance == 'Over 50') checked @endif value="Over 50">Over 50
                                                            miles</span></div>

                                                    <div style="clear: both;" id="max_distance_error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-11">
                                                <div class="celltip-bmm regist-tip">
                                                    <div class="col-md-6 celltip-wppr text-right">
                                                        <p style="margin: 14px 0px">How many CET points do you have in the current cycle ? <a href="javascript:void(0);" data-toggle="tooltip" title=""
                                                               data-original-title="Locumkit will require evidence of your CET points every three months to ensure credibility."><i class="fa fa-question-circle" aria-hidden="true"></i></a></p>
                                                    </div>
                                                    <div class="col-md-6 text-left"><input type="text" name="cet" id="cet" class="form-control input-text width-100" value="{{ Auth::user()->user_extra_info->cet ?? '' }}"
                                                               placeholder="000" maxlength="3" autocomplete="off" style="margin:10px 0px" value=""> </div>
                                                </div>
                                            </div>
                                            <div class="col-md-11">
                                                <div class="col-md-6 text-right">
                                                    <p style="margin: 8px 0px">What is your GOC No?<i class="fa fa-asterisk required-stars" aria-hidden="true"></i></p>
                                                </div>
                                                <div class="col-md-6 text-left"><input required type="text" name="goc" id="goc" class="form-control input-text width-100 req-qus-10001"
                                                           value="{{ Auth::user()->user_extra_info->goc ?? '' }}" placeholder="01-12345 or D-00000" maxlength="8" autocomplete="off">
                                                    <div id="required-qus-10001"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-11">
                                                <div class="col-md-6 text-right">
                                                    <p style="margin: 14px 0px">Indemnity insurance: If AOP, what is your AOP membership number? </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" name="aop" id="aop" class="form-control input-text width-100 req-qus-10000" value="{{ Auth::user()->user_extra_info->aop ?? '' }}" placeholder="00000"
                                                           maxlength="5" autocomplete="off" style="margin:10px 0px">
                                                </div>
                                            </div>
                                            <div class="col-md-11">
                                                <div class="col-md-6 text-right">
                                                    <p style="margin: 8px 0px"> If not AOP, please insert Company insured with, Policy number, Date of renewal? </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <div id="inshu_open" class="margin-top">
                                                        <input type="text" value="{{ Auth::user()->user_extra_info->inshurance_company ?? '' }}" name="inshurance_company" id="inshurance_company"
                                                               class="form-control input-text width-100 margin-bottom" placeholder="company name" autocomplete="off">
                                                        <input type="text" value="{{ Auth::user()->user_extra_info->inshurance_no ?? '' }}" name="inshurance_no" id="inshurance_no" class="form-control input-text width-100 margin-bottom"
                                                               placeholder="Membership number" autocomplete="off" minlength="1" maxlength="10">
                                                        <!--<input type="date" value="{{ Auth::user()->user_extra_info->inshurance_renewal_date ?? '' }}" name="inshurance_renewal_date" id="inshurance_renewal_date"-->
                                                        <!--       class="form-control input-text width-100 margin-bottom" placeholder="dd-mm-yyyy" autocomplete="off" maxlength="10">-->
                                                         <input type="text" name="inshurance_renewal_date"
                                                            id="inshurance_renewal_date"
                                                            class="form-control input-text width-100 margin-bottom"
                                                            placeholder="dd/mm/yyyy" autocomplete="off" maxlength="10"
                                                            value="{{ Auth::user()->user_extra_info->inshurance_renewal_date ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-11">
                                                <div class="col-md-6 margin-bottom text-right">
                                                    <p style="margin: 14px 0px">What is your Opthalmic List Number (OPL)?</p>
                                                </div>
                                                <div class="col-md-6"><input type="text" value="{{ Auth::user()->user_extra_info->aoc_id ?? '' }}" name="aoc_id" id="aoc_id" placeholder="OPL 11-11111/1AA" maxlength="16"
                                                           class="form-control input-text width-100 -req-qus-10002" style="margin:10px 0px">
                                                    <div id="-required-qus-10002"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="getlist-section" class="modal fade" role="dialog">
                                            <div class="list-popup">
                                                <div class="modal-dialog" style="height: calc(100vh - 8rem);">

                                                    <div class="modal-content">
                                                        <div class="modal-header no-border-bottom">
                                                            <button type="button" class="close" data-dismiss="modal" onclick="close_dive('getlist-section');">×</button>
                                                            <h4 class="modal-title">Towns list</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h3 id="load_list" style="display:none"><img src="/frontend/locumkit-template/img/loader.gif"> Please wait... </h3>
                                                            <div id="store_list_div">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer no-border-top">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-offset-3 col-md-5" align="center">
                                            <button type="submit" class="btn btn-small btn-warning"> Save Answer </button>
                                        </div>

                                    </form>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#inshurance_renewal_date").datepicker({
                dateFormat: "dd/mm/yy" // Change format to match your placeholder
            });
        });
    </script>
    <script>
        function get_list(id) {
            if (id == 'Over 50') {
                $('#store_selected').val(id);
            } else {
                var town = $("#city").val();
                var addr = $("input#address").val() + "+" + $("input#city").val() + ",+UK";
                var zip = $("input#zip").val();
                var cat_id = $("#profession_type").val();
                var store_id = $("#store_id").val();
                var store_data = $("#store_data").val();
                $('#store_selected').val(id);
                $('#getlist-section').show();
                $('#getlist-section').addClass('in');
                $('#getlist-section').css('display', 'block');
                $("#load_list").show();
                $("#store_list_div").html('');
                $.ajax({
                    'url': '/ajax/get-town-list',
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    'type': 'POST',
                    'data': {
                        'max_dis': id,
                        'city': town,
                        'cat_id': cat_id,
                        'full_addr': addr,
                        'zip': zip
                    },
                    'success': function(result) { //alert(result);
                        if (result) {
                            $("#load_list").hide();
                            $("#store_list_div").html(result.html);
                            $('#getlist-section').show();
                            $('#getlist-section').addClass('in');
                            $('#getlist-section').css('display', 'block');
                        }
                    }
                });

            }
        }

        function close_dive(id) {
            $("#" + id).hide(1000);
            $('.modal-backdrop').hide(1000);
        }

        function save_list() {


            $("#getlist-section").hide(1000);
            $('.modal-backdrop').hide(1000);

        }
    </script>

    <script type="text/javascript">
        $('input#cet').keydown(function(e) {
            var key = e.charCode || e.keyCode || 0;
            return (key == 8 ||
                key == 9 ||
                key == 46 ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
        $('#goc').keydown(function(e) {
                var key = e.charCode || e.keyCode || 0;
                $goc = $(this);

            })

            .bind('focus click', function() {
                $goc = $(this);

                if ($goc.val().length === 0) {} else {
                    var val = $goc.val();
                    $goc.val('').val(val); // Ensure cursor remains at the end
                }
            })

            .blur(function() {
                $goc = $(this);

                if ($goc.val() === '(') {
                    $goc.val('');
                }
            });
        $('#aop').keydown(function(e) {
                var key = e.charCode || e.keyCode || 0;
                $aop = $(this);

                return (key == 8 ||
                    key == 9 ||
                    key == 46 ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));
            })

            .bind('focus click', function() {
                $aop = $(this);

                if ($aop.val().length === 0) {} else {
                    var val = $aop.val();
                    $aop.val('').val(val);
                }
            })

            .blur(function() {
                $aop = $(this);

            });

        $('#aoc_id').keydown(function(e) {
                var key = e.charCode || e.keyCode || 0;
                $aoc_id = $(this);
                //alert(key);
                // Auto-format- do not expose the mask as the user begins to type
                if ($aoc_id.val().length == 0) {
                    $aoc_id.val('OPL/' + $aoc_id.val());
                }
                if ($aoc_id.val().length < 15) {
                    if (key !== 8 && key !== 9) {
                        if ($aoc_id.val().length === 6) {
                            $aoc_id.val($aoc_id.val() + '-');
                        }
                        if ($aoc_id.val().length === 12) {
                            $aoc_id.val($aoc_id.val() + '/');
                        }
                    }
                }
                return (key == 8 ||
                    key == 9 ||
                    key == 46 ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105) || (key >= 65 && key <= 90) || (key >= 97 && key <= 122));
            })

            .blur(function() {
                $aoc_id = $(this);

                if ($aoc_id.val() === '(') {
                    $aoc_id.val('');
                }
            });
    </script>
    

<script>
    document.getElementById('free_min_rate_open').addEventListener('input', function (event) {
        if (event.target.classList.contains('min-rate')) {
            let value = event.target.value;
            if (value < 0) {
                // Show the error message
                document.getElementById('error-message').style.display = 'block';
                document.getElementById('error-message').textContent = 'Please enter a positive number for all rates';

                // Convert the value to positive
                value = Math.abs(value);

                // Update the input field with the positive value
                event.target.value = value;
            } else {
                // Hide the error message if the value is valid
                document.getElementById('error-message').style.display = 'none';
            }

            // Ensure the value is a whole number (no decimals)
            event.target.value = Math.floor(value);
        }
    });
</script>


@endpush
