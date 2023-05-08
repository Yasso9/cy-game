<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_utilisateur.php")
    ));

    $_SESSION[g_utilisateurSession]->forcer_deconnexion();

    define("g_postFormulaire", "postFormulaireUtilisateurInscription");

    $formulaireUtilisateur = new FormulaireUtilisateur(Etat::Creation);
    if (isset($_SESSION[g_postFormulaire]))
    {
        $formulaireUtilisateur = $_SESSION[g_postFormulaire];
        unset($_SESSION[g_postFormulaire]);
    }

    InclusionFichier::debut("Inscription", true, "formulaire.css");
?>


<form method="post" action="/pages/utilisateur/requete_formulaire.php" novalidate
    class="mt-3 align-self-center non-selection rounded bg-dark shadow
    <?php echo $formulaireUtilisateur->recuperer_classe_formulaire(); ?>">

    <fieldset class="container-sm d-flex flex-column py-2">

        <legend class="mb-0 pb-0">Formulaire d'inscription CY Game</legend>
        
        <div>Vous possédez un compte ? Veuillez vous connecter <a href="connexion.php">ici</a></div>

        <?php
            echo $formulaireUtilisateur->creer_formulaire_html();
        ?>

        <!-- <div class="g-recaptcha align-self-center"
            data-sitekey="6Lc0OeoaAAAAAG6GSbmJoek2RID09-FrfmJLrGYk"
            data-callback="onSubmit"
            data-size="invisible">
        </div> -->

        <input class="btn btn-primary align-self-end" type="submit" name="inscription" value="S'inscrire">


    </fieldset>
</form>

<?php
    InclusionFichier::fin();
?>

