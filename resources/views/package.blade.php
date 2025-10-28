@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="#">Compare All</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section id="services" class="service-blocks">
        <div class="container">
            <div class="row">
                <div class="package-bg white-bg contents">
                    <div class="package-table-wrapp">
                        <table class="table-bordered table-hover package-table table">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="text-center" style="line-height: 190px;">
                                            <img src="/frontend/locumkit-template/img/logo.png" alt="Locumkit Package Services" title="Locumkit Package Services" width="150px">
                                        </div>
                                    </th>
                                    <th>
                                        <div class="price-info gradient-cricle-bronze text-center">
                                            <h3>{{ $paid_packages['bronze_price'] }}</h3>
                                        </div>
                                        <div class="pkg-name text-center">
                                            <h3>Bronze</h3>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="price-info gradient-cricle-silver text-center">
                                            <h3>{{ $paid_packages['silver_price'] }}</h3>
                                        </div>
                                        <div class="pkg-name text-center">
                                            <h3>Silver</h3>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="price-info gradient-cricle-gold text-center">
                                            <h3> {{ $paid_packages['gold_price'] }} </h3>
                                        </div>
                                        <div class="pkg-name text-center">
                                            <h3>Gold</h3>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages_info as $package)
                                    <tr>
                                        <td>{{ $package->label }}</td>
                                        @if ($package->bronze)
                                            <td class="text-center"><i class='fa fa-check' aria-hidden='true'></i></td>
                                        @else
                                            <td class="text-center"><i class='fa fa-times' aria-hidden='true'></i></td>
                                        @endif
                                        @if ($package->silver)
                                            <td class="text-center"><i class='fa fa-check' aria-hidden='true'></i></td>
                                        @else
                                            <td class="text-center"><i class='fa fa-times' aria-hidden='true'></i></td>
                                        @endif
                                        @if ($package->gold)
                                            <td class="text-center"><i class='fa fa-check' aria-hidden='true'></i></td>
                                        @else
                                            <td class="text-center"><i class='fa fa-times' aria-hidden='true'></i></td>
                                        @endif
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
