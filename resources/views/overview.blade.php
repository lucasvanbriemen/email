<x-email-layout class="overview-page">
    @php
        $last_iterated_date = null;
    @endphp

    <div class='email-listing'>
        {!! $listingHTML !!}
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
