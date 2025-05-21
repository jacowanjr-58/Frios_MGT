<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order POS</title>
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
        }

        .item-list {
            list-style: none;
            padding-left: 20px;
            margin-top: 10px;
        }

        .cart-summary {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 280px;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .cart-summary:hover {
            transform: translateY(-5px);
        }

        .cart-summary h3 {
            margin-bottom: 15px;
            color: #2d3748;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .checkout-btn {
            width: 100%;
            padding: 12px;
            background: #4299e1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .checkout-btn:hover {
            background: #3182ce;
        }

        .pos-container {
            display: grid;
            grid-template-columns: 1fr;
            padding: 30px;
            min-height: 100vh;
        }

        .menu-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        .category-list {
            list-style: none;
        }

        .category-item {
            margin-bottom: 20px;
        }

        .category-header {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
            padding: 10px 15px;
            background: #edf2f7;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-header:hover {
            background: #e2e8f0;
        }

        .item {
            padding: 12px 15px;
            margin: 8px 0;
            background: #f8fafc;
            border-radius: 6px;

            color: #4a5568 {
                ;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .item:hover {
                background: #e2e8f0;
                transform: translateX(5px);
            }
    </style>
</head>

<body>

    <div class="pos-container">
        <div class="menu-section">
            <ul class="category-list">
                <li class="category-item">

                    <div class="category-header">{{ $franchisee->business_name }}
                        <div style="float: right">OR-00{{ $orderTransaction->id }}</div>
                    </div>
                    <ul class="item-list">
                        @php
                            $total = 0;
                        @endphp

                        @foreach ($orderDetails as $index => $item)
                            @php
                                $lineTotal = $item->unit_number * $item->unit_cost;
                                $total += $lineTotal;
                            @endphp
                            <li class="item">
                                {{ $item->fgp_item->name ?? '' }} x ({{ $item->unit_number }})
                                <div style="float: right">${{ number_format($lineTotal, 2) }}</div>
                            </li>
                        @endforeach


                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <div class="cart-summary">
        <h3>Total: ${{ number_format($total) }}</h3>
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
