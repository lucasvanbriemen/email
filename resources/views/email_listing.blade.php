<div class='{{ $class }}' data-url='{{ $dataUrl }}'>
    <p class='email-from'>{{ $email['from'] }}</p>
    <p class='email-subject'>{{ $email['subject'] }}</p>
    <p class='email-sent-at'>{{ date("H:i", strtotime($email['sent_at'])) }}</p>
    @if ($quickAction)
        <div class='quick-action-wrapper'>
            @include('quick_actions', ['email' => $email, 'selectedFolder' => $selectedFolder])
        </div>
    @endif
</div>
