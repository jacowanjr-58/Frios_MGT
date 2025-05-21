<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Frios Invoice with Tax and Customer Note</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #444;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    #logo {
      max-height: 80px;
    }

    .section {
      margin-bottom: 20px;
    }

    .addresses {
      display: flex;
      justify-content: space-between;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: #f4f4f4;
    }

    .totals {
      text-align: right;
      margin-top: 20px;
    }

    .totals table {
      width: auto;
      float: right;
    }

    .form-section {
      margin-top: 30px;
      clear: both;
    }

    textarea, input[type="number"] {
      font-size: 1rem;
      padding: 5px;
    }

    textarea {
      width: 100%;
      height: 80px;
      margin-top: 10px;
    }

    .tax-input {
      text-align: right;
    }
  </style>
</head>
<body>

<header>
  <h1>Invoice</h1>
</header>

<div class="section addresses">
  <div>
    <h3>From:</h3>
    <p>Frios Gourmet Pops<br>
      {{ $invoice->franchisee->business_name ?? '' }}<br>
      {{ $invoice->franchisee->address1 ?? '' }}<br>
      {{ $invoice->franchisee->city ?? '' }}, {{ $invoice->franchisee->state ?? '' }} {{ $invoice->franchisee->zip_code ?? '' }}</p>
  </div>
  <div>
    <h3>To:</h3>
    <p>{{ $invoice->customer->name ?? '' }}<br>
      {{ $invoice->customer->address1 ?? '' }}<br>
      {{ $invoice->customer->state ?? '' }} {{ $invoice->customer->zip_code ?? '' }}</p>
  </div>
</div>

<div class="section">
  <strong>Invoice #: </strong> INV-000{{ $invoice->id }}<br>
  <strong>Date: </strong> {{ $invoice->created_at->format('M d, Y') }}<br>
</div>

<table id="invoiceTable">
  <thead>
    <tr>
      <th>Description</th>
      <th>Qty</th>
      <th>Unit Price</th>
      <th>Taxable</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
    @php
        $subtotal = 0;
    @endphp
    @php
    $taxRate = $invoice->tax_price;
    $tax = 0;

    foreach ($invoiceItems as $invoiceItem) {
        if ($invoiceItem->taxable) {
            $itemTotal = $invoiceItem->quantity * $invoiceItem->unit_price;
            $tax += $itemTotal * ($taxRate / 100);
        }
    }
@endphp

    @foreach ($invoiceItems as $index=>$invoiceItem)
        @php
        $itemTotal = $invoiceItem->quantity * $invoiceItem->unit_price;
        $subtotal += $itemTotal; // Accumulating the subtotal value
    @endphp
    <tr>
      <td>{{ $invoiceItem->flavor->name ?? '' }} (Case of {{ $invoiceItem->quantity }})</td>
      <td>{{ $invoiceItem->quantity }}</td>
      <td>${{ number_format($itemTotal, 2) }}</td>
      <td><input type="checkbox" class="taxable" {{ $invoiceItem->taxable ? 'checked' : '' }} disabled></td>
      <td class="line-total">${{ number_format($invoiceItem->total_price, 2) }}</td>
    </tr>

    @endforeach
  </tbody>
</table>

<div class="tax-input">
  <label for="taxRate"><strong>Tax Rate (%):</strong></label>
  <input type="number" id="taxRate" value="{{ $invoice->tax_price }}" disabled min="0" max="100" step="0.01"  />
</div>

<div class="totals">
  <table>
    <tr>
      <td><strong>Subtotal:</strong></td>
      <td id="subtotal">${{ number_format($subtotal , 2) }}</td>
    </tr>
    <tr>
      <td><strong>Tax:</strong></td>
      <td id="tax">${{ number_format($tax , 2) }}</td>
    </tr>
    <tr>
      <td><strong>Total:</strong></td>
      <td id="total"><strong>${{ number_format($invoice->total_price , 2) }}</strong></td>
    </tr>
  </table>
</div>

@if ($invoice->note)
<div class="form-section">
  <label for="customerNote"><strong>Note to Customer:</strong></label>
  <textarea id="customerNote" placeholder="Add your message to the customer here..." disabled>{{ $invoice->note }}</textarea>
</div>
@endif

</body>
</html>
