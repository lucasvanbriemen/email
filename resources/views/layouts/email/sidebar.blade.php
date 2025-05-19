@vite(['resources/css/email/sidebar.scss', 'resources/js/email/sidebar.js'])

<div class='sidebar'>
    @foreach ($folders as $folder)
        <div class='folder'>
            <a href='#' class='folder-link'>
                {{ $folder->name }}
            </a>
        </div>
    @endforeach
</div>
