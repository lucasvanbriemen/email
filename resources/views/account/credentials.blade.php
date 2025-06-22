<x-account-layout :profiles="$profiles" :selectedProfile="$selectedProfile">

    <h1>{{ __('Credentials') }}</h1>

    <form action="/account/{{ $selectedProfile->linked_profile_count }}/imap" method="post">
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

    <hr>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <hr>
    <h2>{{ __('SMTP Credentials') }}</h2>

    <form action="/account/smtp_credentials" method="post">
        @csrf

        {{-- <select name='imap_credential_id' required>
            <option value=''>{{ __('Select IMAP Credential') }}</option>
            @foreach ($imap_credentials as $credential)
                <option value='{{ $credential->id }}'>{{ $credential->username }}</option>
            @endforeach
        </select> --}}

        <input type='text' name='host' placeholder="{{ __('SMTP host') }}" required >
        <input type='text' name='port' placeholder="{{ __('SMTP port') }}" required >
        <input type='text' name='username' placeholder="{{ __('SMTP username') }}" required>
        <input type='password' name='password' placeholder="{{ __('SMTP password') }}" required>
        <input type='text' name='reply_to_email' placeholder="{{ __('SMTP reply_to_email') }}" required>
        <input type='text' name='reply_to_name' placeholder="{{ __('SMTP reply_to_name') }}" required>
        <input type='text' name='from_name' placeholder="{{ __('SMTP from_name') }}" required checked>
        <input type='text' name='from_email' placeholder="{{ __('SMTP from_email') }}" required checked>

        <button type="submit" class="btn btn-primary">
            {{ __('Add SMTP Credential') }}
        </button>
    </form>

</x-account-layout>