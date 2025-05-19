<x-email-layout :selectedFolder="$selectedFolder">
    @foreach ($messages as $message)
        <a href='/mail/{{ $message->getUid() }}'>
            Subject: {{ $message->getSubject() }}<br>
            UID: {{ $message->getUid() }}<br><br>
        </a>
    @endforeach
</x-email-layout>