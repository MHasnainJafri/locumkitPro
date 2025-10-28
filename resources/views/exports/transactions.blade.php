<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            padding: 10px 0;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .summary {
            margin: 20px 0;
            text-align: left;
        }
        .summary p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="header">
        <!--<h1>{{ $title }}</h1>-->
        <h1>Financial Transactions Report</h1>
    </div>

    <div class="summary">
        <!--<p><strong>Financial Year:</strong> {{ $financeyear }}</p>-->
        <p><strong>Total Income:</strong> {{ number_format($total_income, 2) }} USD</p>
        <p><strong>Total Expense:</strong> {{ number_format($total_expense, 2) }} USD</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Type</th>
                <th>Date</th>
                <th>Amount (USD)</th>
                <th>Category</th>
                <th>Bank Status</th>
                <th>Bank Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>{{ $record["#"] }}</td>
                    <td>{{ $record["Type"] }}</td>
                    <td>{{ $record["Date"] }}</td>
                    <td>{{ $record["Amount"] }}</td>
                    <td>{{ $record["Category"] }}</td>
                    <td>{{ $record["Banked"] }}</td>
                    <td>{{ $record["Bank Date"] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
