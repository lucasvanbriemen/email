export default {
    init: function() {
        const previousPage = document.querySelector('.previous-page');
        const nextPage = document.querySelector('.next-page');

        previousPage.addEventListener('click', function() {
            emailListing.changePage(previousPage.dataset.page);
        });

        nextPage.addEventListener('click', function() {
            emailListing.changePage(nextPage.dataset.page)
        });

        this.addEvents();
    },

    addEvents: function() {
        const emailListingDiv = document.querySelector('.email-listing');

        const emailItems = emailListingDiv.querySelectorAll('.email-item');
        emailItems.forEach(emailItem => {
            emailItem.addEventListener('click', function() {
                emailListing.openEmail(emailItem);
            });
        });

        // Thread toggle buttons
        const toggles = emailListingDiv.querySelectorAll('.toggle-thread-children');
        toggles.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const thread = btn.closest('.email-thread');
                if (!thread) return;
                const expanded = thread.classList.toggle('expanded');
                btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');

                // Update button text
                const totalChildren = thread.querySelectorAll('.email-item.child').length;
                if (expanded) {
                    btn.textContent = `Hide relevant messages (${totalChildren})`;
                } else {
                    btn.textContent = `Show relevant messages (${totalChildren})`;
                }
            });
        });

        emailContextMenu.init();
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

        loader.show();
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
            })
            .finally(() => loader.hide());
    },

    updateEmailListing: function(url) {
        const emailListingDiv = document.querySelector('.email-listing');

        loader.show();
        fetch(url)
            .then(response => response.json())
            .then(data => {
                emailListingDiv.innerHTML = data.html;
                this.updateEmailListingHeader(data.header);
                this.addEvents(); // Reinitialize the email listing after updating
            })
            .catch(error => {
                console.error('Error updating email listing:', error);
                emailListingDiv.innerHTML = '<p>Error loading emails.</p>';
            })
            .finally(() => loader.hide());
    },

    changePage: function(page) {
        // if the value is null, it means there is no next page
        if (page === 'null') {
            return;
        }

        this.updateEmailListing(emailSidebar.currentFolder + '/listing/' + page);
    },

    updateEmailListingHeader: function(headerData) {
        const listingHeader = document.querySelector('.listing-header');
        listingHeader.querySelector('.total-email-count').textContent = headerData.total_email_count;
        listingHeader.querySelector('.current-min').textContent = headerData.current_min;
        listingHeader.querySelector('.current-max').textContent = headerData.current_max;
        listingHeader.querySelector('.previous-page').dataset.page = headerData.previous_page;
        listingHeader.querySelector('.next-page').dataset.page = headerData.next_page;
    },
}
