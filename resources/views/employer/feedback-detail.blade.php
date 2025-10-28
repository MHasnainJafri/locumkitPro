@extends('layouts.user_profile_app')

@section('content')
    <section id="breadcrum" class="breadcrum">
        <div class="breadcrum-sitemap">
            <div class="container">
                <div class="row">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/employer/dashboard">My Dashbord</a></li>
                        <li><a href="javascript:void(0)">FEEDBACK</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="breadcrum-title">
            <div class="container">
                <div class="row">
                    <div class="set-icon registration-icon">
                        <i class="glyphicon glyphicon-star-empty" aria-hidden="true"></i>
                    </div>
                    <div class="set-title">
                        <h3>FEEDBACK INFORMATION</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="primary-content" class="main-content profiles">
        <div class="container">
            <div class="row">
                <div class="gray-gradient contents">
                    <div class="welcome-heading">
                        <h1 class="h1pad">
                            @if (count($feedbacks) > 0)
                                <div id="stars-rating" class="user-rating">
                                    <div class="div-title"><span> Average Rating </span></div>
                                    <div class="div1">
                                        <div class="star-ratings-sprite"><span style="width:{{ $overall_rating }}%" class="star-ratings-sprite-rating"></span> {{ $overall_rating }}</div>
                                    </div>
                                    <div class="div2"> Rated by <span> {{ sizeof($feedbacks) }} </span> {{ Auth::user()->user_acl_role_id == 2 ? 'employers' : 'locums' }} </div>
                                </div>
                            @else
                                <span>No feedback.</span>
                            @endif
                        </h1>
                        <hr class="shadow-line">
                    </div>
                    <div class="feedback-details-content">

                    </div>
                    <div class="feedback-details">
                        <div class="panel-group" id="accordion">
                            @foreach ($feedbacks as $feedback)
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse-{{ $loop->iteration }}">
                                                <ul>
                                                    <li>
                                                        <h6><i class="fa fa-user" aria-hidden="true"></i> {{ $feedback->freelancer->firstname . ' ' . $feedback->freelancer->lastname }} </h6>
                                                    </li>
                                                    <li>
                                                        <div id="stars-rating" class="user-rating">
                                                            @for ($i = 5; $i >= 1; $i--)
                                                                @if ($feedback->rating <= $i)
                                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                                @else
                                                                    <i class="fa fa-star-o" aria-hidden="true"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </li>
                                                </ul>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse-{{ $loop->iteration }}" class="panel-collapse collapse @if ($loop->first) in @endif">
                                        <div class="panel-body">
                                            <div class="feedback-qus-ans-details col-md-8">
                                                @php
                                                    $feedbackDetailsArray = json_decode($feedback->feedback, true);
                                                @endphp
                                                @if(isset($feedbackDetailsArray))
                                                
                                                    @foreach ($feedbackDetailsArray as $feedbackResult)
                                                        <div class="feedback-qus-ans">
                                                            <p class="qus"><span>Qus.{{ $loop->iteration }} </span> {{ $feedbackResult['qus'] }} </p>
                                                            <div class="user-rating">
                                                                @for ($i = 5; $i >= 1; $i--)
                                                                    @if ($feedbackResult['qusRate'] <= $i)
                                                                        <i class="glyphicon glyphicon-star" aria-hidden="true"></i>
                                                                    @else
                                                                        <i class="glyphicon glyphicon-star-empty" aria-hidden="true"></i>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </div>
                                            @if ($feedback->job)
                                                <div class="feedback-detail-info col-md-4">
                                                    <h4>Details</h4>
                                                    <table width="100%">
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Job Ref</td>
                                                            <td> {{ $feedback->job->id }} </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Job Date</td>
                                                            <td> {{ $feedback->job->job_date }} </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Rate</td>
                                                            <td> {{ set_amount_format($feedback->job->job_rate) }} </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Feedback left on </td>
                                                            <td> {{ $feedback->created_at->format('d-m-Y') }} </td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
