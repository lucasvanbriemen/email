<x-app-layout>
    
    @vite(['resources/css/dashboard.scss', 'resources/js/theme.js', 'resources/js/email/overview.js'])

    <div class='left-panel'>
        <div class='linked-accounts'>
            @foreach ($credentials as $credential)
                <a class='linked-account' href='/{{ $credential->id }}/folder/inbox'>
                    <img src='{{ gravar($credential->username, 64) }}'>
                    {{ $credential->username }}
                    <hr>
                </a>
            @endforeach
        </div>
        
        <div class='ai-overview'>
            {{ $ollama_response['response'] }}
        </div>
    </div>

    <div class='new-emails'>
        @foreach ($emails as $email)
            @foreach ($credentials as $credential)
                @if ($email->credential_id == $credential->id)
                    @php $email->from = $credential->username @endphp
                @endif
            @endforeach

            @include('email_listing', [
        'email' => $email,
        'class' => 'message',
        'uuid' => uniqid('email-'),
        'dataUrl' => '/' . $email->credential_id . '/folder/inbox/mail/' . $email->uuid,
    ])
        @endforeach
    </div>
</x-app-layout>