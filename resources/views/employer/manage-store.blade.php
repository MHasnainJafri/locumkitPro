@extends('layouts.user_profile_app')

@push('styles')
    <style type="text/css">
        ul.ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content {
            max-width: 300px !important;
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
                        <li><a href="/employer/dashboard">Dashboard</a></li>
                        <li><a href="#">Manage Store</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="fa fa-user-plus" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Manage Store</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content register manage-store-wrapper">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <section>
                        <div class="col-md-12"></div>

                        <div class="col-md-12 manage-store">
                            <form id="mamagestore" action="/employer/manage-store" method="post">
                                @method('PUT')
                                @csrf
                                <div class="user-store-list heading-list">
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <p>Store name</p>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-4">
                                        <p>Store address</p>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2">
                                        <p>Store location</p>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-2">
                                        <p>Post code</p>
                                    </div>
                                    <div class="col-xs-1 col-sm-1 col-md-1">
                                        <p style="text-align:center">Action</p>
                                    </div>
                                </div>
                                <div class="user-store-list">
                                    @foreach ($stores as $store)
                                        <div class="col-xs-3 col-sm-3 col-md-3 no-padding-right"><input type="text" class="width-100 input-text margin-bottom" name="store_name_{{ $store->id }}" value="{{ $store->store_name }}" minlength="3" maxlength="40" required>
                                        </div>
                                        <div class="col-xs-3 col-sm-3 col-md-4 no-padding-right"><input type="text" class="width-100 input-text margin-bottom" name="store_address_{{ $store->id }}" value="{{ $store->store_address }}"
                                                   required></div>
                                        <div class="col-xs-3 col-sm-3 col-md-2 no-padding-right"><input type="text" class="width-100 input-text margin-bottom city" name="store_region_{{ $store->id }}" value="{{ $store->store_region }}"
                                                   required></div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 no-padding-right"><input type="text" class="width-100 input-text margin-bottom" name="store_zip_{{ $store->id }}" value="{{ $store->store_zip }}" required minlength="5" maxlength="8">
                                        </div>
                                        <div class="col-xs-1 col-sm-1 col-md-1"><span class="deleteclass small2" id="{{ $store->id }}"><i class="fa fa-times" title="Remove" aria-hidden="true"></i></span></div>
                                        <input type="hidden" name="store_ids[]" value="{{ $store->id }}">

                                        <div class="col-md-12 store-timing-div">

                                            <div class="col-md-4">
                                                <p>What are your store Opening time(s)?</p>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="col-md-12">
                                                    <div class="col-xs-3 col-sm-3 col-md-3"></div>
                                                    <div class="col-xs-3 col-sm-3 col-md-3" style="text-align: center;">
                                                        <p>Opening time</p>
                                                    </div>
                                                    <div class="col-xs-3 col-sm-3 col-md-3" style="text-align: center;">
                                                        <p>Closing time</p>
                                                    </div>
                                                    <div class="col-xs-3 col-sm-3 col-md-3" style="text-align: center;">
                                                        <p>Lunch break (mins)</p>
                                                    </div>
                                                </div>
                                                @foreach (get_days_list() as $day)
                                                    <div class="col-md-12">
                                                        <div class="col-xs-3 col-sm-3 col-md-3">
                                                            <p>{{ $day }}</p>
                                                        </div>
                                                        <div class="col-xs-3 col-sm-3 col-md-3">
                                                            <select name="job_start_time_{{ $store->id }}[{{ $day }}]" class="input-text width-100" id="start_time_day_{{ $day }}_{{ $store->id }}">
                                                                @for ($i = today()->setTime(0, 0); $i->lessThan(today()->setTime(24, 0)); $i->addMinutes(15))
                                                                    <option value='{{ $i->format('H:i') }}' @selected($store->get_store_start_time($day) == $i->format('H:i'))> {{ $i->format('H:i') }} </option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-3 col-sm-3 col-md-3" align="center">
                                                            <select name="job_end_time_{{ $store->id }}[{{ $day }}]" class="input-text width-100" id="end_time_day_{{ $day }}_{{ $store->id }}">
                                                                @for ($i = today()->setTime(0, 0); $i->lessThan(today()->setTime(24, 0)); $i->addMinutes(15))
                                                                    <option value='{{ $i->format('H:i') }}' @selected($store->get_store_end_time($day) == $i->format('H:i'))> {{ $i->format('H:i') }} </option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-3 col-sm-3 col-md-3">
                                                            <select name="job_lunch_time_{{ $store->id }}[{{ $day }}]" class="input-text width-100" id="lunch_time_day_{{ $day }}_{{ $store->id }}">
                                                                @for ($i = today()->setTime(0, 0); $i->lessThan(today()->setTime(1, 0)); $i->addMinutes(5))
                                                                    <option value='{{ $i->format('i') }}' @selected($store->get_store_lunch_time($day) == $i->format('i'))> {{ $i->format('i') }} </option>
                                                                @endfor
                                                                <option value='60' @selected($store->get_store_lunch_time($day) == '60')> 60 </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    @endforeach

                                </div>
                                <div class="user-store-list user-store-btn-list" style="display:flex; justify-content:center;">
                                    <div class="col-sm-6 col-md-2 no-padding-right"><button style="border-radius: 10px !important;" class="save-store-btn">Update</button></div>
                                    <div class="col-sm-6 col-md-2"><a href="javascript:void(0);" style="border-radius: 10px !important;" class="save-store-btn" id="add_store_edit">Add another store</a></div>
                                </div>
                            </form>

                            <form id="addstore" action="/employer/manage-store" method="post" class="margin-top" style="display:none;">
                                @csrf
                                <div class="col-md-12 margin-top">
                                    <h2>Add store details</h2>
                                </div>
                                <div class="col-md-12">
                                    <div class="store_block add-new-store-form-wrapp">
                                        <div class="store-details add-new-store-inner-scroll">
                                            @php
                                                $unqiue_value_here = uniqid() . time();
                                            @endphp
                                            <input type="hidden" name="total_emp_stores[]" value="{{ $unqiue_value_here }}">
                                            <div class="width-full" id="show_add_button"><a href="javascript:void(0);" class="color-blue" id="add_emp_store"><i class="fa fa-plus" aria-hidden="true" title="Add Employer store"></i></a>
                                            </div>
                                            <div class="col-xs-3 col-sm-3 col-md-3 no-padding-left"><input type="text" minlength="4" maxlength="30" name="emp_store_name_{{ $unqiue_value_here }}" required placeholder="Enter Store name"
                                                       class="input-text width-100 required-field_0"> </div>
                                            <div class="col-xs-4 col-sm-4 col-md-4 no-padding-left"><input type="text" name="emp_store_address_{{ $unqiue_value_here }}" required placeholder="Enter Store address"
                                                       class="input-text width-100 required-field_0">
                                            </div>
                                            <div class="col-xs-2 col-sm-2 col-md-2 no-padding-left"><input type="text" name="emp_store_region_{{ $unqiue_value_here }}" required placeholder="Enter Store Region"
                                                       class="input-text width-100 required-field_0 city"></div>
                                            <div class="col-xs-2 col-sm-2 col-md-2 no-padding-left"><input type="text" name="emp_store_zip_{{ $unqiue_value_here }}" required placeholder="Enter Store post code"
                                                       class="input-text width-100 required-field_0" minilength="5" maxlength="8">
                                            </div>
                                            <div class="css_error2 required-field-no_0" style="clear:both;"></div>
                                            <div class="col-md-12 store-timing-div store-opening-tdive-wrapp">
                                                <div class="add-store-scroll-wrapp">
                                                    <div class="col-md-4">
                                                        <p>What are your store Opening time(s)?</p>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="col-md-12">
                                                            <div class="col-xs-3 col-sm-3 col-md-3"></div>
                                                            <div class="col-xs-3 col-sm-3 col-md-3" align="center">
                                                                <p>Opening time</p>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-3 col-md-3" align="center">
                                                                <p>Closing time</p>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-3 col-md-3" align="center">
                                                                <p>Lunch break (mins)</p>
                                                            </div>
                                                        </div>

                                                        @foreach (get_days_list() as $day)
                                                            <div class="col-md-12">
                                                                <div class="col-xs-3 col-sm-3 col-md-3">
                                                                    <p>{{ $day }}</p>
                                                                </div>
                                                                <div class="col-xs-3 col-sm-3 col-md-3">
                                                                    <select name="job_start_time_{{ $unqiue_value_here }}[{{ $day }}]" class="input-text width-100" id="start_time_day_{{ $unqiue_value_here }}">
                                                                        @for ($i = today()->setTime(0, 0); $i->lessThan(today()->setTime(24, 0)); $i->addMinutes(15))
                                                                            <option value='{{ $i->format('H:i') }}'> {{ $i->format('H:i') }} </option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-3 col-sm-3 col-md-3" align="center">
                                                                    <select name="job_end_time_{{ $unqiue_value_here }}[{{ $day }}]" class="input-text width-100" id="end_time_day_{{ $unqiue_value_here }}">
                                                                        @for ($i = today()->setTime(0, 0); $i->lessThan(today()->setTime(24, 0)); $i->addMinutes(15))
                                                                            <option value='{{ $i->format('H:i') }}'> {{ $i->format('H:i') }} </option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-3 col-sm-3 col-md-3">
                                                                    <select name="job_lunch_time_{{ $unqiue_value_here }}[{{ $day }}]" class="input-text width-100" id="lunch_time_day_{{ $unqiue_value_here }}">
                                                                        @for ($i = today()->setTime(0, 0); $i->lessThan(today()->setTime(1, 0)); $i->addMinutes(5))
                                                                            <option value='{{ $i->format('i') }}'> {{ $i->format('i') }} </option>
                                                                        @endfor
                                                                        <option value='60'> 60 </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-offset-4 col-md-2 no-padding-left">
                                        <button style="border-radius: 10px !important;" class="save-store-btn" name="add_store">Save Store</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>

        <div id="alert-confirm-modal" class="alert-modal modal fade">
            <div class="modal-dialog">

                <div class="modal-content">
                    <div class="modal-header">

                        <h4 class="modal-title">LocumKit</h4>
                    </div>
                    <div class="modal-body">
                        <h3 id="alert-message"></h3>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="confirm">Yes</button>
                        <button type="button" class="close-alert btn btn-default">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $("#add_store_edit").click(function() {
            if ($('#addstore').css('display') == 'none') {
                $("#addstore").show(1000);
            } else {
                $("#addstore").hide(1000);
            }
        });

        var i = $(".store-details").size() + 1;
        var m = 0;
        $("#add_emp_store").click(function() {
            if (i > 1) {
                $.ajax({
                    'url': '/ajax/mutli-store-time',
                    'type': 'POST',
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    success: function(result) {
                        $('.store_block').append(
                            `
                            <div class="store-details add-new-store-inner-scroll">
                                <div class="col-xs-3 col-sm-3 col-md-3 no-padding-left">
                                    <input type="text" name="emp_store_name_${result.key}" required placeholder="Enter Store name" class="input-text width-100 required-field_${m}">
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4 no-padding-left">
                                    <input type="text" name="emp_store_address_${result.key}" required placeholder="Enter Store address" class="input-text width-100 required-field_${m}">
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2 no-padding-left">
                                    <input type="text" name="emp_store_region_${result.key}" required placeholder="Enter Store Region" class="input-text width-100 required-field_${m} city">
                                </div>
                                <div class="col-xs-2 col-sm-2 col-md-2 no-padding-left">
                                    <input type="text" name="emp_store_zip_${result.key}" required placeholder="Enter Store post code" class="input-text width-100 required-field_${m}">
                                </div>
                                <span class="removeclass small2 "><i class="fa fa-times" title="Remove" aria-hidden="true"></i></span>
                                <div class="css_error2 required-field-no_${m}" style="clear:both;"> </div>
                                ${result.html}
                            </div>
                            `
                        );
                    }
                });
                i++;
                m++;
            }
            return false;
        });

        $("body").on("click", ".removeclass", function(e) {
            if (i > 1) {
                $(this).parent('.store-details').remove();
                i--;
            }
        });

        $(".deleteclass").click(function() {
            var id = $(this).attr('id');
            $('div#alert-confirm-modal #alert-message').html('Do you really want to delete store?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#loader-div").show();
                $.ajax({
                    'url': `/ajax/employer/manage-store/${id}`,
                    'type': 'DELETE',
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    success: function(result) {
                        if (result.success) {
                            $("#loader-div").hide();
                            $('div#alert-confirm-modal').removeClass('in');
                            $('div#alert-confirm-modal').css('display', 'none');
                            messageBoxOpen("Store deleted.");
                        } else {
                            $("#loader-div").hide();
                            $('div#alert-confirm-modal').removeClass('in');
                            $('div#alert-confirm-modal').css('display', 'none');
                            messageBoxOpen(result.message, "not-reload");
                        }
                    }
                });
            });
        });
    </script>
    <script type="text/javascript">
        $(function() {
            var availableTags = @json($site_towns_available_tags);
            $(".city").autocomplete({
                source: availableTags
            });
        });
    </script>
@endpush
