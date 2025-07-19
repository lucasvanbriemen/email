export default {
    init: function() {
        const quickActions = document.querySelectorAll('.quick-action');

        quickActions.forEach(action => {
            action.addEventListener('click', emailQuickActions.handleAction);
        });

        document.querySelectorAll('.quick-action-wrapper select').forEach(select => {
            select.addEventListener('change', emailQuickActions.handleLabelChange);
        });
    },

    handleAction: function(event) {
        const url = this.getAttribute('data-url');
        const token = document.querySelector('.quick-action-wrapper input[name="_token"]').value;

        emailQuickActions.postQuickAction(url, token);
        emailQuickActions.followUpAction(event);
    },

    handleLabelChange: function(event) {
        const url = this.dataset.url;
        const token = document.querySelector('.quick-action-wrapper input[name="_token"]').value;

        const value = this.value;

        fetch(url, {method: 'POST', headers: {'X-CSRF-TOKEN': token}, body: JSON.stringify({tag_id: value})})
            .then(response => response.json())
            .then(data => {
                toast.show_toast(data.message, data.status);
            });
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
    }
}