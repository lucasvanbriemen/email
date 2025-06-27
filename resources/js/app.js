const modules = import.meta.glob('./**/*.js', { eager: true });

const theme = modules['./theme.js'].default;

theme.setCssVariables();