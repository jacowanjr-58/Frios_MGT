@component('mail::message')
# Event Payment Confirmation

Hi {{ $franchisee->name }},

We have successfully processed the payment for your event: **{{ $eventTransaction->event->event_name }}**.

@component('mail::panel')
- **Event Name:** {{ $eventTransaction->event->event_name }}
- **Event Date:** {{ \Carbon\Carbon::parse($eventTransaction->event->start_date)->format('d M Y') }} to {{ \Carbon\Carbon::parse($eventTransaction->event->end_date)->format('d M Y') }}
- **Amount Paid:** ${{ number_format($eventTransaction->amount, 2) }}
@endcomponent

Please find the attached invoice PDF for your reference.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
