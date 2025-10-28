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

        .login-form {
            margin: 40px auto;
            box-shadow: 0px 0px 4px 0px #ececec;
        }
    </style>
</head>

<body>
    <div class="top" align="center" style="height: 50px; width: 1080px; background-color: #fff; display: inline;">
        <div align="left" style="display:inline; margin-left: 50px;  position: absolute; top: 17%; left:8%;"><img src="{{ asset('frontend/locumform/img/logo.png') }}" width="400px"></div>
    </div>
    <div align="center">
        <img class="center-fit" src="{{ asset('frontend/locumform/img/1.jpg') }}" width='auto' style="width: 100%; height: 400px;">
    </div>



    <div class="login-form" id="findlocum" align="center">

        <form method="post">
            @csrf
            <div class="top">
                <h1>Find a Locum</h1>
            </div>
            <div class="form-area" align="left">
                <div class="form-group">
                    <label class="col-sm-3 control-label form-label">Contact Name</label>
                    <div class="col-sm-8">
                        <input name="contactname" type="text" class="form-control" id="contantname" placeholder="Contact Name" required />
                    </div>
                </div><br><br>
                <div class="form-group">
                    <label class="col-sm-3 control-label form-label">Email</label>
                    <div class="col-sm-8">
                        <input name="email" type="email" class="form-control" id="email" placeholder="example@example.com" required />
                    </div>
                </div><br><br>
                <div class="form-group">
                    <label class="col-sm-3 control-label form-label">Internal Reference</label>
                    <div class="col-sm-8">
                        <input name="intRef" type="text" class="form-control" id="intRef" placeholder="Internal Reference" required />
                    </div>
                </div><br><br>
                <div class="form-group">
                    <label class="col-sm-3 control-label form-label">Date</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input name="date" type="date" class="form-control" id="date" placeholder="dd/mm/yyyy" required />
                        </div>
                    </div>
                </div><br><br>
                <div class="form-group">
                    <label class="col-sm-3 control-label form-label">Rate</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <div class="input-group-addon"><i> £ </i></div>
                            <input name="rate" type="number" class="form-control" id="rate" placeholder="" required />
                        </div>
                    </div>
                </div><br><br>
                <div class="form-group">
                    <label class="col-sm-3 control-label form-label">Store name & Address</label>
                    <div class="col-sm-8">
                        <textarea name="store" id="store" class="form-control" style="height: 80px; padding-left:10px;" cols="45" rows="" placeholder="Please Enter Store Name and Address here" required></textarea>
                    </div>
                </div><br><br><br><br>
                <div class="form-group">
                    <div class="form-group">
                        <label class="col-sm-3 control-label form-label">Store Timings</label>
                        <div class="col-sm-8">
                            <table style="font-size: 20px">
                                <tr>
                                    <td width="auto" style="height: 10px">

                                        <div align="center" style="margin-top: 15px;font-size: 12pt;">Opening Time
                                            <input type="time" class="form-control" name="open" id="open" required>
                                        </div>
                                    </td>
                                    <td width="auto">
                                        <div align="center" style="margin-top: 15px; font-size: 12pt;">Closing Time
                                            <input type="time" class="form-control" name="close" id="close" required>
                                        </div>
                                    </td>
                                    <td width="auto">
                                        <div align="center" style="margin-top: 15px;font-size: 12pt;">Lunch Break
                                            <select class="form-control" name="break" id="break" required>
                                                <option disabled>Select duration</option>
                                                <option>10 Mins</option>
                                                <option>20 Mins</option>
                                                <option>30 Mins</option>
                                                <option>40 Mins</option>
                                                <option>50 Mins</option>
                                                <option>60 Mins</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div><br><br><br><br>
                    <div class="form-group">
                        <label class="col-sm-3 control-label form-label">Testing Times</label>
                        <div class="col-sm-8">
                            <input name="testTime" type="text" class="form-control" id="testTime" placeholder="Please enter your testing time" required />
                        </div>
                    </div><br><br>
                    <div class="form-group">
                        <label class="col-sm-3 control-label form-label">Any other special requirements/notes for the locum to be aware of</label>
                        <div class="col-sm-8">
                            <textarea name="speReq" id="speReq" class="form-control" style="height:100px; padding-left:10px;" cols="45" rows="5" placeholder="i.e must be able to use OCT / supervise a pre reg etc; if none, please type in none" required></textarea>

                        </div>
                    </div><br><br>
                    <div class="form-group">
                        <div class="col-sm-offset-6 col-sm-2" style="padding: 10px;">
                            <input type="submit" style="background-color: #00b4ec; height: 50px; width: 100px; font-size: 20px" class="btn btn-default" value="Submit ">
                        </div>
                    </div>

        </form>

    </div>
    </div>

    @if (session('error'))
        <script>
            swal('Email failed', `{{ session('error') }}`, 'error');
        </script>
    @endif

    @if ($errors->any())
        @php
            $errorUlInner = '';
            foreach ($errors->all() as $value) {
                $errorUlInner .= "->{$value}\n";
            }
            $errorHtmlAcl = "Something went wrong\n";
            $errorHtmlAcl .= "\n{$errorUlInner}\n";
        @endphp
        <script>
            swal('Data error', `{!! $errorHtmlAcl !!}`, 'error');
        </script>
    @endif


</body>

</html>
