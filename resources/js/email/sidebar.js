export default {
    init: function() {
        const currentProfile = document.querySelector('.current-profile');
        const profileSelector = document.querySelector('.profile-selector');

        currentProfile.addEventListener('click', function() {
            profileSelector.classList.toggle('active');
        });
    },

    minMaxSidebar: function() {
        const sidebar = document.querySelector('.sidebar');

        sidebar.classList.toggle('minimized');
    }
}