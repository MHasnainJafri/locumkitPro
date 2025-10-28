@extends('layouts.user_profile_app')

@push('styles')
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .none-shadow {
            box-shadow: none !important;
        }

        .title {
            font-size: 40px;
            animation: sub 1s ease-out 1 both;
        }

        .row {
            margin: 0 !important
        }

        .w-100 {
            width: 100%;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .w-75 {
            width: 75%
        }

        .w-25 {
            width: 25%
        }

        .w-50 {
            width: 50%
        }

        .account-custom {
            font-size: 1.6rem;
            line-height: 2.4rem
        }

        @keyframes sub {
            from {
                opacity: 0;
                transform: perspective(500px) translate3d(-30%, 0, 0);
            }

            to {
                opacity: 1;
                transform: perspective(500px) translate3d(0, 0, 0);
            }
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .img-area {
            background: #f4f6f8;
            padding: 8vw 12vw;
            height: 36vw;
        }

        .img-area img {
            width: 16%;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            cursor: pointer;
            transition: transform 1s;
            border-radius: 4px;
        }

        .btn-area0 img {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        h2.head1 {
            position: relative;
            padding-bottom: 10px;
        }

        h2.head1:after {
            position: absolute;
            content: "";
            width: 65px;
            height: 2px;
            background: #00a8dd;
            bottom: 0;
            left: 0;
            right: 0;
            margin: 0 auto;
        }

        .area2-img2 {
            position: absolute;
            right: 10%;
            height: 90%;
            top: 5%;
            box-shadow: none !important;
            transform: rotate(10deg);
        }

        .area2-img1 {
            position: absolute;
            width: 30%;
            bottom: -10%;
            left: 50%;
            transform: rotate(-10deg);
        }

        .area3-2 {
            width: 24%;
            position: absolute;
            top: 24%;
            border-radius: 24px;
        }

        @media (max-width: 1200px) {
            .area3-2 {
                top: 16%
            }
        }

        .area3-1 {
            width: 80%;
            margin-left: 20%;
        }

        .area5 {
            transform: rotate(-30deg);
            width: 120px;
            margin-left: calc(50% - 60px);
            box-shadow: none !important;
        }

        .mb--10 {
            margin-bottom: -10%;
        }

        .main-bg {
            width: 20% !important;
            position: absolute;
            left: 40%;
            margin-top: -4%;
        }

        .fst-bg {
            margin-top: -6%;
            box-shadow: none !important;
        }

        .float-left.fst-bg {
            transform: rotate(-10deg);
        }

        .float-right.fst-bg {
            transform: rotate(10deg);
            margin-right: -42%;
        }

        .snd-bg {
            width: 22% !important;
            transform: rotate(0deg);
        }

        .intro-area img {
            width: 60%;
        }

        .intro-area hr {
            height: 1px;
            background: #00c853;
            margin: 2% 10% 5%
        }

        .trd-bg {
            width: 19% !important;
            margin-top: -2%;
        }

        .float-left.trd-bg {
            margin-left: -6%;
            transform: rotate(10deg);
        }

        .float-right.trd-bg {
            margin-right: -34%;
            transform: rotate(-5deg);
        }

        .float-left.snd-bg {
            margin-left: -8%;
            margin-top: 2%;
        }

        .float-right.snd-bg {
            margin-right: 18%;
        }

        .btn-area0 button {
            background: #00a8dd;
            height: 50px;
            font-size: 20px;
            color: #fff;
            -webkit-transition: all 0.5s;
            -moz-transition: all 0.5s;
            -o-transition: all 0.5s;
            transition: all 0.5s;
            position: relative;
            perspective: 2000px;
        }

        .btn-area0 button:hover {
            color: black;
            background: white;
        }

        .btn-area0 button:hover::after {
            background-color: rgba(33, 150, 243, 0.25);
        }

        .btn-area0 button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            -webkit-transition: all 0.5s;
            -moz-transition: all 0.5s;
            -o-transition: all 0.5s;
            transition: all 0.5s;
            border: 1px solid rgba(33, 150, 243, 0.5);
        }

        .btn-area0 button::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            -webkit-transition: all 0.5s;
            -moz-transition: all 0.5s;
            -o-transition: all 0.5s;
            transition: all 0.5s;
            border: 1px solid rgba(33, 150, 243, 0.5);
        }

        .text-white {
            color: white
        }

        .btn-area0 button:hover::before {
            -webkit-transform: rotateX(60deg) translate(0px, 40px);
            transform: rotateX(60deg) translate(0px, 40px);
            background-color: rgba(33, 150, 243, 0.25);
        }

        .btn-area0 button:focus {
            outline: none !important;
        }

        .btn-area0 button:hover::after {
            -webkit-transform: rotateX(-60deg) translate(0px, -40px);
            transform: rotateX(-60deg) translate(0px, -40px);
            background-color: rgba(33, 150, 243, 0.25);
        }

        .mb-5 {
            margin-bottom: 3rem !important;
        }

        .px-5 {
            padding-right: 3rem !important;
            padding-left: 3rem !important;
        }

        .my-4 {
            margin-bottom: 1.5rem !important;
        }

        .my-2 {
            margin-bottom: .5rem !important;
            margin-top: .5rem !important;
        }

        .mt-2 {
            margin-top: .5rem !important;
        }

        .mb-2 {
            margin-bottom: .5rem !important;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .my-5 {
            margin-bottom: 3rem !important;
            margin-top: 3rem !important;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .banner {
            background: url("/frontend/locumkit-template/new-design-assets/images/account/banner1.jpg");
            padding: 20rem 2rem;
        }

        .intro-area ul {
            list-style: none;
            font-size: 16px;
        }

        .intro-area ul li:before {
            content: '';
            padding: 6px 0 0 24px;
            margin-left: -24px;
            background: url("/frontend/locumkit-template/new-design-assets/images/account/check.png") no-repeat 0 7px;
        }

        .check {
            width: 24px;
            margin-right: 16px;
        }

        .section-title {
            margin-bottom: -36px
        }

        @keyframes animationFramesOne {
            0% {
                transform: translate(0, 0) rotate(0deg)
            }

            20% {
                transform: translate(73px, -1px) rotate(36deg)
            }

            40% {
                transform: translate(141px, 72px) rotate(72deg)
            }

            60% {
                transform: translate(83px, 122px) rotate(108deg)
            }

            80% {
                transform: translate(-40px, 72px) rotate(144deg)
            }

            100% {
                transform: translate(0, 0) rotate(0deg)
            }
        }

        @-webkit-keyframes animationFramesOne {
            0% {
                -webkit-transform: translate(0, 0) rotate(0deg)
            }

            20% {
                -webkit-transform: translate(73px, -1px) rotate(36deg)
            }

            40% {
                -webkit-transform: translate(141px, 72px) rotate(72deg)
            }

            60% {
                -webkit-transform: translate(83px, 122px) rotate(108deg)
            }

            80% {
                -webkit-transform: translate(-40px, 72px) rotate(144deg)
            }

            100% {
                -webkit-transform: translate(0, 0) rotate(0deg)
            }
        }

        @keyframes animationFramesThree {
            0% {
                transform: translate(165px, -179px)
            }

            100% {
                transform: translate(-346px, 617px)
            }
        }

        @-webkit-keyframes animationFramesThree {
            0% {
                -webkit-transform: translate(165px, -179px)
            }

            100% {
                -webkit-transform: translate(-346px, 617px)
            }
        }

        @-webkit-keyframes scale-up-three {
            0% {
                -webkit-transform: scale(1);
                transform: scale(1)
            }

            40% {
                -webkit-transform: scale(.4);
                transform: scale(.4)
            }

            100% {
                -webkit-transform: scale(1);
                transform: scale(1)
            }
        }

        @keyframes scale-up-three {
            0% {
                -webkit-transform: scale(1);
                transform: scale(1)
            }

            40% {
                -webkit-transform: scale(.4);
                transform: scale(.4)
            }

            100% {
                -webkit-transform: scale(1);
                transform: scale(1)
            }
        }

        .working-process {
            background: linear-gradient(287deg, #2196f3, #40c4ff);
            padding-top: 10px;
            padding-bottom: 0px;
            position: relative;
            z-index: 10
        }

        .working-process::before {
            position: absolute;
            content: '';
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            background-image: url("/frontend/locumkit-template/new-design-assets/images/account/working-line.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
            opacity: .07
        }

        .working-process .section-title .title {
            color: #fff
        }

        .working-process .section-title .title span {
            color: #fff
        }

        .working-process .section-title p {
            color: #fff
        }

        .working-process .working-item {
            position: relative
        }

        .working-process .working-item i {
            height: 120px;
            width: 120px;
            border-radius: 50%;
            text-align: center;
            line-height: 120px;
            border: 2px dashed #fff;
            color: #fff;
            font-size: 50px
        }

        .working-process .working-item .title {
            color: #fff;
            font-size: 24px;
            font-weight: 600;
            padding-top: 25px
        }

        .working-process .working-item p {
            color: #fff;
            font-size: 14px;
            line-height: 28px;
            padding-top: 7px
        }

        .working-process .working-item.item-1 {
            margin-top: 80px
        }

        @media only screen and (min-width:768px) and (max-width:991px) {
            .working-process .working-item.item-1 {
                margin-top: 30px
            }
        }

        @media(max-width:767px) {
            .working-process .working-item.item-1 {
                margin-top: 30px
            }
        }

        .working-process .working-item.item-2 {
            margin-top: 120px
        }

        @media only screen and (min-width:768px) and (max-width:991px) {
            .working-process .working-item.item-2 {
                margin-top: 30px
            }
        }

        @media(max-width:767px) {
            .working-process .working-item.item-2 {
                margin-top: 30px
            }
        }

        .working-process .working-item.item-3 {
            margin-top: 30px
        }

        @media only screen and (min-width:768px) and (max-width:991px) {
            .working-process .working-item.item-3 {
                margin-top: 30px
            }
        }

        @media(max-width:767px) {
            .working-process .working-item.item-3 {
                margin-top: 30px
            }
        }

        .working-process .working-item.item-4 {
            margin-top: 56px
        }

        @media only screen and (min-width:768px) and (max-width:991px) {
            .working-process .working-item.item-4 {
                margin-top: 30px
            }
        }

        @media(max-width:767px) {
            .working-process .working-item.item-4 {
                margin-top: 30px
            }
        }

        .working-process .working-item .dot-1 {
            position: absolute;
            right: -105px;
            top: 0;
            transform: rotate(-20deg)
        }

        @media only screen and (min-width:768px) and (max-width:991px) {
            .working-process .working-item .dot-1 {
                display: none
            }
        }

        @media(max-width:767px) {
            .working-process .working-item .dot-1 {
                display: none
            }
        }

        .working-process .working-item .dot-2 {
            position: absolute;
            right: -100px;
            top: -72px;
            transform: rotate(12deg)
        }

        @media only screen and (min-width:768px) and (max-width:991px) {
            .working-process .working-item .dot-2 {
                display: none
            }
        }

        @media(max-width:767px) {
            .working-process .working-item .dot-2 {
                display: none
            }
        }

        .working-process .working-item .dot-3 {
            position: absolute;
            top: -15px;
            left: -96px;
            transform: rotate(-18deg)
        }

        @media only screen and (min-width:768px) and (max-width:991px) {
            .working-process .working-item .dot-3 {
                display: none
            }
        }

        @media(max-width:767px) {
            .working-process .working-item .dot-3 {
                display: none
            }
        }

        .working-process .shape-1 {
            position: absolute;
            top: 75px;
            left: 100px;
            height: 60px;
            width: 60px;
            border-radius: 50%;
            background: #fff;
            animation: scale-up-three linear 15s infinite
        }

        @media(max-width:767px) {
            .working-process .shape-1 {
                left: 20px;
                top: 30px
            }
        }

        .working-process .shape-2 {
            position: absolute;
            bottom: 50px;
            right: 100px;
            height: 60px;
            width: 60px;
            border-radius: 50%;
            border: 5px solid #fff;
            animation: rotatey linear 15s infinite
        }

        @media(max-width:767px) {
            .working-process .shape-2 {
                right: 30px;
                bottom: 30px
            }
        }

        .working-process .shape-3 {
            position: absolute;
            top: 150px;
            right: 50px;
            height: 5px;
            width: 60px;
            background: #fff;
            transform: rotate(-18deg);
            animation: rotated linear 30s infinite
        }

        @media(max-width:767px) {
            .working-process .shape-3 {
                right: 20px;
                top: 50px
            }
        }

        .working-process .shape-3::before {
            position: absolute;
            content: '';
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            height: 60px;
            width: 5px;
            background: #fff
        }

        .working-process .shape-4 {
            position: absolute;
            bottom: 75px;
            left: 150px;
            height: 5px;
            width: 50px;
            background: #fff;
            transform: rotate(-18deg);
            animation: rotated linear 30s infinite;
            opacity: .7
        }

        @media(max-width:767px) {
            .working-process .shape-4 {
                left: 20px;
                bottom: 50px
            }
        }

        .working-process .shape-4::before {
            position: absolute;
            content: '';
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            height: 50px;
            width: 5px;
            background: #fff;
            opacity: .7
        }

        @keyframes rotatey {
            0% {
                transform: rotateY(-360deg) rotateX(-90deg)
            }

            100% {
                transform: rotateY(360deg) rotateX(90deg)
            }
        }

        @-webkit-keyframes rotatey {
            0% {
                transform: rotateY(-360deg) rotateX(-90deg)
            }

            100% {
                transform: rotateY(360deg) rotateX(90deg)
            }
        }

        @keyframes rotated {
            0% {
                transform: rotate(-360deg)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        @-webkit-keyframes rotated {
            0% {
                transform: rotate(-360deg)
            }

            100% {
                transform: rotate(360deg)
            }
        }
    </style>
@endpush

@section('content')
    <section id="breadcrum" class="breadcrum">
    <div class="breadcrum-sitemap">
         <div class="container">
            <div class="row">
                <ul>          
                    <li><a href="{{route('index')}}">Home</a></li>
		    <li><a href="javascript:void(0);">Registration Guide</a></li>
                </ul>
             </div>
         </div>
     </div>
</section>
<div id="primary-content" class="main-content about">
   <div class="container">
    	<div class="row">
            <div class="contents white-bg">
                <section>
                <div class="welcome-heading ans-que-heading"> 
                    <h1><span>Registration Guide</span></h1>
                    <hr class="shadow-line">
                </div>
                <div class="main-content">
                    <p><span style="box-sizing: border-box; font-family: Raleway, sans-serif; font-size: 14px; text-align: center; color: rgb(0, 0, 255);"><span style="box-sizing: border-box; font-weight: 700;"><span style="box-sizing: border-box;">This guide will assist you with our registration questions.</span></span></span>&nbsp;<span style="font-size:14px;"><span style="box-sizing: border-box; color: rgb(0, 0, 255);"><span style="box-sizing: border-box; font-weight: 700;">Answers can be edited at any stage in your account dashboard.</span></span></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;"><span style="box-sizing: border-box; color: rgb(0, 0, 255);"><span style="box-sizing: border-box; font-weight: 700;">If any confusion, please do not hesitate to contact us and one of our team members can call you to assist with the questions and answers.</span></span></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;">&nbsp;</p>
                </div>
                <div class="panel-group" id="accordion">
                                                            <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">
                          <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Optometry</a>
                        </h4>
                      </div>
                      <div id="collapse2" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;"><u style="box-sizing: border-box;">Please list the equipment your store has to offer?</u></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">Please tick all that apply. We ask this question as some Locums are only comfortable testing with certain equipment. This way we ensure that the applicants who apply can fulfil the job.</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;"><u style="box-sizing: border-box;">How many years does the optician need to be qualified?</u></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">For any locum who works at your store, what is the minimum number of years experience you require them to have? Whichever answer you select, please select all options above this requirement. For example, if you need the Locum to have a minimum of 6 years experience, please select 6-10 years, 11-15 years, 16-20 years, 21-25 years and 26-50 years.</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">It is important to select all options above your requirement to help match potential locums.</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;"><u style="box-sizing: border-box;">Do you require the locum to have any area of specialism?</u></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">If you require your locums to have additional experience or accreditation, please tick the relevant box. Please select all that apply. This will ensure that only Locums with the necessary expertise are matched to any job that you post.</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;"><u style="box-sizing: border-box;">What is the testing time for a standard eye examination?</u></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">How long are your eye examination appointments? Please select the relevant option and each testing time below your requirement. For example, if your standard eye examination appointment is 20 minutes, please select 16-20 minutes and 10-15 minutes. This will help us to match potential locums.</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;"><u style="box-sizing: border-box;">What is the testing time for a standard contact lens aftercare?</u></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">How long are your contact lens aftercare appointments? Please select the relevant option and each testing time below your requirement. For example, if your standard contact lens aftercare appointment is 20 minutes, please select 16-20 minutes, 10-15 minutes and 5-10 minutes. This will help us to match potential locums.</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;"><u style="box-sizing: border-box;">Does the optometrist need to have lab glazing experience?</u></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">If you need the Locum to be able to glaze, please select &lsquo;Yes&rsquo;</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">(Glazing experience: carry out spectacle repairs, assemble rimless spectacles, glaze spectacles and/or surface)</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;"><u style="box-sizing: border-box;">Does the Optometrist need to supervise a pre-reg?</u></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">Do locums need to supervise a pre-registration student while working for you? If so, please select &lsquo;Yes&rsquo;</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;"><u style="box-sizing: border-box;">Are there any specific languages the locum should know?</u></span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;"><span style="box-sizing: border-box;">If you require your Locums to carry out examinations in additional languages, please select the relevant option. Please select all that apply. This will ensure any jobs you post will only show Locums who meet your language requirements.</span></p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;">&nbsp;</p>

<p style="box-sizing: border-box; margin: 0px 0px 20px; color: rgb(13, 13, 13); text-align: justify; font-family: Raleway, sans-serif; font-size: 14px;">&nbsp;</p>
                        </div>
                      </div>
                    </div>
                                                        </div> 
                    
                </section>
            </div>
        </div>
    </div>
</div>        <script type="text/javascript" src="https://locumkit-old.noumandev.work/public/frontend/locumkit-template/js/jquery.dataTables.min.js" charset="UTF-8"></script>
@endsection