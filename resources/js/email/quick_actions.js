export default {
    init: function() {
        const quickActions = document.querySelectorAll('.quick-action');

        quickActions.forEach(action => {
            action.addEventListener('click', emailQuickActions.handleAction);
        });
    },

    handleAction: function(event) {
        const url = this.getAttribute('data-url');
        const token = document.querySelector('.quick-action-wrapper input[name="_token"]').value;

        emailQuickActions.postQuickAction(url, token);
        emailQuickActions.followUpAction(event);
    },

    postQuickAction: function(url, token) {
        if (!url) {
            return;
        }

        fetch(url, {method: 'POST', headers: {'X-CSRF-TOKEN': token}})
            .then(response => response.json())
            .then(data => {
                toast.show_toast(data.message, data.status);
            })
    },

    followUpAction: function(event) {
        const followUpAction = event.target.dataset.followUpAction;
        alert('Follow-up action: ' + followUpAction);
    }
}