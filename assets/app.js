import './styles/app.scss';

document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]'); // Bouton de menu mobile
    const mobileMenu = document.querySelector('#mobile-menu'); // Menu mobile

    mobileMenuButton.addEventListener('click', function() {
        const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true'; // Vérifie l'état actuel

        // Basculer l'attribut aria-expanded
        mobileMenuButton.setAttribute('aria-expanded', !isExpanded);

        // Basculer les classes pour afficher/masquer le menu
        mobileMenu.classList.toggle('hidden');
        mobileMenu.classList.toggle('block');
    });

    const profileMenuButton = document.querySelector('#user-menu-button'); // Bouton de menu de profil
    const profileMenu = document.querySelector('#user-menu'); // Menu de profil

    profileMenuButton.addEventListener('click', function() {
        const isExpanded = profileMenuButton.getAttribute('aria-expanded') === 'true'; // Vérifie l'état actuel

        // Basculer l'attribut aria-expanded
        profileMenuButton.setAttribute('aria-expanded', !isExpanded);

        // Basculer les classes pour afficher/masquer le menu
        profileMenu.classList.toggle('hidden');
        profileMenu.classList.toggle('block');
    });

    // Optionnel : fermer le menu lorsque l'utilisateur clique en dehors
    window.addEventListener('click', function(event) {
        if (!profileMenuButton.contains(event.target) && !profileMenu.contains(event.target)) {
            profileMenu.classList.add('hidden');
            profileMenuButton.setAttribute('aria-expanded', 'false');
        }
    });
});

