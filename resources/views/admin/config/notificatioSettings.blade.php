@extends('admin.layout.app')
@section('content')

<div class="main-container container">
    @include('admin.config.sidebar')

    <div class="col-lg-12 main-content">
        <div id="breadcrumbs" class="breadcrumbs">
            <div id="menu-toggler-container" class="hidden-lg">
                <span id="menu-toggler">
                    <i class="glyphicon glyphicon-new-window"></i>
                    <span class="menu-toggler-text">Menu</span>
                </span>
            </div>
            <ul class="breadcrumb">
            </ul>
        </div>
        <div class="page-content">

            <form method="post" class="relative&#x20;form-horizontal" action="{{route('admin.config.NotificationSetting')}}">
                @csrf
                <div id="accordion">
                    <h3>Notification time manager</h3>
                    <div>
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="attend_job_notification">Attend Job Notification Time (in 24 hour format)</label>
                            <div class="col-lg-10">
                                <select name="attend_job_notification" id="attend_job_notification" class="form-control">
                                    <option {{$notification -> attend_job_notification == '01' ? 'selected' : ''}} value="01">01</option>
                                    <option {{$notification -> attend_job_notification == '02' ? 'selected' : ''}} value="02">02</option>
                                    <option {{$notification -> attend_job_notification == '03' ? 'selected' : ''}} value="03">03</option>
                                    <option {{$notification -> attend_job_notification == '04' ? 'selected' : ''}} value="04">04</option>
                                    <option {{$notification -> attend_job_notification == '05' ? 'selected' : ''}} value="05">05</option>
                                    <option {{$notification -> attend_job_notification == '06' ? 'selected' : ''}} value="06">06</option>
                                    <option {{$notification -> attend_job_notification == '07' ? 'selected' : ''}} value="07">07</option>
                                    <option {{$notification -> attend_job_notification == '08' ? 'selected' : ''}} value="08">08</option>
                                    <option {{$notification -> attend_job_notification == '09' ? 'selected' : ''}} value="09">09</option>
                                    <option {{$notification -> attend_job_notification == '10' ? 'selected' : ''}} value="10">10</option>
                                    <option {{$notification -> attend_job_notification == '11' ? 'selected' : ''}} value="11">11</option>
                                    <option {{$notification -> attend_job_notification == '12' ? 'selected' : ''}} value="12">12</option>
                                    <option {{$notification -> attend_job_notification == '13' ? 'selected' : ''}} value="13">13</option>
                                    <option {{$notification -> attend_job_notification == '14' ? 'selected' : ''}} value="14">14</option>
                                    <option {{$notification -> attend_job_notification == '15' ? 'selected' : ''}} value="15">15</option>
                                    <option {{$notification -> attend_job_notification == '16' ? 'selected' : ''}} value="16">16</option>
                                    <option {{$notification -> attend_job_notification == '17' ? 'selected' : ''}} value="17">17</option>
                                    <option {{$notification -> attend_job_notification == '18' ? 'selected' : ''}} value="18">18</option>
                                    <option {{$notification -> attend_job_notification == '19' ? 'selected' : ''}} value="19">19</option>
                                    <option {{$notification -> attend_job_notification == '20' ? 'selected' : ''}} value="20">20</option>
                                    <option {{$notification -> attend_job_notification == '21' ? 'selected' : ''}} value="21">21</option>
                                    <option {{$notification -> attend_job_notification == '22' ? 'selected' : ''}} value="22">22</option>
                                    <option {{$notification -> attend_job_notification == '23' ? 'selected' : ''}} value="23">23</option>
                                    <option {{$notification -> attend_job_notification == '24' ? 'selected' : ''}} value="24">24</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="expenses_job_notification">Expenses Job Notification Time (in 24 hour format)</label>
                            <div class="col-lg-10">
                                <select name="expenses_job_notification" id="expenses_job_notification" class="form-control">
                                    <option {{$notification -> expenses_job_notification == '01' ? 'selected' : ''}} value="01">01</option>
                                    <option {{$notification -> expenses_job_notification == '02' ? 'selected' : ''}} value="02">02</option>
                                    <option {{$notification -> expenses_job_notification == '03' ? 'selected' : ''}} value="03">03</option>
                                    <option {{$notification -> expenses_job_notification == '04' ? 'selected' : ''}} value="04">04</option>
                                    <option {{$notification -> expenses_job_notification == '05' ? 'selected' : ''}} value="05">05</option>
                                    <option {{$notification -> expenses_job_notification == '06' ? 'selected' : ''}} value="06">06</option>
                                    <option {{$notification -> expenses_job_notification == '07' ? 'selected' : ''}} value="07">07</option>
                                    <option {{$notification -> expenses_job_notification == '08' ? 'selected' : ''}} value="08">08</option>
                                    <option {{$notification -> expenses_job_notification == '09' ? 'selected' : ''}} value="09">09</option>
                                    <option {{$notification -> expenses_job_notification == '10' ? 'selected' : ''}} value="10">10</option>
                                    <option {{$notification -> expenses_job_notification == '11' ? 'selected' : ''}} value="11">11</option>
                                    <option {{$notification -> expenses_job_notification == '12' ? 'selected' : ''}} value="12">12</option>
                                    <option {{$notification -> expenses_job_notification == '13' ? 'selected' : ''}} value="13">13</option>
                                    <option {{$notification -> expenses_job_notification == '14' ? 'selected' : ''}} value="14">14</option>
                                    <option {{$notification -> expenses_job_notification == '15' ? 'selected' : ''}} value="15">15</option>
                                    <option {{$notification -> expenses_job_notification == '16' ? 'selected' : ''}} value="16">16</option>
                                    <option {{$notification -> expenses_job_notification == '17' ? 'selected' : ''}} value="17">17</option>
                                    <option {{$notification -> expenses_job_notification == '18' ? 'selected' : ''}} value="18">18</option>
                                    <option {{$notification -> expenses_job_notification == '19' ? 'selected' : ''}} value="19">19</option>
                                    <option {{$notification -> expenses_job_notification == '20' ? 'selected' : ''}} value="20">20</option>
                                    <option {{$notification -> expenses_job_notification == '21' ? 'selected' : ''}} value="21">21</option>
                                    <option {{$notification -> expenses_job_notification == '22' ? 'selected' : ''}} value="22">22</option>
                                    <option {{$notification -> expenses_job_notification == '23' ? 'selected' : ''}} value="23">23</option>
                                    <option {{$notification -> expenses_job_notification == '24' ? 'selected' : ''}} value="24">24</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required&#x20;control-label&#x20;col-lg-2" for="feedback_job_notification">Feedback Job Notification Time (in 24 hour format)</label>
                            <div class="col-lg-10">
                                <select name="feedback_job_notification" id="feedback_job_notification" class="form-control">
                                    <option {{$notification -> feedback_job_notification == '01' ? 'selected' : ''}} value="01">01</option>
                                    <option {{$notification -> feedback_job_notification == '02' ? 'selected' : ''}} value="02">02</option>
                                    <option {{$notification -> feedback_job_notification == '03' ? 'selected' : ''}} value="03">03</option>
                                    <option {{$notification -> feedback_job_notification == '04' ? 'selected' : ''}} value="04">04</option>
                                    <option {{$notification -> feedback_job_notification == '05' ? 'selected' : ''}} value="05">05</option>
                                    <option {{$notification -> feedback_job_notification == '06' ? 'selected' : ''}} value="06">06</option>
                                    <option {{$notification -> feedback_job_notification == '07' ? 'selected' : ''}} value="07">07</option>
                                    <option {{$notification -> feedback_job_notification == '08' ? 'selected' : ''}} value="08">08</option>
                                    <option {{$notification -> feedback_job_notification == '09' ? 'selected' : ''}} value="09">09</option>
                                    <option {{$notification -> feedback_job_notification == '10' ? 'selected' : ''}} value="10">10</option>
                                    <option {{$notification -> feedback_job_notification == '11' ? 'selected' : ''}} value="11">11</option>
                                    <option {{$notification -> feedback_job_notification == '12' ? 'selected' : ''}} value="12">12</option>
                                    <option {{$notification -> feedback_job_notification == '13' ? 'selected' : ''}} value="13">13</option>
                                    <option {{$notification -> feedback_job_notification == '14' ? 'selected' : ''}} value="14">14</option>
                                    <option {{$notification -> feedback_job_notification == '15' ? 'selected' : ''}} value="15">15</option>
                                    <option {{$notification -> feedback_job_notification == '16' ? 'selected' : ''}} value="16">16</option>
                                    <option {{$notification -> feedback_job_notification == '17' ? 'selected' : ''}} value="17">17</option>
                                    <option {{$notification -> feedback_job_notification == '18' ? 'selected' : ''}} value="18">18</option>
                                    <option {{$notification -> feedback_job_notification == '19' ? 'selected' : ''}} value="19">19</option>
                                    <option {{$notification -> feedback_job_notification == '20' ? 'selected' : ''}} value="20">20</option>
                                    <option {{$notification -> feedback_job_notification == '21' ? 'selected' : ''}} value="21">21</option>
                                    <option {{$notification -> feedback_job_notification == '22' ? 'selected' : ''}} value="22">22</option>
                                    <option {{$notification -> feedback_job_notification == '23' ? 'selected' : ''}} value="23">23</option>
                                    <option {{$notification -> feedback_job_notification == '24' ? 'selected' : ''}} value="24">24</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="margin-top-30">
                    <input id="input-save" type="submit" class="btn btn-warning" value="Save" name="submit">
                </div>

            </form>
            <script type="text/javascript">
                $(function() {
                    Gc.saveCommand();
                    Gc.checkDataChanged();
                    $('#accordion').accordion({
                        heightStyle: "content",
                        collapsible: true
                    });
                });
            </script>
        </div>
    </div>
</div>
@endsection