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
                    <p><span><span style="font-size: 14px;"><strong>This guide will assist you with our registration questions.</strong></span></span>&nbsp;<span><span style="font-size: 14px;"><strong>Answers can be edited at any stage in your account dashboard.</strong></span></span></p>

<p>&nbsp;</p>
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
                            <p><u>How many years have you been qualified?</u></p>

<p>Please enter the number of whole years it has been since your GOC registration.</p>

<p>Please can you also send a copy of your GOC certificate to the following email address (admin@locumkit.com)</p>

<p>&nbsp;</p>

<p><u>Testing times?</u></p>

<p>These questions are asked to determine how long it takes you to perform various examinations.</p>

<p>Please try to be accurate with your answer as employers may expect you to manage an entire diary with the selected time.</p>

<p>&nbsp;</p>

<p><u>Do you have any areas of specialism?</u></p>

<p>If you have undertaken any accreditation courses with your local CCG or pathway specialist training, please select the relevant fields.</p>

<p>Some employers require additional specialism&rsquo;s, so answering this question will give a better chance at being matched to potential jobs.</p>

<p>&nbsp;</p>

<p><u>What languages can you speak?</u></p>

<p>In addition to English, which languages can you fluently interact with patients in?</p>

<p>Please select all that apply. It is important that you can carry out a full examination in the selected languages as employers may require this.</p>

<p>&nbsp;</p>

<p><u>Can you supervise a pre-reg?</u></p>

<p>Have you been qualified for more than three years and are comfortable supervising a pre-registration student? If so, please tick the &lsquo;yes&rsquo; box. Some employers may require supervision of a pre registration student.</p>

<p>&nbsp;</p>

<p><u>Do you have any experience in the lab - ie glazing?</u></p>

<p>Are you able to competently carry out spectacle repairs, assemble rimless spectacles, glaze spectacles and/or surface? If so, please tick the &lsquo;yes&rsquo; boxThis refers to the standard base daily rate you expect to receive each particular weekday, excluding bonuses. Please enter a value for each weekday. This will ensure that all notifications you receive from LocumKit are at the minimum rate you require. Rates can be edited at any time in your dashboard. Rates can also be edited for any particular one off day from your calendar.</p>

<p>&nbsp;</p>

<p><u>Which of the following store have you worked for in the past?</u></p>

<p>This refers to the listed companies you have already worked for in the past, whether that be as a locum or a resident at any of their branches, For each company, please also select how long it has been since you last worked for that particular company at any of their branches. Certain employers require familiarity with their systems so will only ask for those with recent experience. Therefore we ask you to update this regularly on a bi-annual basis. Please select all that apply.</p>

<p>&nbsp;</p>

<p><u>How far are you willing to travel?</u></p>

<p>Please select the distance you are willing to travel for your locum days. Once you have made your selection, you will shown a list of all towns located within this distance. Please de-select any particular town that you do not wish to work in. This will ensure that all notifications you receive from Locumkit are only within the areas you want to go to.</p>

<p>&nbsp;</p>

<p><u>How many CET points do you have within this current cycle?</u></p>

<p>This refers to the number of continuous education and training points you have completed in the current 3 year cycle, both interactive and non interactive.</p>

<p>We reserve the right to request evidence as proof of these points on a quarterly basis.</p>

<p>&nbsp;</p>

<p><u>What is your GOC NO?</u></p>

<p>Please enter your GOC registration number in full (00-0000)</p>

<p>&nbsp;</p>

<p><u>Indemnity insurance information?</u></p>

<p>Please provide us details of your indemnity insurance so we can pass it on to the respective employers you engage with.</p>

<p>We ask this as employers often ask for proof of indemnity cover.</p>

<p>Please can you send a copy of your AOP certificate to the following email address (locumkit@gmail.com)</p>
                        </div>
                      </div>
                    </div>
                                                        </div> 
                    
                </section>
            </div>
        </div>
    </div>
</div>        <script type="text/javascript" src="https://locumkit-old.noumandev.work/public/frontend/locumkit-template/js/jquery.dataTables.min.js" charset="UTF-8"></script>

 
<!-- Alert Message Modal -->
<div id="alert-modal" class=" alert-modal modal fade" >
    <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <!--<button type="button" class="close hide-pop-up" data-dismiss="modal">&times;</button>-->
            <h4 class="modal-title">LocumKit</h4>
        </div>
        <div class="modal-body" >
            <h3 id="alert-message"></h3>
        </div>
        <div class="modal-footer">
            <button type="button"  class="close-alert btn btn-default" data-dismiss="modal" onClick="window.location.reload()">Ok</button>
        </div>
    </div>

    </div>
</div> 
<!-- Alert Message Modal -->
<div id="alert-confirm-modal" class=" alert-modal modal fade" >
    <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <!-- <button type="button" class="close close-alert" data-dismiss="modal" onClick="window.location.reload()">&times;</button> -->
            <h4 class="modal-title">LocumKit</h4>
        </div>
        <div class="modal-body" >
            <h3 id="alert-message"></h3>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" id="confirm">Yes</button>
            <button type="button" class="close-alert btn btn-default" >No</button>
        </div>
    </div>

    </div>
</div> 

<!-- Modal Manage income bank-->
<div id="manage-bank-income" class="modal fade financepopup" role="dialog">
<div class="modal-dialog">
<form action="" method="post">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><!--Manage Income Bank Status-->Locumkit</h4>
    </div>
    <div class="modal-body">
        <div class="col-md-12 pad0 financeform">        
            <div class="form-group" id="bank_date">
                <div class="pull-left" style="display: none;">
                    <input name="in_bank" id="modal-in_bank" value="1" type="hidden">  
                </div>
                <div class="input-group" id="for-display" style="display: block;">
                    <p>Please enter the date the transaction hit the bank</p>
                    <input type="hidden" name="in_bankid" id="in_bankid">
                    <input type="text" class="form-control financein_bankdate readonly" name="in_bankdate" id="in_bankdate" placeholder="dd/mm/yyyy" required>
                    <button type="submit" class="btn btn-info" name="income-bank-btn" value="income-bank-btn" id="income-bank-btn" >Submit</button>
                    </div>
                </div>  
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        </form>
    </div>
</div>
                                                
<!-- Modal Manage expense bank-->
<div id="manage-bank-expense" class="modal fade financepopup" role="dialog">
<div class="modal-dialog">
<form action="" method="post">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Locumkit</h4>
    </div>
    <div class="modal-body">
        <div class="col-md-12 pad0 financeform">        
            <div class="form-group" id="bank_date">
                <div class="pull-left" style="display:none">
                    <input name="ex_bank" id="modal-ex_bank" value="1" type="hidden">
                </div>
                <div class="input-group" id="for-displayex" style="display:block">
                    <p>Please enter the date the transaction hit the bank</p>
                    <input type="hidden" name="ex_bankid" id="ex_bankid">                  
                    <input type="text" class="form-control financeex_bankdate readonly" name="ex_bankdate" id="ex_bankdate" placeholder="dd/mm/yyyy" required>
                    <button type="submit" class="btn btn-info" id="expense-bank-btn" name="expense-bank-btn" value="expense-bank-btn">Submit</button>
                    </div>
                </div>  
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        </form>
    </div>
</div>


<script type="text/javascript">
    
    
        /*use for manage bank status*/
       $('input#modal-in_bank').change(function(){
        var c = this.checked ? '1' : '0';
        if(c==1){
            
            $('#fordisplay').show();
        }else{
            $('#fordisplay').hide();
            }
       });

       $('input#modal-ex_bank').change(function(){
        var c = this.checked ? '1' : '0';
        if(c==1){
            
            $('#fordisplayex').show();
        }else{
            $('#fordisplayex').hide();
            }
       }); 

    function managebankincome(id) {
	$('#fordisplay').hide();
	$('#in_bankdate').val('');
	$('#modal-in_bank').attr('checked', false); 
    $('#in_bankid').val(id);
    $('#manage-bank-income').modal('show');
    }   
    function managebankexpanse(id) {
	 $('#fordisplayex').hide();
	$('#ex_bankdate').val('');
	$('#modal-ex_bank').attr('checked', false); 
    $('#ex_bankid').val(id);
    $('#manage-bank-expense').modal('show');
    }
    /*use for manage bank status end*/
    
    
    
    $('div.alert-modal button.close-alert').click(function(){
        messageBoxClose();
    }); 
    $('div.alert-modal button.close.hide-pop-up').click(function(){
        messageBoxClose();
    });
    function messageBoxClose(){
        $('div#alert-modal').removeClass('in');
        $('div#alert-modal').css('display','none');
        $('div#alert-confirm-modal').removeClass('in');
        $('div#alert-confirm-modal').css('display','none');
    }
    function messageBoxOpen(msg, url){
        $('div#alert-modal #alert-message').html(msg);
        $('div#alert-modal').addClass('in');
        $('div#alert-modal').css('display','block'); 
        if(url != null ) {  
           $('button.close-alert').attr('onClick','window.location.replace("'+url+'")');
        }
    }
</script> 

<!--default date picker-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('input.financein_bankdate').datepicker({
            maxDate: '0',
            dateFormat: 'dd/mm/yy'
        });
    });  
       $(document).ready(function() {
        $('input.financeex_bankdate').datepicker({
            maxDate: '0',
            dateFormat: 'dd/mm/yy'
        });
    }); 


$(".readonly").keydown(function(e){
e.preventDefault();
});
 

//prevent to submit form by enter button

$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      //event.preventDefault();
      //return false;
    }
  });

$('p.finance-price').each(function(){
var hprice =  $(this).text().split('£');
$(this).text('£'+set_thousand_number_format(hprice[1]));

});
$('h2.mar0').each(function(){
var hprice =  $(this).text().split('£');
$(this).text('£'+set_thousand_number_format(hprice[1]));

});


});



function set_thousand_number_format(nStr){
   nStr += '';
   x = nStr.split('.');
   x1 = x[0];
   x2 = x.length > 1 ? '.' + x[1] : '';
   var rgx = /(\d+)(\d{3})/;
   while (rgx.test(x1)) {
     x1 = x1.replace(rgx, '$1' + ',' + '$2');
   }
   return x1 + x2;        
 }

    /*function set_thousand_number_format(nStr){
      nStr += '';
      x = nStr.split('.');
      x1 = x[0].replace(/,/g, '').split('');
      x2 = x.length > 1 ? '.' + x[1] : '';
      x3 = '';

      x1.forEach(function(item, index, array) {
        x3_length = x1.length - 4;
        x3 +=''+item;
        if(index == x3_length)
          x3 +=',';
      });
      x4 = x3.split(','); 
      if(x4.length > 1)
        return set_hundred_number_format(x4[0]) +','+ x4[1]+x2;
      else
        return x4[0]+x2;      
    }


  function set_hundred_number_format(nStr){
      nStr += '';
      x = nStr.split('.');
      x1 = x[0];
      x6 = x.length > 1 ? '.' + x[1] : '';
      var rgx = /(\d+)(\d{2})/;
      while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
      }
      return x1 + x6;        
    }*/
  </script>        <a href="#" class="scrollToTop"><i class="fa fa-chevron-circle-up" aria-hidden="true"></i></a>
	<script>
		function logout_user(obj)
		{ 
			var str=obj.value; //alert(obj);
		   if(str!=""){
		   $.ajax({
					'url'   :'/ajax-request',
					'type'  :'POST',
					'data'  :{'user_id':str, 'log_out':'1'},
					'success':function(result){ //alert(result);
						console.log(result);
						if($.trim(result)=="1"){
							$('div#alert-modal button.close.hide-pop-up').hide();
							$("#loader-div").hide(100);		                    
							messageBoxOpen('Logged out successfully.');
							//$('div#alert-modal button.close-alert.btn.btn-default').html('Login');
							$('div#alert-modal button.close-alert.btn.btn-default').remove();
							window.setTimeout(function(){
						        window.location.href = "https://locumkit-old.noumandev.work";
						    }, 500);
						}
					}
				});
		   }
		}	
	</script>
	<!--  sticky Header script and scroll to top -->
	<script type="text/javascript">
        $(function(){
	        var headerTop = $('#header').offset().top;
	        $(window).scroll(function(){
		        if( $(window).scrollTop() > headerTop ) {
			        $('#header').css({position: 'fixed', top: '0px'});
					$('#header').addClass('fixed-header-wrapper');
					$('#header .navbar-default .navbar-brand img').css({width: '70px', transition: 'all 0.4s ease 0s'});
					$('#header .navbar-default .navbar-brand ').css({padding: '5px 15px', transition: 'all 0.4s ease 0s'});
					$('#header .navbar-default .top-main-nav').css({padding: '15px 0 10px', transition: 'all 0.4s ease 0s'});
			    } else {
			        $('#header').css({position: 'static', top: '0px'});
					$('#header').removeClass('fixed-header-wrapper');
					$('#header .navbar-default .navbar-brand img').css({width: '80px', transition: 'all 0.4s ease 0s'});
					$('#header .navbar-default .navbar-brand ').css({padding: '15px', transition: 'all 0.4s ease 0s'});
					$('#header .navbar-default .top-main-nav').css({padding: '30px 0 15px', transition: 'all 0.4s ease 0s'});
		        }
	        });
        });

        $(document).ready(function(){
			//Check to see if the window is top if not then display button
			$(window).scroll(function(){
				if ($(this).scrollTop() > 100) {
					$('.scrollToTop').fadeIn();
				} else {
					$('.scrollToTop').fadeOut();
				}
			});

			//Click event to scroll to top
			$('.scrollToTop').click(function(){
				$('html, body').animate({scrollTop : 0},800);
				return false;
			});

		});
    </script>
    <!-- sticky Header script and scroll to top script  End -->
<script>

  
    
        /*use for manage bank status*/
       $('input#modal-in_bank').change(function(){
        var c = this.checked ? '1' : '0';
        if(c==1){
            
            $('#fordisplay').show();
        }else{
            $('#fordisplay').hide();
            }
       });

       $('input#modal-ex_bank').change(function(){
        var c = this.checked ? '1' : '0';
        if(c==1){
            
            $('#fordisplayex').show();
        }else{
            $('#fordisplayex').hide();
            }
       }); 

    function managebankincome(id) {
	$('#fordisplay').hide();
	$('#in_bankdate').val('');
	$('#modal-in_bank').attr('checked', false); 
    $('#in_bankid').val(id);
    $('#manage-bank-income').modal('show');
    }   
    function managebankexpanse(id) {
	 $('#fordisplayex').hide();
	$('#ex_bankdate').val('');
	$('#modal-ex_bank').attr('checked', false); 
    $('#ex_bankid').val(id);
    $('#manage-bank-expense').modal('show');
    }
    /*use for manage bank status end*/
    $('div.alert-modal button.close-alert').click(function(){
        messageBoxClose();
    }); 
    $('div.alert-modal button.close.hide-pop-up').click(function(){
        messageBoxClose();
    });
    function messageBoxClose(){
        $('div#alert-modal').removeClass('in');
        $('div#alert-modal').css('display','none');
        $('div#alert-confirm-modal').removeClass('in');
        $('div#alert-confirm-modal').css('display','none');
    }
    function messageBoxOpen(msg, url){
        $('div#alert-modal #alert-message').html(msg);
        $('div#alert-modal').addClass('in');
        $('div#alert-modal').css('display','block'); 
        if(url != null ) {  
           $('button.close-alert').attr('onClick','window.location.replace("'+url+'")');
        }
    }


        $("#loader-div").hide(100);
</script>
    
@endsection
