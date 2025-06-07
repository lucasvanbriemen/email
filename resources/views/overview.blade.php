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


        @if (count($emailThread) > 1)
            <div class='email-thread'>
                @include('email_listing', [
                    'email' => $emailThread[0],
                    'class' => 'thead-top-message ' . (in_array(false, array_column($emailThread, 'has_read')) ? 'unread' : 'read'),
                    'dataUrl' => '',
                    'quickAction' => false,
                    'current_iteration_date' => $current_iteration_date,
                    'size' => count($emailThread),
                ])
        @endif

        @foreach ($emailThread as $email)
            @include('email_listing', [
                'email' => $email,
                'class' => 'message ' . ($email['has_read'] ? 'read' : 'unread'),
                'dataUrl' => '/folder/' . $selectedFolder . '/mail/' . $email['uuid'],
                'quickAction' => true,
                'current_iteration_date' => $current_iteration_date,
                'size' => null,
            ])
        @endforeach

        @if (count($emailThread) > 1)
            </div>
        @endif
    @endforeach
</x-email-layout>