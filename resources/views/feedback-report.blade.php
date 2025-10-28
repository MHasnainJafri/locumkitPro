@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/{{ $for_user_role }}/dashboard">My dashboard</a></li>
                        <li><a href="#">Feedback statistic</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-gbp" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>Feedback statistic</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg thank-you-page contents">
                    <section class="text-left">
                        <div class="col-md-12 pad0">
                            <div class="finance-page-head text-center" style="margin: 20px 0 15px;">Feedback statistic for user id {{ $user_id }} </div>

                            @if (empty($feedbacks))
                                <div class="col-md-12 text-center">
                                    <h3 class="text-uppercase color-blue">No records of feedback </h3>
                                </div>
                            @endif
                        </div>


                        <div class="col-md-12 pad0 mart30">
                            <div class="fb-sc">
                                <div class="col-md-12 pad0 feebb-tobchat">
                                    <div class="col-md-8 feebavg">
                                        <div id="feedmap">
                                            <canvas id="myChart" class="well"></canvas>
                                            <div id="myChart-legend" class="chart-legend"></div>
                                        </div>
                                    </div>

                                    @php
                                        $i = 1;
                                        $c = count($qusdata);
                                    @endphp

                                    @foreach ($qusdata as $key => $qusdata)
                                        @php
                                            $quedata = 'Q' . $i . ' : ' . $qus[$key];
                                            $dataX[] = 'Q' . $i;
                                            $dataper = round(($qusdata / ($quscount[$key] * 5)) * 100, 2);
                                            $dataY[] = $dataper;
                                        @endphp
                                        @if ($c >= 4)
                                            @php $j = 4; @endphp
                                        @elseif ($c == 2)
                                            @php $j = 1; @endphp
                                        @else
                                            @php $j = $c; @endphp
                                        @endif
                                        <div class="feedback-qustm">
                                            <div class="col-md-8 feedback-qus">
                                                <div class="feebk-list" id="feed1">
                                                    <div class="arrow-right"></div>
                                                    <ul>
                                                        <li> {{ $quedata }} </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            @if ($i == $j && $j == 3)
                                                <div class="col-md-4"> </div>
                                            @endif
                                            @php
                                                $i++;
                                            @endphp
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-md-12 job-details" style="text-align: center;">
                    <div class="job-edit-btn"><input type="button" onclick="goBack()" class="invite-user-btn" value="Back to previous page"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/frontend/locumkit-template/js/Chart.js"></script>
    @if (sizeof($feedbacks) > 0)
        <script>
            $(document).ready(function() {
                var data_x = @json($dataX);
                var data3 = @json($dataY);

                var data = {
                    labels: data_x,
                    datasets: [{
                        label: "Average rating percentage",
                        fillColor: "rgba(36, 169, 224, 0.8)",
                        strokeColor: "#24a9e0",
                        pointColor: "#fc9b29",
                        pointStrokeColor: "#fff",
                        data: data3
                    }]
                };
                var options = {
                    animation: true,
                    tooltipTemplate: "<%= label %> : <%= value %> %",
                    barValueSpacing: 10,
                    scaleLabel: function(label) {
                        return label.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '%';
                    },
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            steps: 10,
                            stepValue: 5,
                            max: 100
                        }
                    }]
                };

                var c = $('#myChart');
                var ct = c.get(0).getContext('2d');
                var ctx = document.getElementById("myChart").getContext("2d");
                var myChart = new Chart(ctx).Bar(data, options);
            });
        </script>
    @endif
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
@endpush
