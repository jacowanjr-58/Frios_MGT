@component('mail::message')
# Order Payment Confirmation

Hi {{ $franchiseeadmin->name }},

An order has been placed and is pending payment.

@component('mail::panel')
- **Order Date:** {{ \Carbon\Carbon::parse($order->date_transaction)->format('d M Y') }}
- **Order ID:** #{{ $order->id }}
@endcomponent

@component('mail::button', ['url' => $paymentUrl])
Pay Now
@endcomponent

Please find the attached invoice PDF for your reference.

Thanks,
{{ config('app.name') }}
@endcomponent
