@vite(['resources/css/email/quick_actions.scss', 'resources/js/email/quick_actions.js'])

@props([
    'selectedProfile' => null,
])

@csrf

@php
    $parrent_folder_url = '/' . $selectedProfile->id . '/folder/' . $selectedFolder->path;
@endphp

<a href='{{ $parrent_folder_url }}'>
    {!! svg('left-arrow') !!}
</a>

<a data-url='{{ $parrent_folder_url }}/mail/{{ $email->uuid }}/archive' data-action='go_back'
    data-action-hint='{{ $parrent_folder_url }}' class='quick-action'>
    {!! svg('archive') !!}
</a>

<a data-url='{{ $parrent_folder_url }}/mail/{{ $email->uuid }}/star' data-action='add_class' data-action-hint='starred'
    class='quick-action no-fill'>
    {!! svg('star') !!}
</a>

<a data-url='{{ $parrent_folder_url }}/mail/{{ $email->uuid }}/delete' data-action='go_back'
    data-action-hint='{{ $parrent_folder_url }}' class='quick-action'>
    {!! svg('bin') !!}
</a>
