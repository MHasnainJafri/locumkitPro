@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        @auth
                            <li><a href="/{{ $user_type }}/dashboard">Dashbord</a></li>
                            <li><a href="/{{ $user_type }}/job-listing">Job List</a></li>
                        @endauth
                        <li><a href="javascript:void(0);">Cancel Job</a></li>
                    </ul>

                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon" style="padding: 8px 13px 0px;">
                        <i class="glyphicon glyphicon-briefcase" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Cancel Job</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <form id="cancel-job" name="cancel-job" action="{{ $form_post_action }}" method="post" onsubmit="return validateForm()">
                        @csrf
                        @auth
                            <h3 style="text-align: left;">Cancellation rate prior to cancelling current job : {{ $user_cancellation_rate }}%</h3>
                        @endauth
                        <p>Why do you want to cancel the job ? Please specify the reason below...</p>

                        <div class="cancel-reason-div">
                            <textarea name="cancel-reason" rows="5" id="cancel-reason" placeholder="Please enter reason up to 128 character only...."></textarea>
                            <div id="error"></div>
                        </div>
                        <input type="submit" value="SUBMIT" class="cancel-submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        var max = 128;
        jQuery("#cancel-reason").keypress(function(e) {
            if (e.which < 0x20) {
                return;
            }
            if (this.value.length == max) {
                e.preventDefault();
            } else if (this.value.length > max) {
                this.value = this.value.substring(0, max);
            }
        });

        function validateForm() {
            var el = jQuery("#cancel-reason").val();
            if (el == '') {
                jQuery("#error").html("<p style='color:red;'>This field is required</p>");
                return false;
            } else {
                jQuery("#error").html('');
                confirm_delete();
            }
        }

        function confirm_delete() {
            event.preventDefault();
            $('div#alert-confirm-modal #alert-message').html('Are you sure you want to cancel job?');
            $('div#alert-confirm-modal').addClass('in');
            $('div#alert-confirm-modal').css('display', 'block');
            $('div#alert-confirm-modal #confirm').click(function() {
                $("#cancel-job").submit();
                messageBoxClose();
            });
        }
    </script>
@endpush
