export default {
    init: function() {
        const emailListingDiv = document.querySelector('.email-listing');

        const emailItems = emailListingDiv.querySelectorAll('.email-item');
        emailItems.forEach(emailItem => {
            emailItem.addEventListener('click', function() {
                emailListing.openEmail(emailItem);
            });
        });

        // If we scroll to the bottom, load more emails
        emailListingDiv.addEventListener('scroll', function() {
            emailListing.scrollHandler(emailListingDiv);
        });
    },

    openEmail: function(emailItem) {
        const pathToEmail = emailItem.dataset.path;
        app.setUlr(pathToEmail);

        emailSidebar.minMaxSidebar('minimized');

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
    },

    scrollHandler: function(emailListingDiv) {
        const scrollHeight = emailListingDiv.scrollHeight;
        const scrollTop = emailListingDiv.scrollTop;
        const clientHeight = emailListingDiv.clientHeight;

        if (scrollTop + clientHeight >= scrollHeight - 100) {
            emailListing.loadMoreEmails();
        }
    },

    loadMoreEmails: function() {
        alert('Loading more emails...');
    }
}