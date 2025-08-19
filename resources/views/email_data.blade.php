@vite(['resources/css/email/email.scss', 'resources/js/theme.js'])
<script type="text/javascript" src="https://cdn.addevent.com/libs/atc/1.6.1/atc.min.js" async defer></script>

<div class='quick-action-wrapper'>
    @include('quick_actions', [
        'email' => $email,
        'selectedFolder' => $selectedFolder,
        'selectedProfile' => $selectedProfile,
        'action' => 'go_back_to_folder',
        'action_hint' => '/' . $selectedProfile->id . '/folder/' . $selectedFolder,
    ])
</div>

<input type='hidden' id='standalone_email' value='{{ $standalone ? 'true' : 'false' }}'>

<div class='email-wrapper'>
    <div class='email-header @if ($email->is_starred) starred @endif'>
        <h1 class='subject'>{{ $email->subject }}</h1>

        <div class='info'>
            <span class='from'>{{ $email->from }} {{ "<" . $email->sender_email . ">" }}</span> <br>  
            <span class='to'>To: {{ $email->to }}</span>
        </div>

        <div class='date'>

            @php
                $format = 'D d M, H:i';
                $send_at = $email->send_at;

                // If the date is today, show only the time
                if (date('Y-m-d') === $email->created_at->format('Y-m-d')) {
                    $format = 'H:i';
                }
            @endphp

            {{ $email->created_at->format($format) }}
        </div>
    </div>

    @if (!empty($threadChildren) && count($threadChildren) > 0)
        <div class='relevant-messages'>
            <div class='header'>Relevant messages ({{ count($threadChildren) }})</div>
            <ul class='list'>
                @foreach ($threadChildren as $child)
                    @php
                        $childPath = route('mailbox.folder.mail', [
                            'linked_profile_id' => $selectedProfile->linked_profile_count ?? $selectedProfile->id,
                            'folder' => $selectedFolder->path,
                            'uuid' => $child->uuid,
                        ]);
                    @endphp
                    <li>
                        <a href='{{ $childPath }}' onclick="event.preventDefault(); emailListing.openEmail(document.querySelector(`.email-item[data-email-id='{{ $child->uuid }}']`) ?? { dataset: { path: '{{ $childPath }}' }, classList: { add(){}, remove(){} } });">
                            <span class='subject'>{{ $child->subject ?: 'No Subject' }}</span>
                            <span class='from'>{{ $child->from }}</span>
                            <span class='time'>{{ readableTime($child->sent_at) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @foreach ($attachments as $attachment)
        {{-- Every ICS attachment --}}
        @php
            $isIcs = strtolower(pathinfo($attachment->name, PATHINFO_EXTENSION)) === 'ics';
            if (!$isIcs) continue;

            $icsContent = $attachment->getContent();
            $parsedIcs = parseIcsContent($icsContent)[0];
            $parsedIcs["DTSTART"] =  \Carbon\Carbon::createFromFormat('Ymd\THis\Z', $parsedIcs["DTSTART"] ?? '')->setTimezone('Europe/Amsterdam')->toDateTimeString();
            $parsedIcs["DTEND"] =  \Carbon\Carbon::createFromFormat('Ymd\THis\Z', $parsedIcs["DTEND"] ?? '')->setTimezone('Europe/Amsterdam')->toDateTimeString();
        @endphp

        <div class='ics-header'>
            <h1 class='subject'>{{ $parsedIcs["SUMMARY"] }}</h1>

            <div class='info'>
                {{ readableTime($parsedIcs["DTSTART"]) }} until {{ readableTime($parsedIcs["DTEND"]) }} <br>

                @foreach ($parsedIcs["ATTENDEE"] as $attendee)
                    <span class='attendee'>{{ str_ireplace('mailto:', '', $attendee) }}</span><br>
                @endforeach
            </div>

            <div title="Add to Calendar" class="addeventatc">
                Add to Calendar
                <span class="start">{{ $parsedIcs["DTSTART"] ?? '' }}</span>
                <span class="end">{{ $parsedIcs["DTEND"] ?? '' }}</span>
                <span class="timezone">Europe/Amsterdam</span>
                <span class="title">{{ $parsedIcs["SUMMARY"] ?? '' }}</span>
                <span class="description">{{ $parsedIcs["DESCRIPTION"] ?? '' }}</span>
                <span class="location">{{ $parsedIcs["LOCATION"] ?? '' }}</span>
            </div>
        </div>
    @endforeach

    <iframe srcdoc="<style>body{font-family: sans-serif;}</style><base target='_top'>{{ $email->html_body }}" class='email-body' onload="email.init()"></iframe>

    <div class='email-attachments'>
        @foreach ($attachments as $attachment)
            @php
                $IMG_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'png', 'svg'];
                $isImage = in_array(strtolower(pathinfo($attachment->name, PATHINFO_EXTENSION)), $IMG_EXTENSIONS);

                // Exclude any .ics files since we render it at the top
                $isIcs = strtolower(pathinfo($attachment->name, PATHINFO_EXTENSION)) === 'ics';
                if ($isIcs) {
                    continue;
                }
            @endphp
            <a class='email-attachment' href='/{{ $attachment->path }}' target='_blank' rel='noopener noreferrer'>
                @if ($isImage)
                    <img src="/{{ $attachment->path }}" alt="{{ $attachment->name }}" class='attachment-image'>
                @else
                    <span class='attachment-name'>{{ $attachment->name }}</span>
                @endif
            </a>
        @endforeach
    </div>
</div>
