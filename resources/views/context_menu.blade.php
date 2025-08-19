<div class='context-menu'>
    @csrf

    <div class="context-menu-item" data-action="read" data-post-action="remove_class" data-hint="unread" data-requirement="unread single-message">
        <span>Mark as read</span>
    </div>

    <div class="context-menu-item" data-action="unread" data-post-action="add_class" data-hint="unread" data-requirement="read single-message">
        <span>Mark as unseen</span>
    </div>

    <div class="context-menu-item" data-action="star" data-post-action="add_class" data-hint="starred" data-requirement="unstarred single-message">
        <span>Star</span>
    </div>

    <div class="context-menu-item" data-action="unstar" data-post-action="remove_class" data-hint="starred" data-requirement="starred single-message">
        <span>Remove star</span>
    </div>

    <div class="context-menu-item" data-action="archive" data-post-action="remove_email" data-requirement="not-archived single-message">
        <span>Archive</span>
    </div>

    <div class="context-menu-item" data-action="delete" data-post-action="remove_email" data-requirement="not-deleted single-message">
        <span>Delete</span>
    </div>

    {{-- Thread actions --}}
    <div class="context-menu-item" data-action="read_thread" data-post-action="remove_class" data-hint="unread" data-requirement="thread unread">
        <span>Mark thread as read</span>
    </div>

    <div class="context-menu-item" data-action="star_thread" data-post-action="add_class" data-hint="starred" data-requirement="thread unstarred">
        <span>Star thread</span>
    </div>

    <div class="context-menu-item" data-action="archive_thread" data-post-action="remove_thread" data-requirement="thread not-archived">
        <span>Archive thread</span>
    </div>

    <div class="context-menu-item" data-action="delete_thread" data-post-action="remove_thread" data-requirement="thread not-deleted">
        <span>Delete thread</span>
    </div>
</div>
