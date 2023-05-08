<?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";

    inclure_fichier("/global/variables_globales.php");
    inclure_fichier("/global/fonctions_utiles.php");
    inclure_fichier("/global/utilisateur_site.php");

    // Cas ou on doit inclure un fichier avant d'initialiser la session
    // Soivent utilisé dans les formulaires
    if (isset($fichiersAvantSession))
    {
        foreach ($fichiersAvantSession as $fichier)
        {
            inclure_fichier($fichier);
        }
    }

    inclure_fichier("/global/session.php");
    Session::initialisation_session();

    if (
        !isset($_SESSION[g_utilisateurSession]) ||
        $_SESSION[g_utilisateurSession]->est_cree() === false
    )
    {
        UtilisateurSite::reinitialiser_utilisateur();
    }

    // var_dump($_SESSION[g_utilisateurSession]->recuperer_donnees_completes());

    inclure_fichier("/global/inclusion_fichiers.php");
?>