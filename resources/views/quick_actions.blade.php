@vite(['resources/css/email/quick_actions.scss', 'resources/js/email/quick_actions.js'])

@props([
    'theard' => false,

    'selectedCredential' => null,
])

@php
    $archive = $theard ? 'archive_thread' : 'archive';
    $delete = $theard ? 'delete_thread' : 'delete';
@endphp

@csrf

<a data-url='/{{ $selectedCredential->id }}/folder/{{ $selectedFolder }}/mail/{{ $email->uuid }}/{{ $archive }}' data-function='archive' class='quick-action'>
    {!! App\Helpers\SvgHelper::svg('archive') !!}
</a>

<a data-url='/{{ $selectedCredential->id }}/folder/{{ $selectedFolder }}/mail/{{ $email->uuid }}/{{ $delete }}' data-function="delete" class='quick-action'>
    {!! App\Helpers\SvgHelper::svg('bin') !!}
</a>