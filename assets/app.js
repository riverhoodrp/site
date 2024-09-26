import './styles/app.scss';

document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileNav = document.getElementById('mobile-nav');

    mobileMenuToggle.addEventListener('click', function() {
        mobileNav.classList.toggle('active');
    });
});