export default {
    init: function() {
        const emailInput = document.querySelector('.email-input');

        emailInput.focus();

        this.setImageToTheme();
    },

    setImageToTheme: function() {
        const currentTheme = theme.getCurrentTheme();

        if (currentTheme === 'dark') {
            document.querySelector('.login-illustration').src = '/images/login-image-dark.jpg';
        } else {
            document.querySelector('.login-illustration').src = '/images/login-image-light.jpg';
        }
    }
};