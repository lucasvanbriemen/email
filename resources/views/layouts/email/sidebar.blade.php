@vite(['resources/css/email/sidebar.scss', 'resources/js/email/sidebar.js'])

<div class='sidebar-container'>
    <div class='menu-icon'>
        <span class='menu-icon-line'></span>
        <span class='menu-icon-line'></span>
        <span class='menu-icon-line'></span>
    </div>

    <div class='sidebar'>
        {{-- Menu icon --}}
        @foreach ($folders as $folder)
            <div class='folder @if ($folder->path == $selectedFolder) selected @endif'">
                <a href='/folder/{{ $folder->path }}'>
                    {{ strtolower($folder->name) }}
                </a>
            </div>
        @endforeach


        <a href='/account' class='account'>
            <img src='{{ \App\Helpers\GravatarHelper::gravar(auth()->user()->email, 64) }}' alt='{{ auth()->user()->name }}' class='avatar'>
            <span class='text'>{{ auth()->user()->name }}</span>
        </a>
    </div>
</div>
