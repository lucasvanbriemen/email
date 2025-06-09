@vite(['resources/css/email/quick_actions.scss', 'resources/js/email/quick_actions.js'])

@props([
    'theard' => false,

    'selectedCredential' => null,
    'action' => null,
    'action_hint' => null
])

@php
$archive = $theard ? 'archive_thread' : 'archive';
$delete = $theard ? 'delete_thread' : 'delete';
@endphp

@csrf

<a data-url='/{{ $selectedCredential->id }}/folder/{{ $selectedFolder }}/mail/{{ $email->uuid }}/{{ $archive }}' data-action='{{ $action }}' data-action-hint='{{ $action_hint }}' class='quick-action'>
    {!! App\Helpers\SvgHelper::svg('archive') !!}
</a>

<a data-url='/{{ $selectedCredential->id }}/folder/{{ $selectedFolder }}/mail/{{ $email->uuid }}/{{ $delete }}' data-action='{{ $action }}' data-action-hint='{{ $action_hint }}' class='quick-action'>
    {!! App\Helpers\SvgHelper::svg('bin') !!}
</a>