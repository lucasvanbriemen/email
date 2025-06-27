<x-email-layout :selectedFolder="$selectedFolder" :selectedProfile="$selectedProfile">

    @vite(['resources/css/email/overview.scss', 'resources/js/email/overview.js'])

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
                    'selectedCredential' => $selectedProfile,
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
                    '/' . $selectedProfile->linked_profile_count . '/folder/' . $selectedFolder->path . '/mail/' . $email['uuid'],
                'current_iteration_date' => $current_iteration_date,
                'selectedProfile' => $selectedProfile,
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
    @include('compose_email')

    @include('email_data',[ 
        'email' => $emailThreads[0][0] ?? null,
    ])
</x-email-layout>
