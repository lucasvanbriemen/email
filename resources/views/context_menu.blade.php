<div class='context-menu'>
    @csrf

    <div class="context-menu-item" data-action="read" data-requirement="unread single-message">
        <span>Mark as read</span>
    </div>

    <div class="context-menu-item" data-action="unread" data-requirement="read single-message">
        <span>Mark as unread</span>
    </div>

    <div class="context-menu-item" data-action="star" data-requirement="unstarred single-message">
        <span>Star</span>
    </div>

    <div class="context-menu-item" data-action="unstar" data-requirement="starred single-message">
        <span>Remove star</span>
    </div>

    <div class="context-menu-item" data-action="archive" data-requirement="not-archived single-message">
        <span>Archive</span>
    </div>

    <div class="context-menu-item" data-action="delete" data-requirement="not-deleted single-message">
        <span>Delete</span>
    </div>

    <div class="context-menu-item" data-action="read_thread" data-requirement="unread thread">
        <span>Mark thread as read</span>
    </div>

    <div class="context-menu-item" data-action="delete_thread" data-requirement="not-deleted thread">
        <span>Delete thread</span>
    </div>
</div>