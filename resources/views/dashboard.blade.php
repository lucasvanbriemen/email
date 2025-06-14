<x-app-layout>
    @foreach ($emails as $email)
        {{ $email->subject }}
    @endforeach
</x-app-layout>