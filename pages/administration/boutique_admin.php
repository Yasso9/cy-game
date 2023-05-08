<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_produit.php")
    ));
    
    $_SESSION[g_utilisateurSession]->verification_role_page("admin");

    define("g_postFormulaire", "postFormulaireProduitAdmin");
    define("g_formulaireValide", "formulaireValide");

    $formulaireProduit = new FormulaireProduit(Etat::ModificationAdmin);

    // Booleen pour savoir si il faut réafficher le formulaire
    $reafficherFormulaire = (
        isset($_SESSION[g_postFormulaire]) &&
        isset($_SESSION[g_formulaireValide]) && 
        $_SESSION[g_formulaireValide] === false
    );

    if ($reafficherFormulaire)
    {
        $formulaireProduit = $_SESSION[g_postFormulaire];
    }

    unset($_SESSION[g_postFormulaire]);
    unset($_SESSION[g_formulaireValide]);

    InclusionFichier::debut("Admin Boutique", false, "admin.css");
?>

<div class="donnees-completes" data-tableau="<?php
    $tableauDesProduits = $GLOBALS[g_baseDeDonnee]->recherche_tableau($formulaireProduit->recuperer_nom_tableau());

    echo htmlspecialchars(json_encode($tableauDesProduits, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
?>"></div>


<section class="liste-donnees container-sm mb-4">
    <h5 class="display-4 text-center py-3">Listes des produits</h4>

    <ul class="list-group">
        <?php
            echo $GLOBALS[g_baseDeDonnee]->creer_boutons_gestion(
                $formulaireProduit->recuperer_nom_tableau()
            );
        ?>
    </ul>
</section>

<section class="modal fade" id="formulaireAdmin">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded bg-dark shadow <?php echo $formulaireProduit->recuperer_classe_formulaire(); ?>" 
            method="post" action="/pages/administration/requete_boutique_admin.php" 
            enctype="multipart/form-data" novalidate>

            <header class="modal-header">
                <legend class="modal-title">Modification du produit</legend>
                <button type="button" class="fermeture-modal" data-dismiss="modal">
                    <i class="fa fa-window-close" aria-hidden="true"></i>
                </button>
            </header>
            
            <fieldset class="modal-body">
                <?php
                    echo $formulaireProduit->creer_formulaire_html();
                ?>
            </fieldset>
            
            <footer class="modal-footer">
                <input class="sauvegarde btn btn-success" type="submit" name="sauvegarder" value="Sauvegarder Choix">
                <input class="suppression btn btn-danger" type="submit" name="suppression" value="Supprimer Produit">
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
    // Pour ne plus avoir un formulaire prérempli lorsqu'on quitte un formulaire raté
    $('#formulaireAdmin').on('hidden.bs.modal', function (evenement) 
    {
        document.querySelector('.image-produit').remove();
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