export default {

    theme: 'auto', // 'light' or 'dark', 'auto' will use the system preference

    colors: {
        'background-color': {
            light: '#ffffff',
            dark: '#121212',
        },

        'background-color-one': {
            light: '#d5e1ed',
            dark: '#1E1E1E',
        },

        'background-color-two': {
            light: '#aab5bf',
            dark: '#303030',
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
            light: '#4285f4',
            dark: '#1266f1',
        },

        'primary-color-dark': {
            light: '#1266f1',
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