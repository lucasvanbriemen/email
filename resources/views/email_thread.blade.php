@php
    // Ensure subject fallback
    if (!$parent['subject']) {
        $parent['subject'] = 'No Subject';
    }

    $parentPath = route('mailbox.folder.mail', [
        'linked_profile_id' => $linked_profile_id,
        'folder' => $folderPath,
        'uuid' => $parent->uuid,
    ]);

    $hasChildren = isset($children) && count($children) > 0;
@endphp

@if (!$hasChildren)
    {{-- Single message only: render as a normal email item without thread container --}}
    @include('email_listing', [
        'email' => $parent,
        'pathToEmail' => $parentPath,
        // no 'thread' context when there are no children
    ])
@else
    <div class="email-thread" data-has-children="true">
        
        {{-- Parent (latest) email at the top, marked as thread for context menu --}}
        @include('email_listing', [
            'email' => $parent,
            'pathToEmail' => $parentPath,
            'contextType' => 'single-message thread',
            'is_child' => false,
        ])

        <div class="thread-children-toggle">
            <button class="toggle-thread-children" type="button" aria-expanded="false">
                Show relevant messages ({{ count($children) }})
            </button>
        </div>

        {{-- Children, smaller entries --}}
        @foreach ($children as $child)
            @php
                if (!$child['subject']) {
                    $child['subject'] = 'No Subject';
                }
                $childPath = route('mailbox.folder.mail', [
                    'linked_profile_id' => $linked_profile_id,
                    'folder' => $folderPath,
                    'uuid' => $child->uuid,
                ]);
            @endphp
            @include('email_listing', [
                'email' => $child,
                'pathToEmail' => $childPath,
                'is_child' => true,
            ])
        @endforeach
    </div>
@endif
