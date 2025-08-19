const modules = import.meta.glob('./**/*.js', { eager: true });

const exportsMap = {
    app: './app.js',
    theme: './theme.js',
    toast: './toast.js',
    emailSidebar: './email/sidebar.js',
    emailListing: './email/email_listing.js',
    email: './email/email.js',
    emailQuickActions: './email/quick_actions.js',
    emailContextMenu: './email/context_menu.js',
    dashboard: './dashboard.js',
    loader: './loader.js',
};

for (const [key, path] of Object.entries(exportsMap)) {
    window[key] = modules[path].default;
}

theme.setCssVariables();
