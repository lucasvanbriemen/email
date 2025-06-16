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

@php
    if ($thread) {
        $data_uuid = $email['uuid'] . '-thread';
    }else {
        $data_uuid = $email['uuid'];
    }
@endphp

@php
    $context_menu_requirements = '';

    if ($email['has_read']) {
        $context_menu_requirements .= 'read ';
    } else {
        $context_menu_requirements .= 'unread ';
    }

    if ($email['is_starred'] == 1) {
        $context_menu_requirements .= 'starred ';
    } else {
        $context_menu_requirements .= 'unstarred ';
    }

    if ($thread) {
        $context_menu_requirements .= 'thread ';
    } else {
        $context_menu_requirements .= 'single-message ';
    }

    if ($email['is_archived'] == 1) {
        $context_menu_requirements .= 'archived ';
    } else {
        $context_menu_requirements .= 'not-archived ';
    }

    if ($email['is_deleted'] == 1) {
        $context_menu_requirements .= 'deleted ';
    } else {
        $context_menu_requirements .= 'not-deleted ';
    }
@endphp

<div class='{{ $class }} {{ $uuid }}' data-url='{{ $dataUrl }}' data-uuid='{{ $data_uuid }}' data-thread='{{ $thread ? 'true' : 'false' }}' data-context-menu='{{ $context_menu_requirements }}'>
    <p class='email-from'>
        {{ $email['from'] }}
        @if ($size != null)
            <span class='email-size'>({{ $size }})</span>
        @endif
    </p>
    <p class='email-subject'>{{ $email['subject'] }}</p>

    <p class='email-sent-at'>{{ date($format, strtotime($email['sent_at'])) }}</p>
</div>
