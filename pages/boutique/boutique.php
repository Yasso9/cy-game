<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_produit.php")
    ));

    if (isset($_COOKIE["panier"]))
    {
        $_SESSION["panier"] = json_decode($_COOKIE["panier"]);

        supprimer_cookie("panier");
    }

    $formulaireProduit = new FormulaireProduit(Etat::Creation);
    $afficherFormulaire = (
        isset($_SESSION["postFormulaireProduitCreation"]) &&
        isset($_SESSION["formulaireValide"]) &&
        $_SESSION["formulaireValide"] === false
    );
    if ($afficherFormulaire)
    {
        $formulaireProduit = $_SESSION["postFormulaireProduitCreation"];
    }
    unset($_SESSION["postFormulaireProduitCreation"]);
    unset($_SESSION["formulaireValide"]);

    InclusionFichier::debut("Boutique", false, "boutique.css");
?>

<?php 
    if (isset($_SESSION["messageAchat"]))
    {
        echo <<<HTML
        <section class="jumbotron jumbotron-fluid bg-warning text-dark">
            <div class="container">
                <h2>Produits Achetés !!!</h2>
                <p>{$_SESSION["messageAchat"]}</p>
            </div>
        </section>
        HTML;

        unset($_SESSION["messageAchat"]);
    }
?>

<div class="alert alert-danger alerte-non-autorise sticky-top" hidden>
    <strong>Attention !</strong> Il faut être membre pour ajouter des éléments au panier.
</div>

<section class="d-md-flex flex-row">
    <h2 class="display-4">Boutique CY Game</h2>
        
    <form class="ml-auto" method="get" action="">
        <div class="recherche input-group"> 
            <input
            class="form-control"
            type="search" 
            id="label-recherche" 
            name="nom_recherche"
            placeholder="Jeux, Goodies, Veste, ..."
            aria-label="Rechercher des éléments dans la boutique">
            
            <div class="input-group-append">
                <input class="btn btn-info" type="submit" name="rechercher" value="Rechercher">
            </div>
        </div>
    </form>
</section>

<section id="formulairePanier" class="formulaire-panier collapse sticky-top container-lg border border-dark rounded py-2">
    <form 
        class="conteneur-panier d-flex flex-column"
        method="post"
        action="/pages/boutique/requete_boutique.php">

        <!-- Met les données du panier stocké dans la session 
        pour pouvoir les lire avec javascript -->
        <div 
            class="donnees-panier" hidden
            data-panier='<?php 
                if (isset($_SESSION["panier"]) && !empty($_SESSION["panier"]))
                {
                    echo json_encode($_SESSION["panier"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            ?>'>
        </div>
        
        <header class="d-flex flex-row justify-content-between">
            <h3 class="titre-panier">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                Panier - Total : <span class="total-panier-affichage"></span> &euro;
                <input type="hidden" name="total_panier" class="total-panier">
            </h3>

            <div>
                <input 
                    class="bouton-commander-produits align-self-center btn btn-success" 
                    type="submit" 
                    name="commander_produits" 
                    value="Commander les articles">
                
                <button class="bouton-effacer-panier btn btn-danger" type="submit" name="effacer_panier">
                    Supprimer Panier <i class="fa fa-window-close" aria-hidden="true"></i>
                </button>
            </div>
        </header>

        <!-- Endroit ou vont s'afficher les éléments du panier -->
        <section class="panier d-flex flex-row flex-wrap justify-content-start"></section>
    </form>
</section>

<main>
    <?php
        $produits = new FormulaireProduit(Etat::Creation);

        $requeteSQL = <<<EOD
        SELECT * FROM `{$produits->recuperer_nom_tableau()}`;
        EOD;

        $listeProduits = $GLOBALS[g_baseDeDonnee]->requete($requeteSQL);
        $messageRecherche = "";
        $listeProduitsRecherches = array();

        // On regarder s'il y a une recherche
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            if (isset($_GET["rechercher"]))
            {
                if(isset($_GET["nom_recherche"]) && !empty($_GET["nom_recherche"]))
                {
                    $recherche = simplification_donnee($_GET["nom_recherche"]);
                    $recherche = $GLOBALS[g_baseDeDonnee]->echapper_chaine($recherche);

                    $requeteSQL = <<<EOD
                    SELECT * FROM `produit`
                    WHERE `nom_produit`
                    LIKE '%{$recherche}%';
                    EOD;

                    $listeProduitsRecherches = $GLOBALS[g_baseDeDonnee]->requete($requeteSQL);

                    if (empty($listeProduitsRecherches))
                    {
                        $messageRecherche = "Désolé, nous n'avons pas réussi à trouver votre produit";
                    }
                    else
                    {
                        $listeProduits = $listeProduitsRecherches;
                    }
                }
            }
        }

        // A CHANGER
        if ($messageRecherche != "")
        {
            echo <<<HTML
            <section class="jumbotron jumbotron-fluid bg-info text-dark">
                <div class="container">
                    <h2>La recherche à échoué !</h2>
                    <p>{$messageRecherche}</p>
                </div>
            </section>
            HTML;
        }
    ?>
    <section class="liste-produits">
        <h3 class="text-center">Listes des produits du site</h3>

        <div class="container-fluid d-flex flex-row flex-wrap justify-content-start">
            <?php

                echo $produits->afficher_produit($listeProduits);
            ?>
            <?php
                if ($_SESSION[g_utilisateurSession]->verification_role_element("admin"))
                {
                    echo <<<HTML
                    <article class="produit card bg-dark text-white m-1">
                    
                        <div class="card-header">
                            <h5 class="text-center">Ajouter un nouveau produit</h5>
                        </div>

                        <div class="card-body d-flex justify-content-center text-center my-auto">
        
                            <img class="ajouter-produit image-produit img-thumbnail" 
                                src="/styles/images/ajouter_produit.svg" 
                                alt="Ajouter un nouveau produit"
                                data-toggle="modal" data-target="#formulaireAjoutProduit">
                        </div>
        
                        <div class="card-footer">
                            <div class="text-center">Cette option n'est disponible que pour le vendeur du site</h5>
                        </div>
                    </article>
                    HTML;
                }
            ?>
        </div>
    </section>
</main>

<section class="modal" id="formulaireAjoutProduit">
    <div class="modal-dialog">
        <form class="modal-content non-selection rounded bg-dark shadow
            <?php echo $formulaireProduit->recuperer_classe_formulaire(); ?>" 
            method="post" action="/pages/boutique/requete_boutique.php" enctype="multipart/form-data" novalidate>
            <header class="modal-header">
                <h3 class="modal-title">Création d'un nouveau produit</h3>
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
                <input class="btn btn-success" type="submit" name="creation" value="Créer Produit">
            </footer>
        </form>
    </div>
</section>

<?php
    if ($afficherFormulaire)
    {
        echo <<<HTML
        <script>
            $('#formulaireAjoutProduit').modal('show');
        </script>
        HTML;

        echo <<<HTML
        <div class="post-formulaire" data-post="true"></div>
        HTML;
    }
?>

<script>
    // Pour ne plus avoir un formulaire prérempli lorsqu'on quitte un formulaire raté
    $('#formulaireAjoutProduit').on('hidden.bs.modal', function (evenement) 
    {
        // document.querySelector('.image-produit').remove();
        // Seuelement dans le cas d'une sauvegarde
        if (document.querySelector(".post-formulaire"))
        {
            window.location = window.location.href;
        }
    });
</script>

<script async defer type="module" src="./panier.js"></script>

<?php
    InclusionFichier::fin();
?>