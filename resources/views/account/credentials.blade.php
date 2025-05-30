<x-account-layout>
    @foreach ($imap_credentials as $credential)
        <div class="mb-4">
            {{ $credential->username }} 
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
    </x-account-layout>