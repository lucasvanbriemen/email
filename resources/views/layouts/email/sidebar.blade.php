<div class='sidebar minimized'>

    <div class='compose-email'>
        <a href='#'>
            {!! svg('plus') !!}
            <span class='compose-mail-text text'>Compose</span>
        </a>
    </div>

    <hr>

    @foreach ($folders as $folder)
        <div class='folder @if ($folder->path == $selectedFolder->path) selected @endif'>
            <a href='/{{ $selectedProfile->id }}/folder/{{ $folder->path }}'>
                {!! svg($folder->icon ?? 'email') !!}
                <span class='text'>{{ $folder->name }}</span>
            </a>
        </div>
    @endforeach


    <div class='profile-selector'>
        @foreach ($profiles as $profile)
            <a class='profile' href='/{{ $profile->id }}/folder/{{ $selectedFolder }}'>
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
