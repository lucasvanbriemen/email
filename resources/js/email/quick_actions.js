const quickActions = document.querySelectorAll('.quick-action');
const quickActionsWrapper = document.querySelector('.quick-action-wrapper');

const ALLOWED_ACTIONS = ['goback_to_overview', 'remove_email'];

quickActions.forEach(action => {
    action.addEventListener('click', function(event) {
        event.preventDefault();

        const url = this.getAttribute('data-url');
        const token = quickActionsWrapper.querySelector('input[name="_token"]').value;
        const action = this.getAttribute('data-action');

        if (!url) {
            return;
        }

        fetch(url, {method: 'POST', headers: {'X-CSRF-TOKEN': token}})

        .then(response => response.json())
        .then(data => {
            if (!ALLOWED_ACTIONS.includes(action)) {
                window.location.reload();
            }

            const actionHint = this.getAttribute('data-action-hint');
            if (action === 'remove_email') {
                document.querySelector('.' + actionHint).remove(); 
            } 

            if (action === 'go_back_to_folder') {
                window.location.href = actionHint;
            }
        });
    });
});
