<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
    <style>
        body {
            font-family: sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Expense Report</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Expense Name</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $index => $expense)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $expense->expense_name }}</td>
                    <td>{{ $expense->amount }}</td>
                    <td>{{ $expense->expense_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>