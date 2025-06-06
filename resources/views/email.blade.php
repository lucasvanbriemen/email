<x-email-layout :selectedFolder="$selectedFolder">
    @vite(['resources/css/email/email.scss', 'resources/js/theme.js'])

    <div class='quick-action-wrapper'>
        <a href='/folder/{{ $selectedFolder }}'>
            {!! App\Helpers\SvgHelper::svg('left-arrow') !!}
        </a>
        
        @include('quick_actions', ['email' => $email, 'selectedFolder' => $selectedFolder])
    </div>

    <div class='email-wrapper'>
        <div class='email-header'>
            <h1 class='email-subject'>{{ $email->subject }}</h1>

            <div class='email-info'>
                <span class='email-from'>{{ $email->from }}</span> <br>
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

        <iframe srcdoc="<style>body{font-family: sans-serif;}</style><base target='_top'>{{ $email->html_body }}" class='email-body' onload="resizeIframe(this)"></iframe>

        <script>
            function resizeIframe(iframe) {
                try {
                    const doc = iframe.contentDocument || iframe.contentWindow.document;
                    iframe.style.height = doc.documentElement.scrollHeight + 'px';
                } catch (e) {
                    console.error('Iframe resize failed:', e);
                }
            }
           </script>
    </div>
</x-email-layout>
