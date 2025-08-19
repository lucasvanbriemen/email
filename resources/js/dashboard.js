export default {
    init() {
        this.makeEmailsClickable();
    },

    makeEmailsClickable() {
        const emailItems = document.querySelectorAll('.email-item');
        emailItems.forEach(emailItem => {
            emailItem.addEventListener('click', () => {
                const path = emailItem.dataset.path;
                if (path) {
                    if (window.loader) loader.show();
                    window.location.href = path;
                }
            });
        });
    }
}
