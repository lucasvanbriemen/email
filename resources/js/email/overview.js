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
        console.log(contextFocusedOn);
    });
});


const contextMenuItems = document.querySelectorAll('.context-menu-item');
contextMenuItems.forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        console.log(contextFocusedOn);
        alert(`Action: ${item.dataset.action} on message with UUID: ${contextFocusedOn}`);
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