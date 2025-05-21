@component('mail::message')
# Expense Payment Confirmation

Hi {{ $corporateAdmin->name }},

A payment of **${{ number_format($expense->amount, 2) }}** was made for **{{ $expense->name }}**.

@component('mail::panel')
- **Date:** {{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}
@endcomponent

Please find the attached invoice PDF.

Thanks,
{{ config('app.name') }}
@endcomponent
