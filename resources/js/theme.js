export default {

    forceLightTheme: false,
    forceDarkTheme: false,

    colors: {
        "background-color": {
            light: '#ffffff',
            dark: '#121212',
        },

        "background-color-one": {
            light: '#f5f5f5',
            dark: '#1E1E1E',
        },

        "text-color": {
            light: '#000000',
            dark: '#E0E0E0',
        },

        "text-color-secondary": {
            light: '#B0B0B0',
            dark: '#B0B0B0',
        },

        "border-color": {
            light: '#000000',
            dark: '#444444',
        },

        "primary-color": {
            light: '#2e60b1',
            dark: '#2e60b1',
        },

        "primary-color-dark": {
            light: '#224887',
            dark: '#224887',
        },

        "font-family": {
            light: 'Roboto, sans-serif',
            dark: 'Roboto, sans-serif',
        },
    },

    getCurrentTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    },

    setCssVariables() {
        let currentTheme = this.getCurrentTheme();

        if (this.forceLightTheme) {
            currentTheme = 'light';
        } else if (this.forceDarkTheme) {
            currentTheme = 'dark';
        }

        for (const [key, value] of Object.entries(this.colors)) {
            document.documentElement.style.setProperty(`--${key}`, value[currentTheme]);
        }
    }
};