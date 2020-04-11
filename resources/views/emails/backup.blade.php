@component('mail::message')
    # Backup Transfer

    The file {{ $backup->name }} was sent to you as attachment by our backup system.

    Regards,
    {{ config('app.name') }}
@endcomponent