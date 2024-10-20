import './styles/app.scss';
import '../node_modules/tinymce/tinymce.min.js';

const $ = require('jquery');
global.$ = global.jQuery = $;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Hello Webpack Encore! Edit me in assets/app.js');
    const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
    const mobileMenu = document.querySelector('#mobile-menu');

    mobileMenuButton.addEventListener('click', function () {
        const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';

        mobileMenuButton.setAttribute('aria-expanded', !isExpanded);

        mobileMenu.classList.toggle('hidden');
        mobileMenu.classList.toggle('block');
    });

    const profileMenuButton = document.querySelector('#user-menu-button');
    const profileMenu = document.querySelector('#user-menu');

    profileMenuButton.addEventListener('click', function () {
        const isExpanded = profileMenuButton.getAttribute('aria-expanded') === 'true';

        profileMenuButton.setAttribute('aria-expanded', !isExpanded);

        profileMenu.classList.toggle('hidden');
        profileMenu.classList.toggle('block');
    });

    // Optionnel : fermer le menu lorsque l'utilisateur clique en dehors
    window.addEventListener('click', function (event) {
        if (!profileMenuButton.contains(event.target) && !profileMenu.contains(event.target)) {
            profileMenu.classList.add('hidden');
            profileMenuButton.setAttribute('aria-expanded', 'false');
        }
    });

    const arrow = document.querySelector('.arrow-up');

    arrow.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    })

    window.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        const arrow = document.querySelector('.arrow-up');

        if (window.scrollY > 200) {
            navbar.classList.add('transparent');
            navbar.classList.add('scroll');
            arrow.classList.add('visible');
            arrow.classList.add('animate-jump-in');
            arrow.classList.add('animate-once');
            arrow.classList.add('animate-ease-in-out');
        } else {
            navbar.classList.remove('transparent');
            navbar.classList.remove('scroll');
            arrow.classList.remove('visible');
            arrow.classList.remove('animate-jump-in');
            arrow.classList.remove('animate-once');
            arrow.classList.remove('animate-ease-in-out');
        }
    });
});

