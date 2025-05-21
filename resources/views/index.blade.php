<x-email-layout :selectedFolder="$selectedFolder">

    @vite('resources/css/email/index.scss')

    @php $last_iterated_date = null; @endphp
    @foreach ($messages as $message)
        @php
            $current_iteration_date = date("Y-m-d", strtotime($message['sent_at']));
        @endphp

        @if ($last_iterated_date != $current_iteration_date)
            <h2 class='email-date'>{{ date("D, d M Y", strtotime($message['sent_at'])) }}</h2>
            @php $last_iterated_date = $current_iteration_date; @endphp
        @endif

        <a href='/folder/{{ $selectedFolder }}/mail/{{ $message['uid'] }}' class='message {{ $message['has_read'] ? 'read' : 'unread' }}'>
            <p class='email-from'>{{ $message['from'] }}</p>
            <p class='email-subject'>{{ $message['subject'] }}</p>
            <p class='email-sent-at'>{{ date("H:i", strtotime($message['sent_at'])) }}</p>
        </a>

    @endforeach
</x-email-layout>