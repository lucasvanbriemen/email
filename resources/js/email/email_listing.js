import app from "../app";
import sidebar from "./sidebar";

export default {
    init: function() {
        const emailListingDiv = document.querySelector('.email-listing');

        const emailItems = emailListingDiv.querySelectorAll('.email-item');
        emailItems.forEach(emailItem => {
            emailItem.addEventListener('click', function() {
                emailListing.openEmail(emailItem);
            });
        });
    },

    openEmail: function(emailItem) {
        const pathToEmail = emailItem.dataset.path;
        app.setUlr(pathToEmail);

        sidebar.minMaxSidebar('minimized');

        const emailListing = document.querySelector('.email-listing');
        emailListing.querySelectorAll('.email-item').forEach(item => {
            item.classList.remove('opened');
        });
        emailItem.classList.add('opened');
        emailItem.classList.remove('unread');

        const emailListingDiv = document.querySelector('.email-listing');
        const emailContent = document.querySelector('.email-content');

        emailListingDiv.classList.add('minimized');
        emailContent.classList.add('maximized');

        const url = pathToEmail + '/html';

        fetch(url)
            .then(response => response.text())
            .then(html => {
                emailContent.innerHTML = html;
            })
            .then(() => {
                emailQuickActions.init();
            })
            .catch(error => {
                console.error('Error fetching email content:', error);
                emailContent.innerHTML = '<p>Error loading email content.</p>';
            });
    }
}