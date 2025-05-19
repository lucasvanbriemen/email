<x-email-layout :selectedFolder="$selectedFolder">
    @foreach ($messages as $message)
        <a href='/folder/{{ $selectedFolder }}/mail/{{ $message->getUid() }}' class='message'>
            Subject: {{ $message->getSubject() }}<br>
            UID: {{ $message->getUid() }}<br><br>
        </a>
    @endforeach
</x-email-layout>