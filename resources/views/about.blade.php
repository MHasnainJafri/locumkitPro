@extends('layouts.app')

@section('content')
    <section class="innerhead">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 bannercont animate text-center" data-anim-type="zoomIn" data-anim-delay="800">

                        <div class="center">
                            <h1>About Us</h1>
                        </div>
                        <div class="breadcrum-sitemap">
                            <ul>
                                <li><a href="{{ url('/') }}">Home</a></li>
                                <li><a href="javascript:void(0);">ABOUT US</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="innerlayout">
        <div class="container">
            <div class="row bgwhite inbox about-page">

                <div id="primary-content" class="main-content about">
                    <div class="container">
                        <div class="row">
                            <div class="white-bg contents">
                                <section class="about_section">
                                    <div class="welcome-heading">

                                    </div>
                                    <div class="about_para animate fadeInDown">
                                        <p><span style="font-size:14px;"><span style="font-family:trebuchet ms,helvetica,sans-serif;">Here at LocumKit we provide an innovative locum experience tailored to you. With instant job notifications and
                                                    24/7 diary management, our smooth and streamlined system allows you to focus on your daily business, whilst&nbsp;we take care of everything else. With a large community of both Locums and
                                                    Employers, we are the only locum resource you will ever need. Designed to suit you, our services can be used online via our website or our app, ensuring complete control of your locum
                                                    needs.</span></span></p>
                                        <p><span style="font-size:14px;"><span style="font-family:trebuchet ms,helvetica,sans-serif;">We saw a need to rethink the current outdated process of Locum recruitment and decided to build a bespoke
                                                    recruitment platform. Using complex, customised algorithms our platform intelligently matches jobs to locums that specifically meet certain job criteria. The system has been extensively
                                                    designed and tested by a team of people, who have an in-depth knowledge of the locum market.</span></span></p>
                                    </div>
                                    <div class="mission-vision">
                                        <div class="col-md-6 col-sm-6 about_ourvision animate fadeInLeft">
                                            <h3>OUR VISION</h3>
                                            <p><span style="font-family:trebuchet ms,helvetica,sans-serif;"><span style="font-size:14px;">&quot;An empowered locum community&quot;</span></span></p>
                                            <p><span style="font-family:trebuchet ms,helvetica,sans-serif;"><span style="font-size:14px;">A service designed to benefit all those in the locum industry.</span></span></p>
                                            <p><span style="font-family:trebuchet ms,helvetica,sans-serif;"><span style="font-size:14px;">Bringing locums and employers closer together.</span></span></p>
                                            <p><span style="font-family:trebuchet ms,helvetica,sans-serif;"><span style="font-size:14px;">Removing barriers to success bringing increased patient satisfaction.</span></span></p>
                                        </div>
                                        <div class="col-md-6 col-sm-6 about_ourmission animate fadeInRight">
                                            <h3>OUR MISSION</h3>
                                            <p><span style="font-size: 14px;"><span style="font-family: &quot;trebuchet ms&quot;, helvetica, sans-serif;">Provide more control to you, the locum and employer.</span></span></p>
                                            <p><span style="font-size: 14px;"><span style="font-family: &quot;trebuchet ms&quot;, helvetica, sans-serif;">Complete transparency between employer and locum - no need for an
                                                        intermediary.</span></span></p>
                                            <p><span style="font-size: 14px;"><span style="font-family: &quot;trebuchet ms&quot;, helvetica, sans-serif;">Real time information with 24/7 access - a booking system that works around the
                                                        clock.</span></span></p>
                                            <p><span style="font-size: 14px;"><span style="font-family: &quot;trebuchet ms&quot;, helvetica, sans-serif;">Exceed our users&#39; expectations with innovative and bespoke services, built on a wealth
                                                        of experience gained from within the sectors.</span></span></p>
                                            <p><span style="font-size: 14px;"><span style="font-family: &quot;trebuchet ms&quot;, helvetica, sans-serif;">Provide exemplary, local, cost efficient, friendly accountancy and recruitment
                                                        services.</span></span></p>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
