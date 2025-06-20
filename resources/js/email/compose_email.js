const composeEmailButton = document.querySelector('.compose-email');
const composeEmailWrapper = document.querySelector('.compose-email-wrapper');

composeEmailButton.addEventListener('click', function() {
    composeEmailWrapper.classList.toggle('open');
});

const composeEmailBackground = document.querySelector('.compose-email-background');
composeEmailBackground.addEventListener('click', function() {
    composeEmailWrapper.classList.remove('open');
});