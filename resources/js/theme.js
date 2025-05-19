const colors = {
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
        light: '#4285F4',
        dark: '#2e60b1',
    },

    "font-family": {
        light: 'Roboto, sans-serif',
        dark: 'Roboto, sans-serif',
    },

    "border-radius-small": {
        light: '0.25rem',
        dark: '0.25rem',
    },

    "border-radius-medium": {
        light: '0.5rem',
        dark: '0.5rem',
    },

    "border-radius-large": {
        light: '1rem',
        dark: '1rem',
    },

    "border-radius-huge": {
        light: '2rem',
        dark: '2rem',
    },

    "error-background-color": {
        light: '#923131',
        dark: '#923131',
    },

    "error-text-color": {
        light: '#ceb1ad',
        dark: '#ceb1ad',
    },

    "success-background-color": {
        light: '#2e7d32',
        dark: '#2e7d32',
    },

    "success-text-color": {
        light: '#a5d6a7',
        dark: '#a5d6a7',
    },
}

let currentSystem = 'light';
if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
    currentSystem = 'dark';
}

// currentSystem = "light";

// Loop over the colors object and set the CSS variables
for (const [key, value] of Object.entries(colors)) {
    document.documentElement.style.setProperty(`--${key}`, value[currentSystem]);
}