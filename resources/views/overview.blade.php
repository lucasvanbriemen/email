<x-email-layout class="overview-page">
    @php
        $last_iterated_date = null;
    @endphp

     {{-- <div onclick="emailSidebar.minMaxSidebar()">
        open
    </div> --}}

    {{-- @if (count($emailThreads) == 0) --}}
        
    {{-- @endif --}}


    <div class='email-listing'>
        {!! $listingHTML !!}
    </div>

    <div class='email-content'>
        {{-- This is where the email content will be loaded --}}
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Sint blanditiis dolorum nihil rerum explicabo at porro a voluptas ullam voluptatem, voluptates perferendis commodi, libero, cupiditate nostrum magnam omnis quam consectetur!
    </div>

    {{-- @include('context_menu')
    @include('compose_email') --}}
</x-email-layout>
