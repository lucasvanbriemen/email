export default {
    currentFolder: null,

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

        const selectedFolder = document.querySelector('.folder.selected');
        if (selectedFolder) {
            this.currentFolder = selectedFolder.dataset.path;
        }
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

        // if the .listing-wrapper does not exist, just go to the URL
        if (!document.querySelector('.listing-wrapper')) {
            window.location.href = folder.dataset.path;
            return;
        }

        emailSidebar.updateFolderClass(folder);
        emailSidebar.currentFolder = folder.dataset.path;
        emailListing.updateEmailListing(folder.dataset.url);
    },

    updateFolderClass: function(folder) {
        const allFolders = document.querySelectorAll('.folder');
        allFolders.forEach(f => {
            f.classList.remove('selected');
        });

        folder.classList.add('selected');
    },
}