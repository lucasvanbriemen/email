const quickActions = document.querySelectorAll('.quick-action');
const quickActionsWrapper = document.querySelector('.quick-action-wrapper');

quickActions.forEach(action => {
    action.addEventListener('click', function(event) {
        event.preventDefault();

        const url = this.getAttribute('data-url');
        const token = quickActionsWrapper.querySelector('input[name="_token"]').value;

        const folowUpFunction = this.getAttribute('data-function');

        if (!url) {
            return;
        }

        fetch(url, {method: 'POST', headers: {'X-CSRF-TOKEN': token}})

        .then(response => response.json())
        .then(data => {
            window.location.reload();
        });
    });
});


function archiveEmail() {
    alert('Email archived successfully!');
}