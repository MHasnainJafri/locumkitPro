@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/login">My Dashboard</a></li>
                        <li><a href="javascript:void(0)"> Account Upgrade </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon packging-info" style="padding: 8px 6px 0px 3px">
                        <i class="fa fa-gbp" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Account Upgrade</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content profiles">
        <div class="container">
            <div class="row">
                <div class="gray-gradient contents">
                    <form method="post">
                        @csrf
                        <div class="package-upgrade-form">
                            <div class="col-md-12">
                                <section id="packages" class="package">
                                    <div class="row">
                                        <div class="package-block">
                                            @foreach ($user_packages as $user_package)
                                                <div class="col-sm-4 col-md-4 package-price-box" id="package-{{ $user_package->id }}">
                                                    <div class="set-pack-icon">
                                                        <div class="set-pack-price">
                                                            <div class="gradient-cricle-{{ $user_package->name }}">
                                                                <span> {{ set_amount_format($user_package->price) }} </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="set-pack-content">
                                                        <h2>{{ $user_package->name }}</h2>
                                                        <p>{{ $user_package->price }}</p>
                                                    </div>
                                                    <div class="set-pack-link">
                                                        <a class="read-common-btn" href="javascript:void(0);" onClick="open_benifits({{ $user_package->id }})" id="pack-{{ strtolower($user_package->name) }}">Select</a>
                                                        <input type="hidden" name="{{ strtolower($user_package->name) }}" class="pkg_price" id="{{ strtolower($user_package->name) }}" value="{{ $user_package->price }}">
                                                        <input type="hidden" name="{{ strtolower($user_package->name) }}_id" class="pkg_id" id="{{ strtolower($user_package->name) }}_id" value="{{ $user_package->id }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </section>
                                <div class="css_error" id="package_error"></div>
                            </div>

                            <div class="row">
                                <input type="hidden" name="package-final" id="package-final">
                                <input type="hidden" name="package_id" id="package_id">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div id="benifits-section"></div>
                                </div>
                            </div>
                            <div class="upgrade-btn">
                                <div id="paypal-upgrade-form"></div>
                                <div class="alert alert-danger" id="pkg-error" style="display:none;"> Please select package. </div>
                                <button type="submit" class="btn btn-primary">
                                    Change Package
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var currentPkg = parseInt(`{{ $pre_package_id }}`);
            open_benifits(currentPkg);
            var pkgPrice = $('.active-package .set-pack-link .pkg_price').val();
            $("#package-final").val(pkgPrice);
            $("#package_id").val(currentPkg);
        });

        function open_benifits(id) {
            var str = id;
            if (id == 1) {
                $("#package-1").addClass("active-package");
                $("#package-2").removeClass("active-package");
                $("#package-3").removeClass("active-package");
                $("#package-4").removeClass("active-package");
            } else if (id == 2) {
                $("#package-2").addClass("active-package");
                $("#package-3").removeClass("active-package");
                $("#package-1").removeClass("active-package");
                $("#package-4").removeClass("active-package");
            } else if (id == 3) {
                $("#package-3").addClass("active-package");
                $("#package-2").removeClass("active-package");
                $("#package-1").removeClass("active-package");
                $("#package-4").removeClass("active-package");

            } else {
                $("#package-3").removeClass("active-package");
                $("#package-2").removeClass("active-package");
                $("#package-1").removeClass("active-package");
                $("#package-4").addClass("active-package");
            }
            var pkgPrice = $('.active-package .set-pack-link .pkg_price').val();
            $("#package-final").val(pkgPrice);
            $("#package_id").val(id);
            if (str != "") {
                $.ajax({
                    'url': '/ajax/open-benefits-form',
                    'type': 'POST',
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    'data': {
                        'pack_id': str
                    },
                    'success': function(result) {
                        console.log(result);
                        if (result && result.html) {
                            $("#benifits-section").html(result.html);
                        } else {
                            $("#benifits-section").html("");
                            $("#package-" + id).removeClass("active-package");
                        }
                    }
                });
            }
        }
    </script>
@endpush
