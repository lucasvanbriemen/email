@vite(['resources/css/email/email_listing.scss'])

@php $format = 'D, d M Y'; @endphp
@if($current_iteration_date == date("Y-m-d", strtotime($email['sent_at'])))
    @php $format = 'H:i'; @endphp
@endif


<div class='{{ $class }}' data-url='{{ $dataUrl }}'>
    <p class='email-from'>{{ $email['from'] }}</p>
    <p class='email-subject'>{{ $email['subject'] }}</p>
    <p class='email-sent-at'>{{ date($format, strtotime($email['sent_at'])) }}</p>
    @if ($quickAction)
        <div class='quick-action-wrapper'>
            @include('quick_actions', ['email' => $email, 'selectedFolder' => $selectedFolder])
        </div>
    @endif
</div>
