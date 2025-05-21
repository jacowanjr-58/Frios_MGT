<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <style>
        .footer {
    background-color: #f8f8f8;
    padding: 20px 0;
    text-align: center;
    font-family: Arial, sans-serif;
    font-size: 14px;
    color: #333;
    border-top: 1px solid #ddd;
    position: relative;
    bottom: 0;
    width: 100%;
}

.footer a {
    color: #0073e6;
    text-decoration: none;
    font-weight: bold;
}

.footer a:hover {
    text-decoration: underline;
}

.footer .copyright {
    margin: 0 auto;
}

        body {
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .invoice-header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #eee;
        }

        .invoice-header h2 {
            color: #333;
            margin: 0 0 20px 0;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            padding: 20px;
        }

        .detail-item {
            margin-bottom: 15px;
        }

        .detail-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            color: #333;
            font-weight: bold;
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
            <div style="text-align: center; margin-bottom: 20px;">
                <img height="100px" src="{{ asset('assets/images/IMG_1298.png') }}" alt="">
            </div>
            <h2 style="margin-top: 10px;">{{ $expense->name }}</h2>
            <div class="invoice-details">
                <div class="left-details">
                    <div class="detail-item">
                        <div class="detail-label">Category</div>
                        <div class="detail-value">{{ $expenseCategory->category }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Amount</div>
                        <div class="detail-value">${{ number_format($expenseTransaction->amount) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Order ID</div>
                        <div class="detail-value">OR-00{{ $expenseTransaction->id }}</div>
                    </div>
                </div>
                <div class="right-details">
                    <div class="detail-item">
                        <div class="detail-label">Sub Category</div>
                        <div class="detail-value">{{ $expenseSubCategory->sub_category }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Date</div>
                        <div class="detail-value">{{ date('d M Y', strtotime($expenseTransaction->created_at)) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="status-badge">{{ $expenseTransaction->stripe_status }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<footer class="footer">
    <div class="copyright">
        <p>
            Copyright © by
            <a href="https://friospops.com/" target="_blank">frios</a>
            <span class="current-year"></span>
        </p>
    </div>
</footer>

<script>
    document.querySelector('.current-year').textContent = new Date().getFullYear();
</script>

</body>
</html>
