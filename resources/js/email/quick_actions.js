export default {
    init: function() {
        const quickActions = document.querySelectorAll('.quick-action');

        console.log(quickActions);

        quickActions.forEach(action => {
            action.addEventListener('click', emailQuickActions.handleAction);
        });
    },

    handleAction: function(event) {
        const url = this.getAttribute('data-url');
        const token = document.querySelector('.quick-action-wrapper input[name="_token"]').value;

        emailQuickActions.postQuickAction(url, token);
    },

    postQuickAction: function(url, token) {
        if (!url) {
            return;
        }

        fetch(url, {method: 'POST', headers: {'X-CSRF-TOKEN': token}})
    }
}