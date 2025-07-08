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

    updateEmailListing: function(url) {
        const emailListingDiv = document.querySelector('.email-listing');

        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                emailListingDiv.innerHTML = data.html;
                this.init(); // Reinitialize the email listing after updating
                this.updateEmailListingHeader(data.header);
            })
            .catch(error => {
                console.error('Error updating email listing:', error);
                emailListingDiv.innerHTML = '<p>Error loading emails.</p>';
            });
    },

    updateEmailListingHeader: function(headerData) {
        const listingHeader = document.querySelector('.listing-header');
        listingHeader.querySelector('.current-folder-name').textContent = headerData.folder;
        listingHeader.querySelector('.total-email-count').textContent = headerData.total_email_count;
        listingHeader.querySelector('.current-min').textContent = headerData.current_min;
        listingHeader.querySelector('.current-max').textContent = headerData.current_max;
        listingHeader.querySelector('.previous-page').dataset.page = headerData.previous_page;
        listingHeader.querySelector('.next-page').dataset.page = headerData.next_page;
    }
}