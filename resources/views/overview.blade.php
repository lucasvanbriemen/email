<x-email-layout :selectedFolder="$selectedFolder" :selectedCredential="$selectedCredential">

    @vite(['resources/css/email/overview.scss', 'resources/js/email/overview.js', 'resources/css/email/compose_email.scss', 'resources/js/email/compose_email.js'])

    @php
        $last_iterated_date = null;
    @endphp

    @if (count($emailThreads) == 0)
        <div class='no-emails'>
            <h2>No emails found here</h2>
            <p>Looks like your all cought up</p>
        </div>
    @endif

    @foreach ($emailThreads as $emailThread)
        @php
            $current_iteration_date = date('Y-m-d', strtotime($emailThread[0]['sent_at']));
        @endphp

        @if ($last_iterated_date != $current_iteration_date)
            <h2 class='email-date'>{{ date('D, d M Y', strtotime($emailThread[0]['sent_at'])) }}</h2>
            @php $last_iterated_date = $current_iteration_date; @endphp
        @endif


        @if (count($emailThread) > 1)
            <?php $uuid = uniqid('thread-'); ?>
            <div class='email-thread {{ $uuid }} '>
                @include('email_listing', [
                    'email' => $emailThread[0],
                    'class' =>
                        'thead-top-message ' .
                        (in_array(false, array_column($emailThread, 'has_read')) ? 'unread' : 'read') .
                        ' ' .
                        ($emailThread[0]['is_starred'] == 1 ? ' starred' : 'unstarred'),
                    'current_iteration_date' => $current_iteration_date,
                    'size' => count($emailThread),
                    'thread' => true,
                    'selectedCredential' => $selectedCredential,
                    'uuid' => $uuid,
                    'is_fully_read' => !in_array(false, array_column($emailThread, 'has_read')),
                ])
        @endif

        @foreach ($emailThread as $email)
            @include('email_listing', [
                'email' => $email,
                'class' =>
                    'message ' .
                    ($email['has_read'] ? 'read' : 'unread') .
                    ' ' .
                    ($email['is_starred'] == 1 ? ' starred' : 'unstarred'),
                'dataUrl' =>
                    '/' . $selectedCredential->id . '/folder/' . $selectedFolder . '/mail/' . $email['uuid'],
                'current_iteration_date' => $current_iteration_date,
                'selectedCredential' => $selectedCredential,
                'thead' => false,
                'uuid' => uniqid('message-'),
                'is_fully_read' => !in_array(false, array_column($emailThread, 'has_read')),
            ])
        @endforeach

        @if (count($emailThread) > 1)
            </div>
        @endif
    @endforeach

    @include('context_menu')

    <div class="compose-email-wrapper">
        <div class="compose-email-background"></div>

        <div class="compose-email">
            <form id="compose-email-form" method="POST" action="/test">
                @csrf
                <input type="text" name="to" value="" placeholder="To">
                <input type="text" name="cc" value="" placeholder="Cc">
                <input type="text" name="bcc" value="" placeholder="Bcc">
                <input type="text" name="reply_to" value="" placeholder="Reply To">
                <input type="text" name="reply_to_all" value="" placeholder="Reply To All">
                <input type="text" name="subject" value="" placeholder="Subject">
                <textarea name="body" placeholder="Body"></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</x-email-layout>
