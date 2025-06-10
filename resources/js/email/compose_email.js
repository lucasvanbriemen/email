const backdrop = document.querySelector('.email-form-backdrop');
const composeEmailWrapper = document.querySelector('.compose-email-wrapper');

composeEmailWrapper.addEventListener('click', function() {
    composeEmailWrapper.classList.toggle('open');
});