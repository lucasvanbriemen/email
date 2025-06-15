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
messages.forEach(message => {
    message.addEventListener("contextmenu", (e) => {
        e.preventDefault();

        const contextMenu = document.querySelector('.context-menu');
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

        let url = currentUrl + '/mail/' + messageUuid + '/' + action;

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

            if (action === 'read') {
                const messageElement = document.querySelector(`[data-uuid="${messageUuid}"]`);
                if (messageElement) {
                    messageElement.classList.remove('unread');
                    messageElement.classList.add('read');
                }
            }

            if (action === 'star') {
                const messageElement = document.querySelector(`[data-uuid="${messageUuid}"]`);
                if (messageElement) {
                    messageElement.classList.remove('unstarred');
                    messageElement.classList.add('starred');
                }
            }

            alert(data.message);
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