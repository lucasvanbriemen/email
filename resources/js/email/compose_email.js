const composeEmailButton = document.querySelector('.compose-email');
const composeEmailWrapper = document.querySelector('.compose-email-wrapper');

const editor = new FroalaEditor('#email-body-wysiwyg');

composeEmailButton.addEventListener('click', function() {
    composeEmailWrapper.classList.add('open');
    composeEmailWrapper.querySelector('.email-to').focus();
});

const composeEmailBackground = document.querySelector('.compose-email-background');
composeEmailBackground.addEventListener('click', function() {
    composeEmailWrapper.classList.remove('open');
});


const sendEmailButton = composeEmailWrapper.querySelector('.send-email');
sendEmailButton.addEventListener('click', function() {
    const emailBodyInput = composeEmailWrapper.querySelector('.email-body');
    emailBodyInput.value = editor.html.get();

    const formData = new FormData();

    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('to', composeEmailWrapper.querySelector('.email-to').value);
    formData.append('cc', composeEmailWrapper.querySelector('.email-cc').value);
    formData.append('bcc', composeEmailWrapper.querySelector('.email-bcc').value);
    formData.append('subject', composeEmailWrapper.querySelector('.email-subject').value);
    formData.append('body', composeEmailWrapper.querySelector('.email-body').value);

    fetch('/compose_email', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
});