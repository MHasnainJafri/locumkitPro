<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Developed by Nouman Habib, +923165667643, noumanhabib521@gmail.com">
    <meta name="keywords" content="LocumKit, SightCare, Nouman Habib">
    <title>SightCare - Find a LocumKit</title>

    <script type="text/javascript" src="{{ asset('frontend/locumform/js/jquery-2.0.3.min.js') }}"></script>
    <!-- SweetAlert -->
    <script src="{{ asset('frontend/locumform/js/sweetalert.min.js') }}"></script>
    <!-- ========== Css Files ========== -->
    <link href="{{ asset('frontend/locumform/css/root.css') }}" rel="stylesheet">

    <style type="text/css">
        body {
            background: #fff;
        }

        #form1 {
            float: center;
            width: 750px;
        }

        .cust {
            color: #00b4ec;
            font-size: 15px;
            font-family: 'Montserrat', 'sans-serif';
            margin-top: 14px;
            margin-right: 14px;
        }

        .center-fit {
            max-width: 100%;
            max-height: 100vh;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="top" align="center" style="height: 50px; width: 1080px; background-color: #fff; display: inline;">
        <div align="left" style="display:inline; margin-left: 50px;  position: absolute; top: 17%; left:8%;">
            <img src="{{ asset('frontend/locumform/img/logo.png') }}" width="400px" />
        </div>
    </div>
    <div align="center">
        <img class="center-fit" src="{{ asset('frontend/locumform/img/1.jpg') }}" width='auto' style="width: 100%; height: 400px;" />
    </div>

    <div class="sent-message" style="display:flex">
        <h1 style="text-align: center;display: inline-block;align-content: center;margin: 50px auto;color: #10a0dd;box-shadow: 0px 0px 4px 0px #d2d2d2;padding: 20px;">
            Thank you - Your request has been received by our team and weshall get back to you as soon as we find a suitable locum
        </h1>
    </div>

    @if (isset($script) && $script)
        {!! $script !!}
    @endif

</body>

</html>
