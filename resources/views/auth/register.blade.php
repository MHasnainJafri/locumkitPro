@extends('layouts.app')
@push('styles')
    <style>
        select.input-text.width-100 {
            color: black !important;
        }

        .form-control {
            color: black !important;
        }
        .formlft{
            padding: 5px 5px !important;
        }
        .lkbtn-1 {
            margin:5px !important;
        }
        .col-md-12.btn-img.paypalbtnn {
    text-align: center;
    margin-bottom: 100px;
    display: flex !important;
    justify-content: center !important;
    gap: 25px !important;
}
@media(max-width:600px){
    .col-md-12.btn-img.paypalbtnn{
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
}
        
    </style>
@endpush
@section('content')
    <section class="innerhead">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 bannercont animate zoomIn text-center" data-anim-type="zoomIn" data-anim-delay="300">
                        <div class="center">
                            <h1>Register</h1>
                        </div>
                        <div class="breadcrum-sitemap">
                            <ul>
                                <li><a href="/">Home</a></li>
                                <li><a href="javascript:void(0);">Register</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="innerlayout" id="main-react-root">

    </section>
@endsection

@push('scripts')
    <script>
        const questions = @json($questions);
        const roles = @json($roles);
        const professions = @json($professions);
        const GOOGLE_RECAPTCHA_SITE_KEY = `{{ config('app.google_recaptcha_site_key') }}`;
        const AVAILABLE_TAGS = @json($site_towns_available_tags);
        const REGISTRATION_VALIDATION_URI = `{{ url('/ajax/registration-info-check') }}`;
        const CSRF_TOKEN = `{{ csrf_token() }}`;
        const REGISTER_FORM_URI = `{{ url('/register') }}`;
        const ERROR_MESSAGES_BAG = @json($errors->jsonSerialize());
    </script>

    <script>
        function save_list() {
            var store_list = $("input[name*=store_list]:checked");
            let site_town_ids = [];
            store_list.each(function() {
                site_town_ids.push($(this).val());
            });
            localStorage.setItem("site_town_ids", JSON.stringify(site_town_ids));

            $("#getlist-section").hide(1000);
            $('.modal-backdrop').hide(1000);
        }
    </script>

    <script src="{{ asset('build/register.js') }}"></script>
@endpush
