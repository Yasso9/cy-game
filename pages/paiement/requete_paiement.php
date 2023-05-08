<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array(
            "/pages/paiement/formulaire_paiement.php",
            "/formulaire/formulaire_utilisateur.php",
            "/pages/evenement/participation_evenement.php",
            "/pages/boutique/achat_produit.php"
        )
    ));

    if (!isset($_SESSION["formulairePaiement"]) || 
        !isset($_SESSION["paiementRedirection"]) ||
        !isset($_SESSION["nomPaiement"]))
    {
        redirection ("/index.php");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (isset($_POST["paiement"]))
        {
            $paiement = new Paiement();
            if ($paiement->verifier_formulaire($_POST) === true)
            {
                if ($_SESSION["nomPaiement"] === "utilisateur")
                {
                    $_SESSION["formulairePaiement"]->finalisation_formulaire();
                }

                else if ($_SESSION["nomPaiement"] === "produit")
                {
                    $_SESSION["formulairePaiement"]->finalisation_commande();

                    $_SESSION["messageAchat"] = "Votre achat à été validé ! Merci d'avoir commandé sur la boutique CY GAME et à bientôt";
                    unset($_SESSION["panier"]);
                    supprimer_cookie("panier");
                }

                else if ($_SESSION["nomPaiement"] === "evenement")
                {
                    $_SESSION["formulairePaiement"]->valider_participation();
                    $_SESSION["messageParticipation"] = "Bravo !!! Vous allez participez à un nouvel événement !";
                }
                else
                {
                    throw new Exception("Votre paiement n'a pas été reconnu");
                }

                $pageRedirection = $_SESSION["paiementRedirection"];

                unset($_SESSION["formulairePaiement"]);
                unset($_SESSION["paiementRedirection"]);

                redirection ($pageRedirection);
            }
            else
            {
                $_SESSION["postFormulairePaiement"] = $paiement;
                redirection ("/pages/paiement/paiement.php");
            }
        }
    }

    redirection ("/index.php");
?>