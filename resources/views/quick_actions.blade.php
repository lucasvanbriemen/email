@vite(['resources/css/email/quick_actions.scss', 'resources/js/email/quick_actions.js'])

@props([
    'theard' => false,

    'selectedCredential' => null,
    'action' => null,
    'action_hint' => null
])

@csrf

@php 
$parrent_folder_url = '/' . $selectedCredential->id . '/folder/' . $selectedFolder;
@endphp

<a href='{{ $parrent_folder_url }}'>
    {!! App\Helpers\SvgHelper::svg('left-arrow') !!}
</a>

<a data-url='{{ $parrent_folder_url }}/mail/{{ $email->uuid }}/archive' data-action='go_back' data-action-hint='{{ $parrent_folder_url }}' class='quick-action'>
    {!! App\Helpers\SvgHelper::svg('archive') !!}
</a>

<a data-url='{{ $parrent_folder_url }}/mail/{{ $email->uuid }}mail/{{ $email->uuid }}/star' data-action='add_class' data-action-hint='stared' class='quick-action no-fill'>
    {!! App\Helpers\SvgHelper::svg('star') !!}
</a>

<a data-url='{{ $parrent_folder_url }}/mail/{{ $email->uuid }}mail/{{ $email->uuid }}/delete' data-action='go_back' data-action-hint='{{ $parrent_folder_url }}' class='quick-action'>
    {!! App\Helpers\SvgHelper::svg('bin') !!}
</a>