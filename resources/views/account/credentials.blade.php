<x-account-layout :profiles="$profiles" :selectedProfile="$selectedProfile">

@vite(['resources/css/account/credentials.scss'])


    <form action="/account/{{ $selectedProfile->linked_profile_count }}/imap" method="post">
        <h2>{{ __('Credentials') }}</h2>

        @csrf
        <x-input type="text" name="host" label="{{ __('IMAP host') }}" :value="$imapCredentials->host" required />
        <x-input type="text" name="port" label="{{ __('IMAP Port') }}" :value="$imapCredentials->port" required />
        <x-input type="text" name="protocol" label="{{ __('IMAP Protocol') }}" :value="$imapCredentials->protocol" required />
        <x-input type="text" name="username" label="{{ __('IMAP Username') }}" :value="$imapCredentials->username" required />
        <x-input type="password" name="password" label="{{ __('IMAP Password') }}" :value="$imapCredentials->password" required />
        <x-input type="text" name="encryption" label="{{ __('IMAP Encryption') }}" :value="$imapCredentials->encryption" required />
        <button type="submit" class="btn btn-primary">
            {{ __('Update IMAP Credential') }}
        </button>
    </form>

    <form action="/account/{{ $selectedProfile->linked_profile_count }}/smtp" method="post">
        <h2>{{ __('SMTP Credentials') }}</h2>
        @csrf

        <x-input type="text" name="host" label="{{ __('SMTP Host') }}" value="{{ $smtpCredentials->host }}" required />
        <x-input type="text" name="port" label="{{ __('SMTP Port') }}" value="{{ $smtpCredentials->port }}" required />
        <x-input type="text" name="username" label="{{ __('SMTP Username') }}" value="{{ $smtpCredentials->username }}" required />
        <x-input type="password" name="password" label="{{ __('SMTP Password') }}" value="{{ $smtpCredentials->password }}" required />
        <x-input type="text" name="reply_to_email" label="{{ __('SMTP Reply To Email') }}" value="{{ $smtpCredentials->reply_to_email }}" required />
        <x-input type="text" name="reply_to_name" label="{{ __('SMTP Reply To Name') }}" value="{{ $smtpCredentials->reply_to_name }}" required />
        <x-input type="text" name="from_name" label="{{ __('SMTP From Name') }}" value="{{ $smtpCredentials->from_name }}" required />
        <x-input type="text" name="from_email" label="{{ __('SMTP From Email') }}" value="{{ $smtpCredentials->from_email }}" required />

        <button type="submit" class="btn btn-primary">
            {{ __('Update SMTP Credential') }}
        </button>
    </form>

    @foreach ($tags as $tag)
        <form action="/account/{{ $selectedProfile->linked_profile_count }}/tags/{{ $tag->id }}" method="post" class="delete-tag-form" style="background-color: {{ $tag->color }}">
            <h2>{{ __('Tag') }}: {{ $tag->name }}</h2>
        </form>
    @endforeach

</x-account-layout>