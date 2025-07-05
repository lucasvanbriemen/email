export default {
    show_toast: function (message, type) {
        const toast = document.querySelector('.toast');

        if (!toast) {
            console.error('Toast element not found');
            return;
        }

        toast.querySelector('.text').innerHTML = message;
        toast.classList.remove('toast-error', 'toast-success');
        toast.classList.add(`toast-${type}`);

        toast.classList.add('toast-visible');

        setTimeout(function () {
            toast.classList.remove('toast-visible');
        }, 3000);
    }
};
