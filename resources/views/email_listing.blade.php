@vite(['resources/css/email/email_listing.scss'])

@props(
    [
        'email',
        'class' => '',
        'dataUrl' => '',
        'context_menu' => true,
        'current_iteration_date' => null,
        'size' => null,
        'thread' => false,
        'selectedCredential' => null,
        'uuid' => uniqid('email-')
    ]
)

@php $format = 'D, d M Y'; @endphp
@if($current_iteration_date == date("Y-m-d", strtotime($email['sent_at'])))
    @php $format = 'H:i'; @endphp
@endif

<div class='{{ $class }} {{ $uuid }}' data-url='{{ $dataUrl }}' data-uuid='{{ $email['uuid'] }}' data-thread='{{ $thread ? 'true' : 'false' }}'>
    <p class='email-from'>
        {{ $email['from'] }}
        @if ($size != null)
            <span class='email-size'>({{ $size }})</span>
        @endif
    </p>
    <p class='email-subject'>{{ $email['subject'] }}</p>

    <p class='email-sent-at'>{{ date($format, strtotime($email['sent_at'])) }}</p>
</div>
