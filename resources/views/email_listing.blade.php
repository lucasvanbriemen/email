@php
    $class = '';
    $is_child = $is_child ?? false;
    $contextType = $contextType ?? 'single-message';
    $contextMenu = $contextType; // e.g., 'single-message' or 'single-message thread'

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

    if ($email['is_deleted']) {
        $contextMenu .= ' deleted';
    } else {
        $contextMenu .= ' not-deleted';
    }

    if ($email['is_archived'] ?? false) {
        $contextMenu .= ' archived';
    } else {
        $contextMenu .= ' not-archived';
    }

    if ($is_child) {
        $class .= ' child';
    }
@endphp

<div class='email-item {{ $class }}' data-email-id='{{ $email['uuid'] }}' data-path='{{ $pathToEmail }}' data-context-menu='{{ $contextMenu }}'>
    <div class='email-from'>

        {{-- {{ dump($email->sender) }} - {{ dump($email->sender_id) }} - {{ dump(App\Models\IncomingEmailSender::where('id', $email->sender_id)->first()) }} --}}

        <img src="/{{ $email->sender->logo_url }}" alt="{{ $email['from'] }}" class='email-avatar'>
        <span class='email-from-name'>{{ $email->getSenderDisplayName() }}</span>
    </div>
    <p class='email-subject'>{{ $email['subject'] }}</p>

    <p class='email-sent-at'>{{ readableTime($email['sent_at']) }}</p>
</div>
