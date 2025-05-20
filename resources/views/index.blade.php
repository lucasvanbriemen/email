<x-email-layout :selectedFolder="$selectedFolder">

    @vite('resources/css/email/index.scss')

    @foreach ($messages as $message)
        <a href='/folder/{{ $selectedFolder }}/mail/{{ $message['uid'] }}' class='message'>
            Subject: {{ $message['subject'] }} <br>
            from: {{ $message['from'] }} <br>
            read: {{ $message['has_read'] ? 'yes' : 'no' }} <br>
            sent_at: {{ $message['sent_at'] }} <br>
        </a>

        <hr>

    @endforeach
</x-email-layout>