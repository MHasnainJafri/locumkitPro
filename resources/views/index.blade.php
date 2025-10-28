@extends('layouts.app')

@section('content')
    <div class="container-fluid homebanner">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-sm-7 col-xs-12 bannercont animate fadeInLeft" data-anim-type="fadeInLeft" data-anim-delay="800">

                    <div class="center">
                        <h1>Locumkit<br>
                            An empowered locum community<br>
                            <span style="color:#0000FF;"><span style="font-size:20px;">From DBS to job bookings to accountancy - we do it all</span></span>
                        </h1>
                        <p>&nbsp;</p>
                    </div>
                    <ul class="list-inline">
                        <li><a href="/locums" class="btn btn-trans">For Locums</a></li>
                        <li><a href="/employer" class="btn btn-trans">For Employers</a></li>
                    </ul>
                    <a href="/register" class="btn btn-white lkbtn btn-1"><span>Register now for FREE</span></a>
                </div>
                <div class="col-md-5 col-sm-5 col-xs-12 bannervideo animate fadeInRight" data-anim-type="fadeInRight" data-anim-delay="800">
                    <div class="inbox onclickvideo scalimg">
                        <figure>
                            <img src="/frontend/locumkit-template/new-design-assets/images/hvideoround.png" alt="Locumkit Video">
                        </figure>
                        <figcaption>
                            <a href="javascript:void(0);" id="fs-pop-video" data-src="https://www.youtube.com/embed/uM4Og3BxQm0?autoplay=1&amp;showinfo=0&amp;controls=0&amp;modestbranding=1&amp;rel=0&amp;enablejsapi=1"><i class="fa fa-play"
                                   aria-hidden="true"></i></a>
                        </figcaption>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="cmnhp hsec1 animate fadeInDown" data-anim-type="fadeInDown" data-anim-delay="800">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <h2 class="head1">Your locumkit journey</h2>
                </div>
            </div>
        </div>
    </section>
    <section class="leflow hsec2 animate fadeInDown" data-anim-type="fadeInDown" data-anim-delay="800">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-sm-12 col-xs-12 frempl">
                    <div class="inbox">
                        <h2 class="text-center">For Locum</h2>
                        <div class="flowbx">
                            <div class="col-md-3 col-sm-3 col-xs-12 cicon icon1 text-center">
                                <figure><img src="/frontend/locumkit-template/new-design-assets/images/eicon1.png" alt="Create a profile"></figure>
                                <figcaption>
                                    <h5>Create a profile</h5>
                                </figcaption>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12 cicon icon2 text-center">
                                <figcaption class="lk_hide_mob">
                                    <h5>Get Proposal</h5>
                                </figcaption>
                                <figure><img src="/frontend/locumkit-template/new-design-assets/images/eicon2.png" alt="Get Proposal"></figure>
                                <figcaption class="lk_sh_mob">
                                    <h5>Get Proposal</h5>
                                </figcaption>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12 cicon icon3 text-center">
                                <figure><img src="/frontend/locumkit-template/new-design-assets/images/eicon3.png" alt="Manage Job"></figure>
                                <figcaption>
                                    <h5>Manage Job</h5>
                                </figcaption>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12 cicon icon4 text-center">
                                <figcaption class="lk_hide_mob">
                                    <h5>Finance</h5>
                                </figcaption>
                                <figure><img src="/frontend/locumkit-template/new-design-assets/images/eicon4.png" alt="Finance"></figure>
                                <figcaption class="lk_sh_mob">
                                    <h5>Finance</h5>
                                </figcaption>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12 frlocum">
                    <div class="inbox">
                        <h2 class="text-center">For Employer</h2>
                        <div class="flowbx">
                            <div class="col-md-3 col-sm-3 col-xs-12 cicon icon1 text-center">
                                <figure><img src="/frontend/locumkit-template/new-design-assets/images/licon1.png" alt="Create a profile"></figure>
                                <figcaption>
                                    <h5>Create a profile</h5>
                                </figcaption>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12 cicon icon2 text-center">
                                <figcaption class="lk_hide_mob">
                                    <h5>Match</h5>
                                </figcaption>
                                <figure><img src="/frontend/locumkit-template/new-design-assets/images/licon2.png" alt="Match Locum"></figure>
                                <figcaption class="lk_sh_mob">
                                    <h5>Match</h5>
                                </figcaption>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12 cicon icon3 text-center">
                                <figure><img src="/frontend/locumkit-template/new-design-assets/images/licon3.png" alt="Engage Locum"></figure>
                                <figcaption>
                                    <h5>Engage</h5>
                                </figcaption>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12 cicon icon4 text-center">
                                <figcaption class="lk_hide_mob">
                                    <h5>Hire</h5>
                                </figcaption>
                                <figure><img src="/frontend/locumkit-template/new-design-assets/images/licon4.png" alt="Hire Locum"></figure>
                                <figcaption class="lk_sh_mob">
                                    <h5>Hire</h5>
                                </figcaption>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <section class="cmnhp hsec3 animate fadeInDown" data-anim-type="fadeInDown" data-anim-delay="800">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <h2 class="head1">A sample of jobs available</h2>
                </div>
            </div>
        </div>
    </section>
    <section class="jobtbl hsec4">
        <div class="container">
            <div class="row animate fadeInDown" data-anim-type="fadeInDown" data-anim-delay="800">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">

                        <p style="text-align: center;"><span style="color:#000000;">Being a platform built for locums by locums, we are trying to ensure you locums get the best remuneration as possible and hence do not take a single penny
                                as agency fee, hoping the saving can be passed onto you.</span></p>
                        <p style="text-align: center;">&nbsp;</p>
                        <table class="table-hover table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th style="text-align: left;">Store</th>
                                    <th style="text-align: left;">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>12 April</td>
                                    <td style="text-align: left;">Multiple</td>
                                    <td style="text-align: left;">Ryde</td>
                                </tr>
                                <tr>
                                    <td>Multiple dates</td>
                                    <td style="text-align: left;">Independent</td>
                                    <td style="text-align: left;">Colchester</td>
                                </tr>
                                <tr>
                                    <td>Multiple dates</td>
                                    <td style="text-align: left;">Multiple</td>
                                    <td style="text-align: left;">York</td>
                                </tr>
                                <tr>
                                    <td>Multiple dates</td>
                                    <td style="text-align: left;">Mutliple</td>
                                    <td style="text-align: left;">Croydon</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row animate fadeInUp" data-anim-type="fadeInUp" data-anim-delay="800">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <a href="/register" class="btn btn-default lkbtn btn-1"><span>Register to Apply</span></a>
                </div>
            </div>
        </div>
    </section>
    <section class="cmnhp hsec5 animate fadeInDown" data-anim-type="fadeInDown" data-anim-delay="800">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <h2 class="head1">Some of our partners</h2>
                </div>
            </div>
            <div class="row clintlogo">
                <div class="col-md-2 col-sm-3 col-xs-12 text-center">
                    <figure><img src="/frontend/locumkit-template/new-design-assets/images/client-logo/Boots.jpg" alt="Client Boots" width="100%"></figure>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 text-center">
                    <figure><img src="/frontend/locumkit-template/new-design-assets/images/client-logo/Optical-Express.jpg" alt="Client Optical Express" width="100%"></figure>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 text-center">
                    <figure><img src="/frontend/locumkit-template/new-design-assets/images/client-logo/Scrivens.jpg" alt="Client Scrivens" width="100%"></figure>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 text-center">
                    <figure><img src="/frontend/locumkit-template/new-design-assets/images/client-logo/Specsavers.jpg" alt="Client Specsavers" width="100%"></figure>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 text-center">
                    <figure><img src="/frontend/locumkit-template/new-design-assets/images/client-logo/VE.jpg" alt="Client VE" width="100%"></figure>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 text-center">
                    <figure><img src="/frontend/locumkit-template/new-design-assets/images/costco.png" alt="Client Logo" width="100%"></figure>
                </div>
            </div>
        </div>
    </section>
@endsection
