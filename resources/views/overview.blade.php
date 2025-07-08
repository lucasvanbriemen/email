<x-email-layout class="overview-page">
    @php
        $last_iterated_date = null;
    @endphp

    <div class='listing-wrapper'>
        <div class='listing-header'>
            <h1 class='listing-title'>{{ $selectedFolder->name }}</h1>
            <h1 class='listing-title'>{{ $totalEmailCount }}</h1>

            <button onclick='emailListing.updateEmailListing("/1/folder/all/listing/2")'>2</button>
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
