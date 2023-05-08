<?php
    // Cherche tous les cas d'erreurs
    error_reporting(-1);
    // mettre 0 si on met le site en production

    // Affiche les erreurs 
    ini_set("display_error", 1);

    function inclure_fichier (string $nomFichier, array $variables = array()) : void
    {
        extract($variables);

        require_once $_SERVER['DOCUMENT_ROOT'] . $nomFichier;
    }
?>