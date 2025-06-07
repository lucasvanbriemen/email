<x-email-layout :selectedFolder="$selectedFolder">

    @vite(['resources/css/email/overview.scss', 'resources/js/email/overview.js'])

    @php
        $last_iterated_date = null;
    @endphp
    @foreach ($emailThreads as $emailThread)

        @php
            $current_iteration_date = date("Y-m-d", strtotime($emailThread[0]['sent_at']));
        @endphp

        @if ($last_iterated_date != $current_iteration_date)
            <h2 class='email-date'>{{ date("D, d M Y", strtotime($emailThread[0]['sent_at'])) }}</h2>
            @php $last_iterated_date = $current_iteration_date; @endphp
        @endif

        <hr>
        @foreach ($emailThread as $email)
            <div class='message {{ $email['has_read'] ? 'read' : 'unread' }}' data-url='/folder/{{ $selectedFolder }}/mail/{{ $email->uuid }}'>
                <p class='email-from'>{{ $email['from'] }}</p>
                <p class='email-subject'>{{ $email['subject'] }}</p>
                <p class='email-sent-at'>{{ date("H:i", strtotime($email['sent_at'])) }}</p>
                <div class='quick-action-wrapper'>
                    @include('quick_actions', ['email' => $email, 'selectedFolder' => $selectedFolder])
                </div>
            </div>
        @endforeach
        <hr>
    @endforeach
</x-email-layout>