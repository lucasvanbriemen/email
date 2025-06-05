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