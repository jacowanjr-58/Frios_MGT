<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS System</title>
    <style>
        body {
            margin: 0;
            padding: 40px;
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            text-align: center; /* Center all content */
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .invoice-header h2 {
            color: #333;
            margin-bottom: 30px;
        }

        table {
            margin: 0 auto;
            width: 80%;
            border-collapse: collapse;
        }

        td {
            padding: 15px;
            vertical-align: top;
            text-align: center;
        }

        .label {
            font-size: 14px;
            color: #666;
        }

        .value {
            font-size: 16px;
            color: #333;
            font-weight: bold;
            margin-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            background: #e8f5e9;
            color: #2e7d32;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="invoice-header">
        {{-- <img height="100px" src="{{ asset('assets/images/Frios-Logo.webp') }}" alt=""> --}}
        <h2 style="margin-top: 10px;">{{ $expense->name }}</h2>
    </div>

    <table>
        <tr>
            <td>
                <div class="label">Category</div>
                <div class="value">{{ $expenseCategory->category }}</div>
            </td>
            <td>
                <div class="label">Sub Category</div>
                <div class="value">{{ $expenseSubCategory->sub_category }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Amount</div>
                <div class="value">${{ number_format($expenseTransaction->amount, 2) }}</div>
            </td>
            <td>
                <div class="label">Date</div>
                <div class="value">{{ date('d M Y', strtotime($expenseTransaction->created_at)) }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Order ID</div>
                <div class="value">OR-00{{ $expenseTransaction->id }}</div>
            </td>
            <td>
                <div class="label">Status</div>
                <div class="status-badge">{{ $expenseTransaction->stripe_status }}</div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
