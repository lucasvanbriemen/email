export default {
    init: function() {
        const emailItems = document.querySelectorAll('.email-item');
        emailItems.forEach(emailItem => {
            emailItem.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                this.openContextMenu(emailItem, e);
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

            console.log(contextMenuRequirement);
            console.log(message);

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
    }
}