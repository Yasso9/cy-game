<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_utilisateur.php")
    ));

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (isset($_POST["connexion"]))
        {
            $formulaireUtilisateur = new FormulaireUtilisateur(Etat::Connexion);
            if ($formulaireUtilisateur->exploiter_formulaire($_POST) === false)
            {
                $_SESSION["postFormulaireUtilisateurConnexion"] = $formulaireUtilisateur;
                redirection ("/pages/utilisateur/connexion.php");
            }
        }
        else if (isset($_POST["inscription"]))
        {
            $formulaireUtilisateur = new FormulaireUtilisateur(Etat::Creation);
            if ($formulaireUtilisateur->exploiter_formulaire($_POST) === false)
            {
                $_SESSION["postFormulaireUtilisateurInscription"] = $formulaireUtilisateur;
                redirection ("/pages/utilisateur/inscription.php");
            }
            else
            {
                // L'utilisateur va aller payer
                $_SESSION["formulairePaiement"] = $formulaireUtilisateur;
                $_SESSION["paiementRedirection"] = "/index.php";
                $_SESSION["nomPaiement"] = "utilisateur";
                redirection ("/pages/paiement/paiement.php");
            }
        }
        else if (isset($_POST["modification"]))
        {
            $formulaireUtilisateur = new FormulaireUtilisateur(Etat::ModificationPerso);
            $formulaireUtilisateur->fixation_donnees_sql($GLOBALS[g_baseDeDonnee]->recherche_tableau(
                $formulaireUtilisateur->recuperer_nom_tableau(), 
                array("id"),
                array($_SESSION[g_utilisateurSession]->recuperer_donnee("id"))
            )[0]);
            
            if ($formulaireUtilisateur->exploiter_formulaire($_POST) === false)
            {
                $_SESSION["postFormulaireUtilisateurModification"] = $formulaireUtilisateur;
                redirection ("/pages/utilisateur/modification_profil.php");
            }
        }
    }

    redirection ("/index.php");
?>