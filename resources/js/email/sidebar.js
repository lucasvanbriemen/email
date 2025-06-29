export default {
    init: function() {
        const currentProfile = document.querySelector('.current-profile');
        const profileSelector = document.querySelector('.profile-selector');

        currentProfile.addEventListener('click', function() {
            profileSelector.classList.toggle('active');
        });

        const folders = document.querySelectorAll('.folder');
        folders.forEach(folder => {
            folder.addEventListener('click', function() {
                emailSidebar.changeFolder(folder);
            });
        });
    },

    minMaxSidebar: function(setState = 'minimized') {
        const sidebar = document.querySelector('.sidebar');

        sidebar.classList.toggle('minimized');

        if (setState === 'minimized') {
            sidebar.classList.add('minimized');
        } else if (setState === 'maximized') {
            sidebar.classList.remove('minimized');
        }
    },

    changeFolder: function(folder) {
        app.setUlr(folder.dataset.path);
        emailSidebar.updateFolderClass(folder);

        const folderUrl = folder.dataset.url;
        fetch(folderUrl)
            .then(response => response.text())
            .then(html => {
                const content = document.querySelector('.email-listing');
                content.innerHTML = html;
            })
            .then(() => {
                emailListing.init();
            })
    },

    updateFolderClass: function(folder) {
        const allFolders = document.querySelectorAll('.folder');
        allFolders.forEach(f => {
            f.classList.remove('selected');
        });

        folder.classList.add('selected');
    },
}