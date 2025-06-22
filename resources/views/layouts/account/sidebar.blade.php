@vite(['resources/css/account/sidebar.scss'])

<div class='sidebar-container'>
    <div class='menu-icon'>
        <span class='menu-icon-line'></span>
        <span class='menu-icon-line'></span>
        <span class='menu-icon-line'></span>
    </div>

    <div class='sidebar'>

        @foreach ($profiles as $profile)
            <div class='profile @if ($profile->id == $selectedProfile->id) selected @endif'>
                <a href='/account/{{ $profile->linked_profile_count }}'>
                    {{ $profile->name }}
                </a>
            </div>
        @endforeach
    </div>
</div>