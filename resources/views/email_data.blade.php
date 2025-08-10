@vite(['resources/css/email/email.scss', 'resources/js/theme.js'])

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
        <h1 class='email-subject'>{{ $email->subject }}</h1>

        <div class='email-info'>
            <span class='email-from'>{{ $email->from }} {{ "<" . $email->sender_email . ">" }}</span> <br>  
            <span class='email-to'>To: {{ $email->to }}</span>
        </div>

        <div class='email-date'>

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

    @foreach ($attachments as $attachment)
        {{-- Every ICS attachment --}}
        @php
            $isIcs = strtolower(pathinfo($attachment->name, PATHINFO_EXTENSION)) === 'ics';
            if (!$isIcs) continue;

            $icsContent = $attachment->getContent();
            $parsedIcs = parseIcsContent($icsContent)[0];
        @endphp

        {{-- Dump the content --}}
        <pre>{{ $attachment->getContent()}}</pre>

        <div class='email-header @if ($email->is_starred) starred @endif'>
            <h1 class='email-subject'>{{ $parsedIcs["SUMMARY"] }}</h1>

            <div class='email-info'>
                <span class='email-from'>{{ $email->from }} {{ "<" . $email->sender_email . ">" }}</span> <br>  
                <span class='email-to'>To: {{ $email->to }}</span>
            </div>

            <div class='email-date'>

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
