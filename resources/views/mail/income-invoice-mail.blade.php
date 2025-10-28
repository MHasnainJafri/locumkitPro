<x-mail::message>
To: {{ $data["supplier_name"] }}

Please find attached my invoice for my locum day(s) covered with yourselves.

Kind Regards,<br>
{{ $data["your_name"] }}
[{{ $data["your_email"] }}](mailto:{{ $data["your_email"] }})
</x-mail::message>