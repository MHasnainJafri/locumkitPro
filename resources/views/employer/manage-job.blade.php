@extends('layouts.user_profile_app')
@push('styles')
    <style>
        .ui-datepicker {
            width: 28em !important;
            margin: 0 auto 20px;
        }
    </style>
@endpush
@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/employer/dashboard">My Dashboard</a></li>
                        <li><a href="#">Post Job</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="fa fa-suitcase" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Post Job</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content post-job">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <section>
                    </section>
                    <div class="col-sm-9 col-md-8 col-lg-7 post-job-content">
                        <div class="job-content">
                            <form id="mamagejob" @if ($job && $job_edit_action) action="/employer/managejob/{{ $job->id }}" @else action="/employer/managejob" @endif method="post">
                                @csrf
                                @if ($job && $job_edit_action)
                                    @method('PUT')
                                @endif
                                <div class="col-md-12 margin-bottom margin-top">
                                </div>

                                <div class="mar-mins" id="step2" style="display:block;">
                                    <div class="col-md-12">
                                        <div class="col-md-4"> Please select store to post job </div>
                                        <div class="col-md-8">
                                            <select name="job_store" id="job_store" class="form-control" required>
                                                <option value="" disabled selected>Select Store</option>
                                                @foreach ($employer_store_list as $store)
                                                    <option value="{{ $store->id }}" @if (isset($job) && $job) @selected($job->employer_store_list_id == $store->id) @endif> {{ $store->store_name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-4">Job reference </div>
                                        <div class="col-md-8"><input type="text" name="job_title" minlegnth="5" maxlength="50" class="form-control margin-bottom" @if (isset($job) && $job) value="{{ $job->job_title }}" @endif
                                                   placeholder="Enter job title for your reference" required></div>
                                    </div>
                                    <div class="col-md-12"> 
                                        <div class="col-md-4">Date required</div>
                                        <div class="col-md-8">
                                            <input type="text" name="job_date" class="form-control margin-bottom req-datepicker" @if (isset($job) && $job) value="{{ get_date_with_default_format($job->job_date) }}" @endif
                                                   placeholder="Enter date" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="col-md-4">Rate offered(£)</div>
                                        <div class="col-md-8">
                                            <input 
                                                type="text" 
                                                name="job_rate" 
                                                maxlength="6" 
                                                class="form-control margin-bottom numbersOnly" 
                                                @if (isset($job) && $job) 
                                                    value="{{ $job->job_rate }}" 
                                                @endif
                                                placeholder="Enter job rate in (£)" 
                                                required 
                                                oninput="if (this.value.length > 6) this.value = this.value.slice(0, 6); validateJobRate(this)" 
                                            />
                                        </div>
                                        
                                        
                                        <script>
                                            function validateJobRate(input) {
                                                const min = 0;
                                                const max = 9999999;
                                        
                                                if (input.value < min) {
                                                    input.value = min;
                                                } else if (input.value > max) {
                                                    input.value = max;
                                                }
                                            }
                                        </script>


                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-8 timeline-div">
                                            <div class="timeline-box"><input type="checkbox" name="set_timeline" value="1" class="form-control margin-bottom" id="open_timeline" @if (isset($job) && $job && sizeof($job->job_post_timelines) > 0) checked @endif></div>
                                            <div class="timeline-text">If you wish to increase the rate if a locum is not booked, please click here.</div>
                                            <div class="" id="show_add" @if (isset($job) && $job && sizeof($job->job_post_timelines) > 0) style="" @else style="display:none;" @endif><a href="javascript:void(0);" class="color-white" id="add_timeline"><i
                                                       class="fa fa-plus" aria-hidden="true" title="Add Timeline"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="timeline_box" @if (isset($job) && $job && sizeof($job->job_post_timelines) > 0) style="" @else style="display:none;" @endif>
                                        <div class="col-md-4">Timeline date</div>
                                        <div class="col-md-8 list_block">
                                            @if (isset($job) && $job && sizeof($job->job_post_timelines) > 0)
                                                @foreach ($job->job_post_timelines as $timeline)
                                                    <div class="add_block">
                                                        <div class="col-md-4 no-padding-left">
                                                            <input type="text" name="job_date_new[]" value="{{ get_date_with_default_format($timeline->job_date_new) }}" class="form-control margin-bottom datepicker" placeholder="Enter date"
                                                                   required>
                                                        </div>
                                                        <div class="col-md-3 col-sm-12 no-padding-left job_price_div">
                                                            <input type="text" name="job_rate_new[]" value="{{ $timeline->job_rate_new }}" class="form-control margin-bottom" placeholder="Price" required>
                                                        </div>
                                                        <div class="col-md-4 no-padding-left job_hrs_div no-padding-right">
                                                            <select name="job_timeline_hrs[]" class="form-control margin-bottom" required>
                                                                <option value="">Hours</option>
                                                                <option @selected($timeline->job_timeline_hrs == '1') value="1">1</option>
                                                                <option @selected($timeline->job_timeline_hrs == '2') value="2">2</option>
                                                                <option @selected($timeline->job_timeline_hrs == '3') value="3">3</option>
                                                                <option @selected($timeline->job_timeline_hrs == '4') value="4">4</option>
                                                                <option @selected($timeline->job_timeline_hrs == '5') value="5">5</option>
                                                                <option @selected($timeline->job_timeline_hrs == '6') value="6">6</option>
                                                                <option @selected($timeline->job_timeline_hrs == '7') value="7">7</option>
                                                                <option @selected($timeline->job_timeline_hrs == '8') value="8">8</option>
                                                                <option @selected($timeline->job_timeline_hrs == '9') value="9">9</option>
                                                                <option @selected($timeline->job_timeline_hrs == '10') value="10">10</option>
                                                                <option @selected($timeline->job_timeline_hrs == '11') value="11">11</option>
                                                                <option @selected($timeline->job_timeline_hrs == '12') value="12">12</option>
                                                                <option @selected($timeline->job_timeline_hrs == '13') value="13">13</option>
                                                                <option @selected($timeline->job_timeline_hrs == '14') value="14">14</option>
                                                                <option @selected($timeline->job_timeline_hrs == '15') value="15">15</option>
                                                                <option @selected($timeline->job_timeline_hrs == '16') value="16">16</option>
                                                                <option @selected($timeline->job_timeline_hrs == '17') value="17">17</option>
                                                                <option @selected($timeline->job_timeline_hrs == '18') value="18">18</option>
                                                                <option @selected($timeline->job_timeline_hrs == '19') value="19">19</option>
                                                                <option @selected($timeline->job_timeline_hrs == '20') value="20">20</option>
                                                                <option @selected($timeline->job_timeline_hrs == '21') value="21">21</option>
                                                                <option @selected($timeline->job_timeline_hrs == '22') value="22">22</option>
                                                                <option @selected($timeline->job_timeline_hrs == '23') value="23">23</option>
                                                                <option @selected($timeline->job_timeline_hrs == '24') value="24">24</option>
                                                            </select>
                                                        </div>
                                                        <span class="removeclass small2"><i class="fa fa-times" aria-hidden="true"></i></span>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-md-12" id="timeline_box_new"></div>
                                    <div class="col-md-12" style="display:none">
                                        <div class="col-md-4">Job type</div>
                                        <div class="col-md-8">
                                            <select id="job_type" name="job_type" class="form-control margin-bottom" required>

                                                <option value="1">First come first serve</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-4">Job description</div>
                                        <div class="col-md-8">
                                            <textarea name="job_post_desc" class="form-control margin-bottom" placeholder="Enter any special instructions ie: half day / different timings">@php
                                                if (isset($job) && $job) {
                                                    echo $job->job_post_desc;
                                                }
                                            @endphp</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="col-md-12 store-note store-note-click">

                                            <p>To edit your store requirements<a href="javascript:void(0);" onClick="popup();" style=" color: #ffb200;">click here</a></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12" align="center">
                                        <button class="post-job-btn" style="border-radius:10px;">
                                            Save Job & Search for available locums 
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-5 sidebar-right-post-padd">
                        <div class="sidebar-notifications">
                            <div class="notifications">
                                <div class="set-icon"><img alt="" src="/frontend/locumkit-template/img/notification-ico.png" /></div>
                                <div class="set-title">Enter job requirements here .</div>
                                <div class="set-title">Note: All other requisites are automatically incorporated based on your replies upon registration</div>
                            </div>
                            <div class="notifications notf2">
                                <div class="set-icon"><img alt="" src="/frontend/locumkit-template/img/mobile-ico.png" /></div>
                                <div class="set-title">We use all inputs to carefully select all locums who match your requirement</div>
                            </div>
                        </div>

                        <div class="sidebar-help">
                            <h5>Need help? please <a href="/contact" style="color:#00a9e0">click here</a></h5>
                            <ul>
                                <li><a href="tel:07452 998 238"><img src="/frontend/locumkit-template/img/contact-ico.png"> 07452 998 238</a></li>
                                <li> <a href="mailto:admin@locumkit.com"> <img src="/frontend/locumkit-template/img/mail-ico.png"> admin@locumkit.com</a> </li>

                            </ul>
                        </div>
                    </div>
                    <div class="one-page-box widget-box no-border col-xs-12 visible">
                        <div class="widget-body">
                            <div class="widget-main form_settings managejob-frm">
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
        var dateObj = new Date();
        var currentYear = dateObj.getFullYear();
        var rangeYear = currentYear + 3;
        $(document).ready(datePickerCaller);

        (() => {
            var min_date = 0;
            var currentTime = new Date(`{{ now() }}`);
            if (currentTime.getHours() > 11) {
                min_date = 1;
            }
            if (currentTime.getHours() == 11 && currentTime.getMinutes() > 30) {
                min_date = 1;
            }
            $(".req-datepicker").datepicker({
                minDate: min_date,
                changeMonth: true,
                changeYear: true,
                beforeShowDay: DisableSpecificDatesReq,
                dateFormat: "dd/mm/yy",
                yearRange: currentYear + ':' + rangeYear,
            });
        })();

        function datePickerCaller() {
            $('.datepicker').each(function() {
                var min_date = 0;
                var currentTime = new Date(`{{ now() }}`);
                if (currentTime.getHours() > 11) {
                    min_date = 1;
                }
                if (currentTime.getHours() == 11 && currentTime.getMinutes() > 30) {
                    min_date = 1;
                }

                $(this).datepicker({
                    minDate: min_date,
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "dd/mm/yy",
                    yearRange: currentYear + ':' + rangeYear,
                });
            });
        }

        function DisableSpecificDatesReq(date) {
            var string = $.datepicker.formatDate('yy-mm-dd', date);
            var today = $.datepicker.formatDate('yy-mm-dd', new Date());
            if (string >= today) {
                return [true, "available-date", ""];
            } else {
                return [true, " ui-datepicker-unselectable ui-state-disabled", ""];
            }
        }
    </script>
    <script type="text/javascript">
$(document).ready(function() {
    const blockHtml = `
        <div class="add_block">
            <div class="col-md-4 no-padding-left">
                <input type="text" name="job_date_new[]" class="form-control margin-bottom datepicker in_date" placeholder="Enter date" required>
            </div>
            <div class="col-md-3 col-sm-12 no-padding-left job_price_div">
                <input type="number" min="0" max="9999999" name="job_rate_new[]" class="form-control margin-bottom in_rate" placeholder="Price" required oninput="if (this.value.length > 6) this.value = this.value.slice(0, 6); validateJobRate(this)" >
            </div>
            <div class="col-md-4 no-padding-left job_hrs_div no-padding-right">
                <select name="job_timeline_hrs[]" class="form-control margin-bottom in_hrs" required>
                    <option value="">Hours</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21">21</option>
                    <option value="22">22</option>
                    <option value="23">23</option>
                    <option value="24">24</option>
                </select>
            </div>
            <span class="removeclass small2"><i class="fa fa-times" aria-hidden="true"></i></span>
        </div>
    `;

    function addBlock() {
        $('.list_block').append(blockHtml);
        // Hide the fa-times icon for the first block
        if ($('.add_block').length === 1) {
            $('.add_block:last-child .removeclass').hide();
        } else {
            $('.add_block:last-child .removeclass').show();
        }
        datePickerCaller(); // Initialize the date picker
    }

    // Click event for adding new blocks
    $("#add_timeline").click(function() {
        addBlock();
    });

    // Initial block render when checkbox is checked
    $('input[name="set_timeline"]').click(function() {
        if (this.checked) {
            $('.in_date').attr('required', 'true');
            $('.in_rate').attr('required', 'true');
            $('.in_hrs').attr('required', 'true');
            $("#timeline_box").show();
            $("#show_add").show();
            addBlock(); // Append the first block
        } else {
            $('.in_date').removeAttr('required');
            $('.in_rate').removeAttr('required');
            $('.in_hrs').removeAttr('required');
            $("#timeline_box").hide();
            $("#show_add").hide();
            $('.list_block').html(""); // Clear the blocks
        }
    });

    // Remove block when the remove button is clicked
    $("body").on("click", ".removeclass", function(e) {
        if ($(".add_block").length > 1) {
            $(this).parent('.add_block').remove();
        }
    });
});

        $('.numbersOnly').keyup(function() {
            if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
                this.value = this.value.replace(/[^0-9\.]/g, '');
            }
        });

        function popup() {
            var w = 560;
            var h = 560;
            var left = Number((screen.width / 2) - (w / 2));
            var tops = Number((screen.height / 2) - (h / 2));
            var popper = window.open("/employer/edit-questions?popup=yes",
                "Store Requirements Window",
                `width=${w}, height=${h}, top=${tops}, left=${left}, menubar=no,status=no,scrollbars=yes`
            );
            popper.focus();
        }
    </script>
@endpush
