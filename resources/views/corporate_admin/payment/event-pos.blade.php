<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern POS System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f0f2f5;
        }

        .total-payment-card {
            max-width: 400px;
            margin: 30px auto;
            background-color: #f9f9f9;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px 30px;
            font-family: 'Segoe UI', sans-serif;
            color: #2c3e50;
            text-align: center;
        }

        .total-payment-card h2 {
            margin: 0 0 15px;
            font-size: 1.5rem;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }

        .total-payment-amount {
            font-size: 2rem;
            font-weight: bold;
            color: #27ae60;
            margin-top: 10px;
        }

        .pos-container {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .item-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .item-card:hover {
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        tr {
            border-bottom: 1px solid #f0f2f5;
        }

        td,
        th {
            padding: 12px;
            border: 1px solid #e2e8f0;
        }

        .item-label {
            font-weight: 600;
            color: #4a5568;
            width: 150px;
        }

        .item-value {
            color: #2563eb;
        }

        thead {
            background-color: #f8fafc;
        }

        th {
            text-align: left;
            color: #64748b;
            font-weight: 600;
            background-color: #edf2f7;
            width: 50%;
        }

        .pos-container {
            padding: 20px;
        }

        .items-grid {
            display: block;
        }

        .item-card {
            background: white;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
        }

        table {
            width: 100%;
            margin: 0;
            background: white;
        }
    </style>
</head>

<body>
    <div class="pos-container">
        <div class="items-grid">
            <div style="text-align: center; margin-bottom: 20px;">
                <img height="100px" src="{{ asset('assets/images/IMG_1298.png') }}" alt="">
            </div>
            <div style="font-size: 20px; text-align: center; margin-bottom: 10px;">Order ID:-
                OR-00{{ $eventTransaction->id }}</div>
            @php
                $totalPayment = 0;
            @endphp
            @foreach ($eventItems as $index => $eventItem)
                <div class="item-card">
                    <table>
                        <thead>
                            <tr>
                                <th>In Stock</th>
                                <th>Orderable</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Orderable
                                $orderable = \DB::table('fgp_order_details')
                                    ->where('id', $eventItem->orderable)
                                    ->first();
                                $fgpItem = isset($orderable->fgp_item_id)
                                    ? \App\Models\FgpItem::where('fgp_item_id', $orderable->fgp_item_id)->first()
                                    : null;

                                // InStock
                                $stock = isset($eventItem->in_stock)
                                    ? \App\Models\FgpItem::where('fgp_item_id', $eventItem->in_stock)->first()
                                    : null;

                                // Prices
                                $orderablePrice =
                                    isset($orderable->unit_number) && isset($orderable->unit_cost)
                                        ? $orderable->unit_number * $orderable->unit_cost
                                        : 0;
                                $inStockPrice =
                                    isset($eventItem->quantity) && isset($stock->case_cost)
                                        ? $eventItem->quantity * $stock->case_cost
                                        : 0;

                                // Add to total
                                $totalPayment += $orderablePrice + $inStockPrice;
                            @endphp

                            <tr>
                                <td>
                                    {{-- Orderable --}}
                                    <div class="item-label">Name:
                                        <span
                                            class="item-value">{{ isset($fgpItem->name) ? $fgpItem->name : 'N/A' }}</span>
                                    </div>
                                    <div class="item-label">Quantity:
                                        <span
                                            class="item-value">{{ isset($orderable->unit_number) ? $orderable->unit_number : 0 }}</span>
                                    </div>
                                    <div class="item-label">Price:
                                        <span class="item-value">
                                            ${{ number_format(isset($orderable->unit_number) && isset($orderable->unit_cost) ? $orderable->unit_number * $orderable->unit_cost : 0, 2) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    {{-- InStock --}}
                                    <div class="item-label">Name:
                                        <span class="item-value">{{ isset($stock->name) ? $stock->name : 'N/A' }}</span>
                                    </div>
                                    <div class="item-label">Quantity:
                                        <span
                                            class="item-value">{{ isset($eventItem->quantity) ? $eventItem->quantity : 0 }}</span>
                                    </div>
                                    <div class="item-label">Price:
                                        <span class="item-value">
                                            ${{ number_format(isset($stock->case_cost) && isset($eventItem->quantity) ? $eventItem->quantity * $stock->case_cost : 0, 2) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            @endforeach

            <div class="total-payment-card">
                <h2>Total Payment</h2>
                <div class="total-payment-amount">
                    ${{ number_format($totalPayment) }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
