@component('mail::message')
# Invoice from {{ $franchisee->business_name ?? '-' }}

Thank you for your order!

Attached is your invoice **#INV-00{{ $invoice->id }}**.

To make a payment, please click the button below:

@component('mail::button', ['url' => $paymentUrl])
Pay Now
@endcomponent

If you have any questions, feel free to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

