<x-email-layout class="overview-page">
    @php
        $last_iterated_date = null;
    @endphp

    <div class='listing-wrapper'>
        <div class='listing-header'>
            
            <div class='current-range'>
                <span class='current-min'>{{ $currentMin }}</span> - <span class='current-max'>{{ $currentMax }}</span>
                of <span class='total-email-count'>{{ $totalEmailCount }}</span>
            </div>

            
            <div class='previous-page' data-page='{{ $previousPage ?? 0 }}'>{!! svg('left-arrow') !!}</div>
            <div class='next-page' data-page='{{ $nextPage ?? 1 }}'>{!! svg('right-arrow') !!}</div>
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
        });
    </script>

    @include('context_menu')
    {{-- @include('compose_email') --}}
</x-email-layout>
