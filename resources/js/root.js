const modules = import.meta.glob('./**/*.js', { eager: true });

window.app = modules['./app.js'].default;

window.theme = modules['./theme.js'].default;
window.emailSidebar = modules['./email/sidebar.js'].default;

window.login = modules['./login.js'].default;

theme.setCssVariables();