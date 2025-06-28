export default {
    init: function() {
        const menuicon = document.querySelector('.menu-icon');
        const sidebar = document.querySelector('.sidebar');

        menuicon.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            menuicon.classList.toggle('active');
        });

        const currentAccount = document.querySelector('.current-account');
        const accountSelector = document.querySelector('.account-selector');

        currentAccount.addEventListener('click', function() {
            accountSelector.classList.toggle('open');
        });
    },

    minMaxSidebar: function() {
        const sidebar = document.querySelector('.sidebar');

        sidebar.classList.toggle('minimized');
    }
}