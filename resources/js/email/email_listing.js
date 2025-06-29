import app from "../app";

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
    }
}