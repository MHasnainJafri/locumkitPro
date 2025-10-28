@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/employer/dashboard">My Dashboard</a></li>
                        <li><a href="/employer/edit-questions">EDIT REGISTERATION</a></li>
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

                                    <form id="one-page-form" action="/employer/edit-questions" method="post" class="user_question_edit_form">
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="popup" @if (request()->query('popup') == 'yes') value="1" @endif>

                                        <h3>The following questions will enable us to notify you about job opportunities relevant to your individual needs and clinical competencies.</h3>
                                        <div class='col-md-12 margin-bottom' align='center'><a id="how-to-answer-question" href="/how-to-answer-question-emp" target='_blank' class='tip_font2' style='text-align:center;'>( Please click here for
                                                help on how to answer these questions)</a>
                                        </div>

                                        <div id="emp_opt_store" class="register-frm-next-step">
                                            <div class="col-md-12" id="emp_store_list_fix">
                                                <div class="col-md-11">
                                                    <div class="col-md-6 text-right">
                                                        <p>What type of store do you run?</p>
                                                    </div>
                                                    <div class="col-md-6 margin-bottom">
                                                        <select name="store_id_emp" id="store_id_emp" required="" class="form-control">
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Boots') selected @endif value="Boots">Boots</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Specsavers') selected @endif value="Specsavers">Specsavers</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Vision') selected @endif value="Vision express">Vision express</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Asda') selected @endif value="Asda">Asda</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'David') selected @endif value="David Clulows">David Clulows</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Domaciliary') selected @endif value="Domaciliary">Domaciliary</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Independent') selected @endif value="Independent">Independent</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Leightons') selected @endif value="Leightons">Leightons</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Scrivens') selected @endif value="Scrivens">Scrivens</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Tesco') selected @endif value="Tesco">Tesco</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'The') selected @endif value="The Optical Shop">The Optical Shop</option>
                                                            <option @if (Auth::user()?->user_extra_info?->store_type_name == 'Optical') selected @endif value="Optical express">Optical express</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 margin-bottom">
                                            {!! $user_database_questions_html !!}
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
    <script>
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
@endpush
