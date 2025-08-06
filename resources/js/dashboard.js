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
                    window.location.href = path;
                }
            });
        });
    }
}