// const quickActions = document.querySelectorAll('.quick-action');
// const quickActionsWrapper = document.querySelector('.quick-action-wrapper');

// const ALLOWED_ACTIONS = ['go_back', 'add_class', 'custom'];

// quickActions.forEach(action => {

//     if (action.getAttribute('data-action') === 'custom') {
//         return;
//     }

//     action.addEventListener('click', function(event) {
//         event.preventDefault();

//         const url = this.getAttribute('data-url');
//         const token = quickActionsWrapper.querySelector('input[name="_token"]').value;
//         const action = this.getAttribute('data-action');

//         if (!url) {
//             return;
//         }

//         fetch(url, {method: 'POST', headers: {'X-CSRF-TOKEN': token}})

//         .then(response => response.json())
//         .then(data => {
//             if (!ALLOWED_ACTIONS.includes(action)) {
//                 window.location.reload();
//             }

//             const actionHint = this.getAttribute('data-action-hint');
//             if (action === 'add_class') {
//                 document.querySelector('.email-header').classList.add(actionHint); 
//             } 

//             if (action === 'go_back') {
//                 window.location.href = actionHint;
//             }
//         });
//     });
// });

// const tagSelector = document.getElementById('select-tag');
// if (tagSelector) {
//     tagSelector.addEventListener('change', function() {
//         const selectedTag = this.value;
//         const url = this.getAttribute('data-url');
//         const token = quickActionsWrapper.querySelector('input[name="_token"]').value;

//         if (!url || !selectedTag) {
//             return;
//         }

//         const formData = new FormData();
//         formData.append('tag_id', selectedTag);

//         fetch(url, {
//             method: 'POST',
//             headers: {'X-CSRF-TOKEN': token},
//             body: formData
//         })
//     });
// }