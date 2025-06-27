<x-email-layout :selectedFolder="$selectedFolder" :selectedProfile="$selectedProfile">
    @include('email_data', [
        'email' => $email,
        'attachments' => $attachments,
        'selectedProfile' => $selectedProfile,
        'selectedFolder' => $selectedFolder,
        'action_hint' => '/' . $selectedProfile->id . '/folder/' . $selectedFolder->path . '/mail/' . $email->uuid,
    ])
</x-email-layout>
