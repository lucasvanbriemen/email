<x-email-layout :selectedFolder="$selectedFolder">

    @vite('resources/css/email/index.scss')

    @foreach ($messages as $message)
        <a href='/folder/{{ $selectedFolder }}/mail/{{ $message['uid'] }}' class='message {{ $message['has_read'] ? 'read' : 'unread' }}'>
            <p class='email-from'>{{ $message['from'] }}</p>
            <p class='email-subject'>{{ $message['subject'] }}</p>
            <p class='email-sent-at'>{{ $message['sent_at'] }}</p>
        </a>

    @endforeach
</x-email-layout>