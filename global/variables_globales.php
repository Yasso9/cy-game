<?php
    // Variable de la session utilisateur
    define("g_utilisateurSession", "utilisateur_actuel");

    define("g_prixCotisation", "30");

    define("g_pagePrincipale", "/index.php");

    
    // La fonctionnalité de pouvoir faire des requête doit être accessible 
    // dans tout les fichiers
    inclure_fichier("/base-de-donnee/base_de_donnee.php");
    define("g_baseDeDonnee", "base_de_donnee");
    $GLOBALS[g_baseDeDonnee] = new BaseDeDonnee();

?>