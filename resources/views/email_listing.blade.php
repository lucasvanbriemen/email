@php
    $class = '';
    $contextMenu = 'single-message';

    if (!$email['has_read']) {
        $class = 'unread';
        $contextMenu .= ' unread';
    } else {
        $contextMenu .= ' read';
    }

    if ($email['is_starred']) {
        $class .= ' starred';
        $contextMenu .= ' starred';
    } else {
        $contextMenu .= ' unstarred';
    }

@endphp

<div class='email-item {{ $class }}' data-email-id='{{ $email['uuid'] }}' data-path='{{ $pathToEmail }}' data-context-menu='{{ $contextMenu }}'>
    <div class='email-from'>
        <img src="{{ gravar($email['from'], 64) }}" alt="{{ $email['from'] }}" class='email-avatar'>
        {{ $email['from'] }}
    </div>
    <p class='email-subject'>{{ $email['subject'] }}</p>

    <p class='email-sent-at'>{{ readableTime($email['sent_at']) }}</p>
</div>