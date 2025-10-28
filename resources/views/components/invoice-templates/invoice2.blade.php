<div style="width: 700px;margin:0 auto;">
    <div style=" border: 2px solid #dedede; width: 100%;" class="prevboxshadow">
        <div class="mail-header" style="width: 100%; text-align: center; background: rgb(0, 169, 224) none repeat scroll 0px 0px; border-bottom:2px solid #dedede;">
            <a href={{ url('/') }}>
                <img src="{{ env('APP_URL') . '/frontend/locumkit-template/img/logo.png' }}" alt="Locumkit" width="100px" style="margin:10px;">
            </a>
        </div>
        <div style="margin-bottom: -5px; width: 100%;">
            <section>
                <div>
                    <div style="text-align: center;">
                        <h1 style="margin: 0; border-bottom:2px solid #dedede; padding: 10px 0;text-transform: capitalize;font-size: 23px;font-weight: 600;background: #e0e0e0;color: #000;">Invoice</h1>
                    </div>
                    <div>
                        <div style="width: 100%;">
                            <p style="padding: 15px; margin: 0; text-align: end; text-align: end;">Latest activity invoice creadted on {{ date('d-m-Y') }}</p>
                        </div>
                        <div style=" width: 100%;">
                            <table style=" width: 100%;   border-spacing: 0;">
                                <thead>
                                    <tr>
                                        <th colspan="2" style="text-align: center;height: 45px; background:#e0e0e0;border-bottom: 2px solid #dedede;border-top: 2px solid #dedede;">Status : Draft</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding: 15px; text-align:left;">
                                            <div> {{ $data['supplier_name'] }} </div>
                                            <div> {{ $data['supplier_address'] }} </div>
                                            <div>Email :{{ $data['supplier_email'] }}</div>
                                        </td>
                                        <td style=" width: 32%;text-align:left;">
                                            <div>Invoice number : {{ isset($data['invoice_no']) ? $data['invoice_no'] : '--' }} </div>
                                            <div>Invoice Date : {{ date('d-m-Y') }} </div>
                                            <div>Due Date : {{ date('d-m-Y') }} </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <table style="border-top: 2px solid #dedede; width: 100%;border-collapse:collapse;">
                            <thead style="background: #e0e0e0;">
                                <tr style="height: 45px; border-bottom: 2px solid #dedede;">
                                    <th style="text-align: center;border-bottom:2px solid #dedede;">Job No.</th>
                                    <th style="text-align: center;border-bottom:2px solid #dedede;">Price</th>
                                    <th style="text-align: center;border-bottom:2px solid #dedede;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="height: 45px;">
                                    <td style="text-align: center; padding: 15px 0;"> {{ $data['job_id'] }} </td>
                                    <td style="text-align: center; padding: 15px 0;">{{ set_amount_format($data['job_rate']) }}</td>
                                    <td style="text-align: center; padding: 15px 0;">{{ set_amount_format($data['job_rate']) }}</td>
                                </tr>
                                <tr style="height: 45px;">
                                    <td></td>
                                    <td style="text-align: center;border-top: 2px solid black;padding: 20px 0;"><b>TOTAL DUE</b></td>
                                    <td style="text-align: center;border-top: 2px solid black;padding: 20px 0;"><b>{{ set_amount_format($data['job_rate']) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
