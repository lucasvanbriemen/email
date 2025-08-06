export default {
    has_listeners: false,
    defualtPostActions: ['remove_class', 'add_class', 'remove_email'],

    init: function() {
        const emailItems = document.querySelectorAll('.email-item');

        const contextMenuItems = document.querySelectorAll('.context-menu-item');

        emailItems.forEach(emailItem => {
            emailItem.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                this.openContextMenu(emailItem, e);
            });
        });

        if (this.has_listeners) {
            return;
        }

        this.has_listeners = true;
        
        contextMenuItems.forEach(contextMenuItem => {
            contextMenuItem.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleContextMenuItemClick(contextMenuItem, e);
            });
        });

        window.addEventListener('click', (e) => {
            const contextMenu = document.querySelector('.context-menu');
            if (contextMenu && !contextMenu.contains(e.target)) {
                contextMenu.classList.remove('open');
            }
        });
    },

    openContextMenu: function(message, e) {
        const contextMenu = document.querySelector('.context-menu');
        const contextMenuItems = contextMenu.querySelectorAll('.context-menu-item');

        contextMenuItems.forEach(contextMenuItem => { 
            contextMenuItem.classList.remove('hidden');

            const contextMenuRequirement = contextMenuItem.dataset.requirement.split(' ');

            const messageRequirement = message.dataset.contextMenu.split(' ');

            let canShow = true;
            contextMenuRequirement.forEach(requirement => {
                if (!messageRequirement.includes(requirement)) {
                    canShow = false;
                }
            });

            if (!canShow) {
                contextMenuItem.classList.add('hidden');
            }
        });

        contextMenu.classList.add('open');

        const menuWidth =  15 * 16 
        const menuHeight = contextMenu.offsetHeight;

        let left = e.clientX;
        let top = e.clientY;

        if (left + menuWidth > window.innerWidth) {
            left = window.innerWidth - menuWidth;
        }

        if (top + menuHeight > window.innerHeight) {
            top = window.innerHeight - menuHeight;
        }

        contextMenu.style.top = `${top}px`;
        contextMenu.style.left = `${left}px`;

        const emailUuid = message.dataset.emailId;

        contextMenu.dataset.emailId = emailUuid;
    },

    handleContextMenuItemClick: function(contextMenuItem, e) {
        e.preventDefault();
        const contextMenu = document.querySelector('.context-menu');
        const emailUuid = contextMenu.dataset.emailId;

        if (!emailUuid) {
            toast.show_toast('No email selected for context menu action.', 'error');
            return;
        }


        // Get the current URL and remove  everything after the the word following the word 'folder'
        let currentUrl = window.location.href;
        const folderIndex = currentUrl.indexOf('folder');
        if (folderIndex !== -1) {
            const afterFolder = currentUrl.substring(folderIndex + 'folder'.length + 1); // skip slash
            const firstSegment = afterFolder.split('/')[0];
            currentUrl = currentUrl.substring(0, folderIndex + 'folder'.length + 1 + firstSegment.length);
        }

        const postUrl = currentUrl + '/mail/' + emailUuid + '/' + contextMenuItem.dataset.action;
        const token = document.querySelector('input[name="_token"]').value

        this.postContextMenuAction(contextMenuItem, emailUuid);
        fetch(postUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token 
            }
        })
        .then(response => response.json())
        .then(data => {
            toast.show_toast(data.message, data.status);
        })

        contextMenu.classList.remove('open');
    },

    postContextMenuAction: function(contextMenuItem, emailUuid) {
        const postAction = contextMenuItem.dataset.postAction;
        const emailItem = document.querySelector(`.email-item[data-email-id="${emailUuid}"]`);
        // Get current page by using previous page + 1, or default to 0 if no previous page
        const previousPage = document.querySelector('.previous-page').dataset.page;
        let currentPage;
        
        if (previousPage === 'null') {
            currentPage = 0; // First page (0-indexed)
        } else {
            currentPage = parseInt(previousPage) + 1;
        }

        if (!postAction) {
            return;
        }

        if (postAction == 'remove_email') {
            emailItem.remove();
            setTimeout(() => {
                emailListing.updateEmailListing(emailSidebar.currentFolder + '/listing/' + currentPage);
            }, 250);
        }

        if (postAction == 'add_class') {
            emailItem.classList.add(contextMenuItem.dataset.hint);
        }

        if (postAction == 'remove_class') {
            emailItem.classList.remove(contextMenuItem.dataset.hint);
        }
    },
}