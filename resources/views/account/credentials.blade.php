<x-account-layout :profiles="$profiles" :selectedProfile="$selectedProfile">
    @foreach ($profiles as $profile)
        <div class="mb-4">
            {{ $profile->name }}
        </div>
    @endforeach


    <form action="/account/credentials" method="post">
        @csrf

        <input type='text' name='host' placeholder="{{ __('IMAP host') }}" required>
        <input type='text' name='port' placeholder="{{ __('IMAP Port') }}" required value='993'>
        <input type='text' name='protocol' placeholder="{{ __('IMAP Protocol') }}" required value='imap'>
        <input type='text' name='username' placeholder="{{ __('IMAP Username') }}" required>
        <input type='password' name='password' placeholder="{{ __('IMAP Password') }}" required>
        <input type='text' name='encryption' placeholder="{{ __('IMAP Encryption') }}" required value='ssl'>
        <input type='checkbox' name='validate_cert' placeholder="{{ __('IMAP Validate Cert') }}" required checked>

        <button type="submit" class="btn btn-primary">
            {{ __('Add IMAP Credential') }}
        </button>
    </form>

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