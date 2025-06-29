@php 
    $class = '';

    if (!$email['has_read']) {
        $class = 'unread';
    }
@endphp

<div class='email-item {{ $class }}'>
    <div class='email-from'>
        <img src="{{ gravar($email['from'], 64) }}" alt="{{ $email['from'] }}" class='email-avatar'>
        {{-- {{ $email['from'] }} --}} Jonh doe
    </div>
    <p class='email-subject'>{{ $email['subject'] }}</p>

    <p class='email-sent-at'>{{ readableTime($email['sent_at']) }}</p>
</div>
