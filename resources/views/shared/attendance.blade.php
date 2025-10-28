@extends('layouts.user_profile_app')

@section('content')
    <div id="primary-content" class="main-content about">
        <div class="container">
            <div class="row">
                <div class="white-bg contents">
                    <section>
                        <p style="text-align:center;">
                            <img src="{{ asset('/frontend/locumkit-template/img/logo.png') }}" alt="logo">
                        </p>
                        @if ($check_job_status == 1)
                            <h1 class="successfull_msg">Attendance confirmed</h1>
                        @elseif ($check_job_status == 2)
                            <h1 class="successfull_msg">Thanks...Have a nice time..</h1>
                        @elseif ($check_job_status == 3)
                            <h1 class="error_msg">Offfsss...! Please inform employer about the reason.</h1>
                        @elseif ($check_job_status == 4)
                            <h1 class="error_msg">Offfsss...! Please ask locum about the reason.</h1>
                        @elseif ($check_job_status == 6)
                            <h1 class="error_msg">Attendance is already done.</h1>
                        @elseif ($check_job_status == 8)
                            <h1 class="error_msg">Job is already cancelled.</h1>
                        @elseif ($check_job_status == 9)
                            <h1 class="error_msg">Job is deleted.</h1>
                        @else
                            @if ($job_on_day && today()->equalTo($job_on_day->job_date) && $job_on_day->status > 0)
                                <h1 class="error_msg">Attendance is already done.</h1>
                            @else
                                <h1 class="error_msg">You can not able attend this job today.</h1>
                            @endif
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
