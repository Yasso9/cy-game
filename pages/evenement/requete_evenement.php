<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array(
            "/formulaire/formulaire_evenement.php",
            "/pages/evenement/participation_evenement.php"
        )
    ));

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (isset($_POST["creer_evenement"]))
        {
            echo "on est la";
            $formulaireCreation = new FormulaireEvenement(Etat::Creation);
            $_SESSION["formulaireValideCreation"] = $formulaireCreation->exploiter_formulaire($_POST);
            $_SESSION["postFormulaireCreation"] = $formulaireCreation;
        }
        else if (isset($_POST["modifier_evenement"]))
        {
            $formulaireAdmin = new FormulaireEvenement(Etat::ModificationAdmin);
            $_SESSION["formulaireValideModification"] = $formulaireAdmin->exploiter_formulaire($_POST);    
            // echo $_SESSION["formulaireValideModification"];
            $_SESSION["postFormulaireModiffication"] = $formulaireAdmin;
        }
        else if (isset($_POST["participer_evenement"]))
        {
            if (!$_SESSION[g_utilisateurSession]->est_connecte())
            {
                $_SESSION["messageErreurEvenement"] = "Vous devez être membre pour pour participé à un événement";
                redirection ("/pages/evenement/evenement.php");
            }

            $idUtilisateur = $_SESSION[g_utilisateurSession]->recuperer_donnee("id");

            $requeteSQL = <<<EOD
            SELECT `evenementsParticipes_utilisateur`
            FROM `utilisateur` 
            WHERE `id_utilisateur`='{$idUtilisateur}';
            EOD;

            var_dump($GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0]);

            if (strpos($GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0][0], $_POST["participer_evenement"]) !== false)
            {
                $_SESSION["messageErreurEvenement"] = "Vous avez déjà participé à cette événement";
                // redirection ("/pages/evenement/evenement.php");
            }
            else
            {
                $participationEvenement = new ParticipationEvenement($_POST["participer_evenement"]);
    
                $_SESSION["formulairePaiement"] = $participationEvenement;
                $_SESSION["paiementRedirection"] = "/pages/evenement/evenement.php";
                $_SESSION["nomPaiement"] = "evenement";
                redirection ("/pages/paiement/paiement.php");
            }
        }
    }


    redirection ("/pages/evenement/evenement.php");
?>