@component('mail::message')
# Order Payment Confirmation

Hi {{ $corporateAdmin->name }},

Your payment for the has been successfully processed.

@component('mail::panel')
- **Order Date:** {{ \Carbon\Carbon::parse($order->date_transaction)->format('d M Y') }}
@endcomponent

Please find the attached invoice PDF for your reference.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
