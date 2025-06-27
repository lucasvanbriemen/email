const modules = import.meta.glob('./**/*.js', { eager: true });

window.theme = modules['./theme.js'].default;
window.emailSidebar = modules['./email/sidebar.js'].default;

theme.setCssVariables();