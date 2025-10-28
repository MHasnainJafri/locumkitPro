{!! get_mail_header() !!}

<div class="mail-job-info" style="padding: 25px 50px 5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
    Hello, {{ $employer->firstname }}

    A locum want to negotiate on job rate you posted on {{ get_date_with_default_format($job->created_at) }}

    <h3>Job Information is below</h3>
    <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
        <tr style="background-color: #92D000;">
            <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation (Key Details) </td>
        </tr>
        <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> {{ get_date_with_default_format($job->job_date) }} </td>
        </tr>
        <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> {{ set_amount_format($job->job_rate) }} </td>
        </tr>
        <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Address</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> {{ $job->job_address }} </td>
        </tr>
        <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info:</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px; color:red; font-weight:bold;"> {{ $job->job_post_desc }} </td>
        </tr>
    </table>

    <h3>Freelancer information who want to negotiate</h3>

    <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
        <tr style="background-color: #92D000;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Freelancer information </th>
        </tr>
        <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Name</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">{{ $freelancer->firstname . ' ' . $freelancer->lastname }}</td>
        </tr>
        <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Contact</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">{{ $freelancer->user_extra_info->mobile }}</td>
        </tr>
        <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Goc</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">{{ $freelancer->user_extra_info->goc }}</td>
        </tr>
        <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Opthalmic number (OPL):</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">{{ $freelancer->user_extra_info->opl }}</td>
        </tr>
        <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Freelancer Negotiate Message:</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">{{ $freelancer_message }}</td>
        </tr>

        {!! $freelancer_questions_html !!}
    </table>

    <p>Freelancer want job rate to increase upto <strong>{{ set_amount_format($job_expected_rate) }}</strong>. Click the below link to accept freelancer rate.</p>

    <p>
        <a href="{{ $accept_url }}"
           style="float: left;  margin-bottom: 15px;  margin-top: -10px;outline: none !important;border-radius: 25px;float: left;margin-bottom: 15px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;">Accept
            Freelancer Offered Rate</a>
    </p>
    <br />
    <p style="font-style: italic; color:red;margin-top: 43px; display: block;">Please note after clicking the link above, job rate increased and current freelancer will be accepted for job automatically. We will notify freelancer.</p>
    <br />
    <p style="font-style: italic;">If you are not interested, ignore the mail</p>
</div>

{!! get_mail_footer() !!}
