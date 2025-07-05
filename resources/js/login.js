export default {

    darkmode_image: '/images/login-image-dark.jpg',
    lightmode_image: '/images/login-image-light.jpg',

    init: function() {
        const firstInput = document.querySelector('.input-wrapper input');
        firstInput.focus();

        this.setImageToTheme();
    },

    setImageToTheme: function() {
        const currentTheme = theme.getCurrentTheme();

        if (currentTheme === 'dark') {
            document.querySelector('.login-illustration').src = this.darkmode_image;
        } else {
            document.querySelector('.login-illustration').src = this.lightmode_image;
        }
    }
};