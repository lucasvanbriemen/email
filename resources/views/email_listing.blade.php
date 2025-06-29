<div class='email-item'>
    <div class='email-from'>

        <img src="{{ gravar($email['from'], 64) }}" alt="{{ $email['from'] }}" class='email-avatar'>
        {{ $email['from'] }}
    </div>
    <p class='email-subject'>{{ $email['subject'] }}</p>

    <p class='email-sent-at'>{{ date('d/m/Y H:i:s', strtotime($email['sent_at'])) }}</p>
</div>
