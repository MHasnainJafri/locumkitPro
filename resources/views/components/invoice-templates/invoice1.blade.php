{{-- Required: $supplier, $user, $income --}}
<div style="width: 700px;margin:0 auto;">
    <div style="border: 2px solid #dedede; width: 100%;" class="prevboxshadow">
        <section style=" width: 100%;">
            <div style="text-align: center;  width: 100%;">
                <h1 style="margin: 0; border-bottom:2px solid #dedede; padding: 10px 0;text-transform: capitalize;font-size: 23px;font-weight: 600;background: #e0e0e0;color: #000;">Invoice</h1>
            </div>
            <div style="width: 100%;">
                <table style="width: 100%;border-spacing: 0;">
                    <tbody>
                        <tr>
                            <td style="padding: 15px; text-align:left;">
                                <div class="invoice-user-info" style="width: 315px;">
                                    <div style="font-weight: bold;border-bottom: 1px solid #ccc;padding-bottom: 5px;margin-bottom: 10px;">Supplier Information</div>
                                    <div><span><b>Name :</b> </span> {{ $data['supplier_name'] }} </div>
                                    <div><span><b>Address :</b> </span> {{ $data['supplier_address'] }} </div>
                                    <div><b>Email :</b> {{ $data['supplier_email'] }} </div>
                                </div>
                            </td>
                            <td style="text-align:left; padding:15px; vertical-align: baseline;">
                                <div style="font-weight: bold;border-bottom: 1px solid #ccc;padding-bottom: 5px;margin-bottom: 10px;">Locum Information</div>
                                <div><span><b>Name :</b> </span> {{ $data['your_name'] }} </div>
                                <div><span><b>Email :</b> </span> {{ $data['your_email'] }} </div>
                                <div><b>Invoice number : </b> {{ isset($data['invoice_no']) ? $data['invoice_no'] : '--' }} </div>
                                <div><b>Invoice Date :</b> {{ date('d-m-Y') }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="width: 100%;">
                <table style="border-top: 2px solid #dedede; width: 100%;border-collapse:collapse;">
                    <thead style="background: #e0e0e0;">
                        <tr style="height: 45px; border-bottom: 2px solid #dedede;">
                            <th style="text-align: center;border-bottom:2px solid #dedede;">Job No.</th>
                            <th style="text-align: center;border-bottom:2px solid #dedede;">Date</th>
                            <th style="text-align: center;border-bottom:2px solid #dedede;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 45px;">
                            <td style="text-align: center; padding: 15px 0;"> {{ $data['job_id'] }} </td>
                            <td style="text-align: center; padding: 15px 0;"> {{ \Carbon\Carbon::parse($data['job_date'])->format('d-m-Y') }}</td>
                            <td style="text-align: center; padding: 15px 0;"> {{ set_amount_format($data['job_rate']) }} </td>
                        </tr>
                        <tr style="height: 45px;">
                            <td style="border-top: 2px solid black;"></td>
                            <td style="text-align: center;border-top: 2px solid black; padding: 20px 0;"><b>TOTAL DUE</b></td>
                            <td style="text-align: center;border-top: 2px solid black; padding: 20px 0;"><b> {{ set_amount_format($data['job_rate']) }} </b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="width: 100%;">
                <table style="border-top: 2px solid #dedede; width: 100%;border-collapse:collapse;">
                    <thead style="background: #e0e0e0; width: 100%;">
                        <tr style="height: 45px; border-bottom: 2px solid #dedede;">
                            <th style="text-align: center;border-bottom:2px solid #dedede;" colspan="2">Please remit to</th>
                        </tr>
                    </thead>
                    <tbody style="width: 100%;">
                        <tr style="height: 35px; width: 100%;">
                            <td style="text-align: center;border-bottom: 1px solid #e4e4e4;padding: 5px 0;width: 100%;"><b>Account name: </b> {{ $data['acc_name'] }}</td>
                        </tr>
                        <tr style="height: 35px; width: 100%;">
                            <td style="text-align: center;border-bottom: 1px solid #e4e4e4;padding: 5px 0;width: 100%;"><b>Account number: </b> {{ $data['acc_number'] }}</td>
                        </tr>
                        <tr style="height: 35px; width: 100%;">
                            <td style="text-align: center;padding: 5px 0;width: 100%;"><b>Account sort code: </b> {{ $data['acc_sort_code'] }} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
