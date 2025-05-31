<x-email-layout>

    @vite(['resources/css/email/email.scss', 'resources/js/theme.js'])

    Subject: {{ $email->subject }}<br>
    From: {{ $email->from }} - {{ $email->sender_email }}<br>
    To: {{ $email->to }}<br>

    {!! $email->html_body !!}
</x-email-layout>
