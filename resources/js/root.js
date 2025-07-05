const modules = import.meta.glob('./**/*.js', { eager: true });

window.app = modules['./app.js'].default;
window.theme = modules['./theme.js'].default;
window.toast = modules['./toast.js'].default;

window.emailSidebar = modules['./email/sidebar.js'].default;
window.emailListing = modules['./email/email_listing.js'].default;
window.email = modules['./email/email.js'].default;

window.emailQuickActions = modules['./email/quick_actions.js'].default;

window.login = modules['./login.js'].default;

theme.setCssVariables();