<x-app-layout class="dashboard">
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

        @php

            foreach ($profiles as $profile) {
                if ($profile->linked_profile_id == $email->linked_profile_id) {
                    $linked_profile_id = $profile->linked_profile_count;
                    break;
                }
            }

            $pathToEmail = route('mailbox.folder.mail', [
                'linked_profile_id' => $linked_profile_id,
                'folder' => 'inbox',
                'uuid' => $email->uuid,
            ]);
        @endphp


            @include('email_listing', [
                'email' => $email,
                'pathToEmail' => "/{$email['uuid']}/view",
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
