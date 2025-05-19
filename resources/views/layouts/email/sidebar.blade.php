@vite(['resources/css/email/sidebar.scss', 'resources/js/email/sidebar.js'])

<div class='sidebar'>
    @foreach ($folders as $folder)
        <div class='folder @if ($folder->name == $selectedFolder->name) selected @endif'>
            <a href='/folder/{{ $folder->name }}'>
                {{ strtolower($folder->name) }}
            </a>
        </div>
    @endforeach
</div>
