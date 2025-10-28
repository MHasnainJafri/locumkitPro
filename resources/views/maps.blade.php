@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="javascript:void(0);">Locumkit Maps</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg Locumkit-Maps contents">
                    <section>
                        <div class="welcome-heading">
                            <h1><span>Locumkit Maps</span></h1>
                            <hr class="shadow-line">
                        </div>
                        <div id="map-container">
                            <div class="col-md-5"><img alt="" src="/media/ckeditor-file/UK-map-437.png" style="width: 100%"></div>
                            <div class="col-md-7"><img alt="" src="/media/ckeditor-file/PCT london by map.jpg" style="width: 100%; "></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mapImageModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <div class="modal-body" id="map-img-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
