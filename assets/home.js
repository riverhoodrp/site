const $ = require("jquery");
$(document).ready(function() {
    console.log('Hello jQuery! Edit me in assets/app.js');
    function updatePlayerCount() {
        $.ajax({
            url: 'http://localhost:8000/players-count',  // Appel vers la route Symfony pour obtenir le nombre de joueurs
            type: 'GET',
            success: function(response) {
                // Si la requête est réussie et que l'API répond correctement
                if (response.success) {
                    if (response.count === 1) {
                        $('.players').text("Il y a actuellement " + response.count + " joueur en ligne");  // Mise à jour du texte avec le nombre de joueurs
                    } else if (response.count > 1) {
                            $('.players').text("Il y a actuellement " + response.count + " joueurs en ligne");
                    } else {
                        $('.players').text("Aucun joueur en ligne");
                    }
                } else {
                    $('.players').text('Erreur lors de la récupération des données');
                }
            },
            error: function() {
                // En cas d'erreur lors de la requête
                $('.players').text('Impossible de récupérer les données');
            }
        });
    }

    // Appel initial pour charger les données immédiatement à l'ouverture de la page
    updatePlayerCount();

    // Refaire la requête toutes les 5 secondes
    setInterval(updatePlayerCount, 5000);
});