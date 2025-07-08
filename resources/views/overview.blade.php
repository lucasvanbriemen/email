<x-email-layout class="overview-page">
    @php
        $last_iterated_date = null;
    @endphp

    <div class='listing-wrapper'>
        <div class='listing-header'>
            <h1 class='current-folder-name'>{{ $selectedFolder->name }}</h1>
            <h1 class='total-email-count'>{{ $totalEmailCount }}</h1>
            <h1 class='current-min'>{{ $currentMin }}</h1>
            <h1 class='current-max'>{{ $currentMax }}</h1>
            <h1 class='previous-page' data-page='{{ $previousPage }}'>Previous</h1>
            <h1 class='next-page' data-page='{{ $nextPage }}'>Next</h1>
        </div>

        <div class='email-listing'>
            {!! $listingHTML !!}
        </div>
    </div>

    <div class='email-content'>
        {{-- This is where the email content will be loaded --}}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            emailListing.init();
            emailContextMenu.init();
        });
    </script>

    @include('context_menu')
    {{-- @include('compose_email') --}}
</x-email-layout>
