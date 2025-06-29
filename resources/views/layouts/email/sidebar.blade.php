<div class='sidebar minimized'>

    <div class='compose-email'>
        {!! svg('plus') !!}
        <span class='compose-mail-text text'>Compose</span>
    </div>

    <hr>

    @foreach ($folders as $folder)
        <div class='folder @if ($folder->path == $selectedFolder->path) selected @endif' data-url='/{{ $selectedProfile->id }}/folder/{{ $folder->path }}/listing' data-folder='{{ $folder->path }}'>
            {!! svg($folder->icon ?? 'email') !!}
            <span class='text'>{{ $folder->name }}</span>
        </div>
    @endforeach


    <div class='profile-selector'>
        @foreach ($profiles as $profile)
            <a class='profile' href='/{{ $profile->id }}/folder/{{ $selectedFolder->path }}'>
                <img src='{{ gravar($profile->email, 64) }}' alt='{{ $profile->email }}' class='avatar'>
                <span class='text'>{{ $profile->name }}</span>
            </a>
        @endforeach

        <a href='/account' class='settings'>
            <span class='text'>Settings</span>
        </a>
    </div>

    <div class='current-profile'>
        <img src='{{ gravar($selectedProfile->email, 64) }}' alt='{{ $selectedProfile->email }}' class='avatar'>
        <span class='text'>{{ $selectedProfile->name }}</span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        emailSidebar.init();
    });
</script>
