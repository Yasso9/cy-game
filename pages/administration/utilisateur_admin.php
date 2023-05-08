<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_utilisateur.php")
    ));

    $_SESSION[g_utilisateurSession]->verification_role_page("admin");
    
    define("g_postFormulaire", "postFormulaireUtilisateurAdmin");
    define("g_formulaireValide", "formulaireValide");

    $formulaireUtilisateur = new FormulaireUtilisateur(Etat::ModificationAdmin);

    $messageErreur = "";
    if (isset($_SESSION["messageErreur"]))
    {
        $messageErreur = $_SESSION["messageErreur"];
        unset($_SESSION["messageErreur"]);
    }

    // Booleen pour savoir si il faut réafficher le formulaire
    $reafficherFormulaire = (
        isset($_SESSION[g_postFormulaire]) &&
        isset($_SESSION[g_formulaireValide]) && 
        $_SESSION[g_formulaireValide] === false &&
        empty($messageErreur)
    );

    if ($reafficherFormulaire)
    {
        $formulaireUtilisateur = $_SESSION[g_postFormulaire];
    }

    unset($_SESSION[g_postFormulaire]);
    unset($_SESSION[g_formulaireValide]);

    InclusionFichier::debut("Admin Utilisateurs", false, "admin.css");
?>

<div class="donnees-completes" data-tableau="<?php
    $tableauDesUtilisateurs = $GLOBALS[g_baseDeDonnee]->recherche_tableau($formulaireUtilisateur->recuperer_nom_tableau());

    echo htmlspecialchars(json_encode($tableauDesUtilisateurs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
?>"></div>


<section class="liste-donnees container-sm mb-4">
    <h5 class="display-4 text-center py-3">Listes des utilisateurs du site</h4>

    <?php
        if (!empty($messageErreur))
        {
            echo <<<HTML
            <article class="jumbotron jumbotron-fluid rounded bg-info">
                <div class="container text-center">
                    <h2>Problème lors de la modification</h2>
                    <p>{$messageErreur}</p>
                </div>
            </article>
            HTML;
        }
    ?>

    <ul class="list-group">
        <?php
            echo $GLOBALS[g_baseDeDonnee]->creer_boutons_gestion(
                $formulaireUtilisateur->recuperer_nom_tableau()
            );
        ?>
    </ul>
</section>

<section class="modal fade" id="formulaireAdmin">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content non-selection rounded bg-dark shadow
            <?php echo $formulaireUtilisateur->recuperer_classe_formulaire(); ?>" 
            method="post" action="/pages/administration/requete_utilisateur_admin.php" novalidate>

            <header class="modal-header">
                <legend class="modal-title">Gestion utilisateur</legend>
                <button type="button" class="fermeture-modal" data-dismiss="modal">
                    <i class="fa fa-window-close" aria-hidden="true"></i>
                </button>
            </header>
            
            <fieldset class="modal-body">
                <?php
                    echo $formulaireUtilisateur->creer_formulaire_html();
                ?>
            </fieldset>
            
            <footer class="modal-footer">
                <input class="sauvegarde btn btn-success" type="submit" name="sauvegarder" value="Sauvegarder Choix">
                <input class="suppression btn btn-danger" type="submit" name="suppression" value="Supprimer Utilisateur">
            </footer>
        </form>
    </div>
</section>

<?php
    if ($reafficherFormulaire)
    {                     
        echo <<<HTML
        <script>
            $('#formulaireAdmin').modal('show');
        </script>
        HTML;

        echo <<<HTML
        <div class="post-sauvegarder" data-post="true"></div>
        HTML;
    }
?>

<script type="module">
    import {RemplissageFormulaire} from './remplissage_formulaire.js';
    let remplissageFormulaire = new RemplissageFormulaire();
</script>

<script>
    // Pour ne plus avoir les variables POST
    $('#formulaireAdmin').on('hidden.bs.modal', function (evenement) 
    {
        // Seuelement dans le cas d'une sauvegarde
        if (document.querySelector(".post-sauvegarder"))
        {
            window.location = window.location.href;
        }
    });
</script>


<?php
    InclusionFichier::fin();
?>