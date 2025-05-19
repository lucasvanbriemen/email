<x-email-layout>
    @foreach($folders as $folder)
        <strong>{{ $folder->name }}</strong><br>

        @foreach ($messages as $messageSet)
            @foreach ($messageSet as $msg)
                @if ($msg->getFolderPath() == $folder->path)
                    <a href='/mail/{{ $msg->getUid() }}'>
                        Subject: {{ $msg->getSubject() }}<br>
                        UID: {{ $msg->getUid() }}<br><br>
                    </a>
                @endif
            @endforeach
        @endforeach
    @endforeach

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</x-email-layout>