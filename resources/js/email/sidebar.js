const menuicon = document.querySelector('.menu-icon');
const sidebar = document.querySelector('.sidebar');

menuicon.addEventListener('click', function() {
    sidebar.classList.toggle('open');
    menuicon.classList.toggle('active');
});
