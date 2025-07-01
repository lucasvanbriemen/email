@php
    $class = '';

    if (!$email['has_read']) {
        $class = 'unread';
    }
@endphp

<div class='email-item {{ $class }}' data-email-id='{{ $email['uuid'] }}' data-path='{{ $pathToEmail }}'>
    <div class='email-from'>
        <img src="{{ gravar($email['from'], 64) }}" alt="{{ $email['from'] }}" class='email-avatar'>
        {{ $email['from'] }}
    </div>
    <p class='email-subject'>{{ $email['subject'] }}</p>

    <p class='email-sent-at'>{{ readableTime($email['sent_at']) }}</p>
</div>