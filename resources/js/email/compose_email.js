const composeEmailButton = document.querySelector('.compose-email');
const composeEmailWrapper = document.querySelector('.compose-email-wrapper');

const editor = new FroalaEditor('#email-body-wysiwyg');

composeEmailButton.addEventListener('click', function() {
    composeEmailWrapper.classList.toggle('open');
});

const composeEmailBackground = document.querySelector('.compose-email-background');
composeEmailBackground.addEventListener('click', function() {
    composeEmailWrapper.classList.remove('open');
});


const sendEmailButton = composeEmailWrapper.querySelector('.send-email');
sendEmailButton.addEventListener('click', function() {
    const emailBodyInput = composeEmailWrapper.querySelector('.email-body');
    emailBodyInput.value = editor.html.get();

    alert(emailBodyInput.value);
});