export default {

    theme: 'light', // 'light' or 'dark', 'auto' will use the system preference

    colors: {
        'background-color': {
            light: '#ffffff',
            dark: '#121212',
        },

        'background-color-one': {
            light: '#f5f5f5',
            dark: '#1E1E1E',
        },

        'text-color': {
            light: '#000000',
            dark: '#E0E0E0',
        },

        'text-color-secondary': {
            light: '#6e6e6e',
            dark: '#B0B0B0',
        },

        'border-color': {
            light: '#000000',
            dark: '#444444',
        },

        'primary-color': {
            light: '#2e60b1',
            dark: '#2e60b1',
        },

        'primary-color-dark': {
            light: '#224887',
            dark: '#224887',
        },

        'font-family': {
            light: 'Roboto, sans-serif',
            dark: 'Roboto, sans-serif',
        },
    },

    getCurrentTheme() {
        if (this.theme === 'light' || this.theme === 'dark') {
            return this.theme;
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    },

    setCssVariables() {
        const currentTheme = this.getCurrentTheme();

        for (const [key, value] of Object.entries(this.colors)) {
            document.documentElement.style.setProperty(`--${key}`, value[currentTheme]);
        }
    }
};