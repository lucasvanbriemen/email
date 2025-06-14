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

messages.forEach(message => {
    message.addEventListener("contextmenu", (e) => { alert("Right click on the message to open the context menu."); });
});