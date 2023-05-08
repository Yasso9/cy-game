<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_produit.php")
    ));

    if (isset($_SERVER["CONTENT_LENGTH"])) 
    {
        if ($_SERVER["CONTENT_LENGTH"] > ((int)ini_get('post_max_size') * 1024 * 1024)) 
        {
            // Faire une meilleurs redirection en cas d'erreur
            redirection ("/index.php");
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (isset($_POST["creation"]))
        {
            $formulaireProduit = new FormulaireProduit(Etat::Creation);
            $_SESSION["formulaireValide"] = $formulaireProduit->exploiter_formulaire($_POST, $_FILES);
            $_SESSION["postFormulaireProduitCreation"] = $formulaireProduit;
        }
        else if (isset($_POST["commander_produits"]))
        {
            inclure_fichier("/pages/boutique/achat_produit.php");
            $achatProduits = new AchatProduit();
            if ($achatProduits->exploiter_panier($_POST) === true)
            {
                // L'utilisateur va aller payer
                $_SESSION["formulairePaiement"] = $achatProduits;
                $_SESSION["paiementRedirection"] = "/pages/boutique/boutique.php";
                $_SESSION["nomPaiement"] = "produit";
                redirection ("/pages/paiement/paiement.php");
            }
            else
            {
                $_SESSION["messageAchat"] = $achatProduits->recuperer_erreur();
            }
        }
        else if (isset($_POST["effacer_panier"]))
        {
            unset($_SESSION["panier"]);
            supprimer_cookie("panier");
        }
    }

    redirection ("/pages/boutique/boutique.php");
?>