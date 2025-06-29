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

    minMaxSidebar: function() {
        const sidebar = document.querySelector('.sidebar');

        sidebar.classList.toggle('minimized');
    },

    changeFolder: function(folder) {
        app.setUlr(folder.dataset.folder);
        emailSidebar.updateFolderClass(folder);
    },

    updateFolderClass: function(folder) {
        const allFolders = document.querySelectorAll('.folder');
        allFolders.forEach(f => {
            f.classList.remove('selected');
        });

        folder.classList.add('selected');
    }
}