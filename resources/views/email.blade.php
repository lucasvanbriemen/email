<x-email-layout :selectedFolder="$selectedFolder">
    @vite(['resources/css/email/email.scss', 'resources/js/theme.js'])


    <div class='option-bar'>
        <a href='/folder/{{ $selectedFolder }}' class='btn btn-secondary'>
            {!! App\Helpers\SvgHelper::svg('left-arrow') !!}
        </a>
    </div>

    Subject: {{ $email->subject }}<br>
    From: {{ $email->from }} - {{ $email->sender_email }}<br>
    To: {{ $email->to }}<br>

    {!! $email->html_body !!}
</x-email-layout>
