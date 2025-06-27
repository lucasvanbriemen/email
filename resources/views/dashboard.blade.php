<x-app-layout class="dashboard">

    @vite(['resources/css/dashboard.scss', 'resources/js/theme.js', 'resources/js/email/overview.js'])

    <div class='left-panel'>
        <div class='linked-accounts'>
            @foreach ($profiles as $profile)
                <a class='linked-account' href='/{{ $profile->linked_profile_count }}/folder/inbox'>
                    <img src='{{ gravar($profile->email, 64) }}'>
                    {{ $profile->name }}
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
            @include('email_listing', [
                'email' => $email,
                'class' => 'message',
                'uuid' => uniqid('email-'),
                'dataUrl' => '/' . $email->profile_id . '/folder/inbox/mail/' . $email->uuid,
                'is_fully_read' => $email->has_read
            ])
        @endforeach


        @if (count($emails) == 0)
            <div class='no-emails'>
                <h2>No new emails</h2>
                <p>Check back later for updates.</p>
            </div>
        @endif
    </div>
</x-app-layout>
