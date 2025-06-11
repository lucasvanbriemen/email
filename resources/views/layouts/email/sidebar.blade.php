@vite(['resources/css/email/sidebar.scss', 'resources/js/email/sidebar.js'])

<div class='sidebar-container'>
    <div class='menu-icon'>
        <span class='menu-icon-line'></span>
        <span class='menu-icon-line'></span>
        <span class='menu-icon-line'></span>
    </div>

    <div class='sidebar'>
        @foreach ($folders as $folder)
            <div class='folder @if ($folder->path == $selectedFolder) selected @endif'>
                <a href='/{{ $selectedCredential->id }}/folder/{{ $folder->path }}'>
                    {{ strtolower($folder->name) }}
                </a>
            </div>
        @endforeach


        <div class='account-selector'>
            @foreach ($imapCredentials as $credential)
                <a class='account' href='/{{ $credential->id }}/folder/{{ $selectedFolder }}'>
                    <img src='{{ \App\Helpers\GravatarHelper::gravar($credential->username, 64) }}' alt='{{ $credential->username }}' class='avatar'>
                    <span class='text'>{{ $credential->username }}</span>
                </a>
            @endforeach

            <div class='settings'>
                <a href='/account'>
                    <span class='text'>Settings</span>
                </a>
            </div>
        </div>

        <div class='current-account'>
            <img src='{{ \App\Helpers\GravatarHelper::gravar($selectedCredential->username, 64) }}' alt='{{ $selectedCredential->username }}' class='avatar'>
            <span class='text'>{{ $selectedCredential->username }}</span>
        </div>
    </div>
</div>
