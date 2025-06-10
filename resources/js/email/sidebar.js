const menuicon = document.querySelector('.menu-icon');
const sidebar = document.querySelector('.sidebar');

menuicon.addEventListener('click', function() {
    sidebar.classList.toggle('open');
    menuicon.classList.toggle('active');
});


const currentAccount = document.querySelector('.current-account');
const accountSelector = document.querySelector('.account-selector');
currentAccount.addEventListener('click', function() {
    accountSelector.classList.toggle('open');
});


const newEmailButton = document.querySelector('.new-email');
const emailForm = document.querySelector('.compose-email-wrapper');

newEmailButton.addEventListener('click', function() {
    emailForm.classList.toggle('open');
});
