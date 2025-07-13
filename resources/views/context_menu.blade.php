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

    <div class="context-menu-item" data-action="delete" data-post-action="remove_email" data-requirement="deleted single-message">
        <span>Delete</span>
    </div>
</div>