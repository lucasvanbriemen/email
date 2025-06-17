const messages = document.querySelectorAll('.message');
const DONT_GO_TO_URL_SELECTOR = ['.quick-action-wrapper']; 

messages.forEach(message => {
    message.addEventListener('click', function (event) {
        const clickedElement = event.target;
        const hasDontGoToUrlClass = DONT_GO_TO_URL_SELECTOR.some(className => 
            clickedElement.classList.contains(className) ||
            clickedElement.closest(className)
        );

        if (!hasDontGoToUrlClass) {
            window.location.href = message.dataset.url;
        }
    });
});


const threadElements = document.querySelectorAll('.email-thread');
const DONT_TOGGLE_CLASS_SELECTOR = ['.message'];

threadElements.forEach(threadElement => {
    threadElement.addEventListener('click', function (event) {
        // Add the open class to the clicked thread element
        const clickedElement = event.target;
        const hasDontToggleClass = DONT_TOGGLE_CLASS_SELECTOR.some(className => 
            clickedElement.classList.contains(className) ||
            clickedElement.closest(className)
        );

        if (!hasDontToggleClass) {
            threadElement.classList.toggle('open');
        }
    });
});

let contextFocusedOn = null;
document.querySelectorAll('.message, .thead-top-message').forEach(message => {
    message.addEventListener("contextmenu", (e) => {
        e.preventDefault();
        const contextMenu = document.querySelector('.context-menu');
        const contextMenuItems = contextMenu.querySelectorAll('.context-menu-item');

        contextMenuItems.forEach(contextMenuItem => { 
            contextMenuItem.classList.remove('hidden');

            const contextMenuRequirement = contextMenuItem.dataset.requirement.split(' ');
            const messageRequirement = message.dataset.contextMenu.split(' ');

            let canShow = true;
            contextMenuRequirement.forEach(requirement => {
                if (!messageRequirement.includes(requirement)) {
                    canShow = false;
                }
            });

            if (!canShow) {
                contextMenuItem.classList.add('hidden');
            }
        });

        contextMenu.classList.add('open');

        const menuWidth =  15 * 16 
        const menuHeight = contextMenu.offsetHeight;

        let left = e.clientX;
        let top = e.clientY;

        if (left + menuWidth > window.innerWidth) {
            left = window.innerWidth - menuWidth;
        }

        if (top + menuHeight > window.innerHeight) {
            top = window.innerHeight - menuHeight;
        }

        contextMenu.style.top = `${top}px`;
        contextMenu.style.left = `${left}px`;

        const uuidElement = e.target.closest('[data-uuid]');
        contextFocusedOn = uuidElement ? uuidElement.dataset.uuid : null;
    });
});


const contextMenuItems = document.querySelectorAll('.context-menu-item');
contextMenuItems.forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        // Get the current URL and remove the trailing slash if it exists
        let currentUrl = window.location.href;
        if (currentUrl.endsWith('/')) {
            currentUrl = currentUrl.slice(0, -1);
        }

        const action = e.target.closest('[data-action]').dataset.action;
        const messageUuid = contextFocusedOn;

        let url = currentUrl + '/mail/' + messageUuid.replace('-thread', '') + '/' + action;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value 
            }
        })
        .then(response => response.json())
        .then(data => {

            if (action === 'delete' || action === 'archive') {
                const messageElement = document.querySelector(`[data-uuid="${messageUuid}"]`);
                if (messageElement) {
                    messageElement.remove();
                }
            }

            if (action === 'read' || action === 'unread') {
                const messageElement = document.querySelector(`[data-uuid="${messageUuid}"]`);
                if (messageElement) {
                    messageElement.classList.toggle('unread');
                    messageElement.classList.toggle('read');
                }
            }

            if (action === 'star'  || action === 'unstar') {
                const messageElement = document.querySelector(`[data-uuid="${messageUuid}"]`);
                if (messageElement) {
                    messageElement.classList.toggle('unstarred');
                    messageElement.classList.toggle('starred');
                }
            }

            if (action === 'read_thread'  || action === 'unread_thread') {
                const readAction = action === 'read_thread' ? 'read' : 'unread';
                const notAction = readAction === 'read' ? 'unread' : 'read';

                const messageElement = document.querySelector(`[data-uuid="${messageUuid}"]`);
                if (messageElement) {
                    messageElement.classList.remove(notAction);
                    messageElement.classList.add(readAction);

                    const messageThread = messageElement.closest('.email-thread');
                    if (readAction === 'read') {
                        messageThread.classList.remove('open');
                    } else {
                        messageThread.classList.add('open');
                    }

                    const threadMessages = messageThread.querySelectorAll('.message');
                    threadMessages.forEach(threadMessage => {
                        threadMessage.classList.remove(notAction);
                        threadMessage.classList.add(readAction);
                    });
                }
            }

            if (action === 'delete_thread') {
                const messageElement = document.querySelector(`[data-uuid="${messageUuid}"]`);
                if (messageElement) {
                    messageElement.remove();
                }
            }

            const contextMenu = document.querySelector('.context-menu');
            if (contextMenu) {
                contextMenu.classList.remove('open');
                contextFocusedOn = null;
            }
        })
    });
});


document.addEventListener('click', (e) => {
    const contextMenu = document.querySelector('.context-menu');
    if (!contextMenu) return;

    const isClickInside = contextMenu.contains(e.target);
    if (!isClickInside && contextMenu.classList.contains('open')) {
        contextMenu.classList.remove('open');
        contextFocusedOn = null;
    }
});