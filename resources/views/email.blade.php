<x-email-layout class="email-page">
    @include('email_data', [
        'email' => $email,
        'attachments' => $attachments,
        'selectedProfile' => $selectedProfile,
        'selectedFolder' => $selectedFolder,
        'action_hint' => '/' . $selectedProfile->id . '/folder/' . $selectedFolder->path . '/mail/' . $email->uuid,
        'standalone' => true
    ])
</x-email-layout>
