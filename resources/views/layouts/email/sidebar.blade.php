<div class='sidebar-container'>
    <div class='menu-icon'>
        <span class='menu-icon-line'></span>
        <span class='menu-icon-line'></span>
        <span class='menu-icon-line'></span>
    </div>

    <div class='sidebar'>

        <div class='compose-email'>
            <span class='compose-mail-text'>Compose</span>
        </div>

        @foreach ($folders as $folder)
            <div class='folder @if ($folder->path == $selectedFolder->path) selected @endif'>
                <a href='/{{ $selectedProfile->id }}/folder/{{ $folder->path }}'>
                    {{ strtolower($folder->name) }}
                </a>
            </div>
        @endforeach


        <div class='account-selector'>
            @foreach ($profiles as $profile)
                <a class='account' href='/{{ $profile->id }}/folder/{{ $selectedFolder }}'>
                    <img src='{{ gravar($profile->email, 64) }}' alt='{{ $profile->email }}' class='avatar'>
                    <span class='text'>{{ $profile->name }}</span>
                </a>
            @endforeach

            <div class='settings'>
                <a href='/account'>
                    <span class='text'>Settings</span>
                </a>
            </div>
        </div>

        <div class='current-account'>
            <img src='{{ gravar($selectedProfile->email, 64) }}' alt='{{ $selectedProfile->email }}' class='avatar'>
            <span class='text'>{{ $selectedProfile->name }}</span>
        </div>
    </div>
</div>
