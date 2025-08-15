<x-app-layout class="dashboard">
    <div class='left-panel'>
        @foreach ($profiles as $profile)
            <a class='linked-profile' href='/{{ $profile->linked_profile_count }}/folder/inbox'>
                <img src='{{ gravar($profile->email, 64) }}'>
                <div class='profile-text'>
                    <span class='profile-name'>{{ $profile->name }}</span>
                    <span class='profile-email'>{{ $profile->email }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <div class='right-panel new-emails'>
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
                'pathToEmail' => $pathToEmail,
            ])
        @endforeach

        @if (count($emails) == 0)
            <div class='no-emails'>
                <h2>No new emails</h2>
                <p>Check back later for updates.</p>
            </div>
        @endif

        <div class="last-update">
            <p>Last email fetched at: {{ readableTime($last_update) }}</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            dashboard.init();
        });
    </script>

</x-app-layout>
