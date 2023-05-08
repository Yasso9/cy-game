<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_utilisateur.php")
    ));

    $_SESSION[g_utilisateurSession]->forcer_connexion();

    define("g_postFormulaire", "postFormulaireUtilisateurModification");

    $formulaireUtilisateur = new FormulaireUtilisateur(Etat::ModificationPerso);
    $formulaireUtilisateur->fixation_donnees_sql($GLOBALS[g_baseDeDonnee]->recherche_tableau(
        $formulaireUtilisateur->recuperer_nom_tableau(), 
        array("id"),
        array($_SESSION[g_utilisateurSession]->recuperer_donnee("id"))
    )[0]);

    if (isset($_SESSION[g_postFormulaire]))
    {
        $formulaireUtilisateur = $_SESSION[g_postFormulaire];
        unset($_SESSION[g_postFormulaire]);
    }

    InclusionFichier::debut("Modification Profil", false, "formulaire.css");
?>

<form method="post" action="/pages/utilisateur/requete_formulaire.php" novalidate
    class="mt-3 align-self-center non-selection rounded bg-dark shadow
    <?php echo $formulaireUtilisateur->recuperer_classe_formulaire(); ?>">

    <fieldset class="container-sm d-flex flex-column py-2">

        <legend class="mb-0 pb-0">Modification de votre compte</legend>
        
        <?php
            echo $formulaireUtilisateur->creer_formulaire_html();
        ?>

        <input class="btn btn-primary align-self-end" type="submit" name="modification" value="Modifier">

    </fieldset>
</form>

<?php
    InclusionFichier::fin();
?>
