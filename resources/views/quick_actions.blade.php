<a href='/folder/{{ $selectedFolder }}/mail/{{ $email->uid }}/archive'>
    {!! App\Helpers\SvgHelper::svg('archive') !!}
</a>

<a href='/folder/{{ $selectedFolder }}/mail/{{ $email->uid }}/delete'>
    {!! App\Helpers\SvgHelper::svg('bin') !!}
</a>