<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_utilisateur.php")
    ));

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $formulaireAdmin = new FormulaireUtilisateur(Etat::ModificationAdmin);

        if (isset($_POST["suppression"]))
        {
            $id = $_POST["id"];

            if ($id === $_SESSION[g_utilisateurSession]->recuperer_donnee("id"))
            {
                $_SESSION["messageErreur"] = "Vous ne pouvez pas vous supprimer vous-même";
            }
            else
            {
                $requeteSQL = <<<EOD
                DELETE FROM `{$formulaireAdmin->recuperer_nom_tableau()}` 
                WHERE `id_{$formulaireAdmin->recuperer_nom_tableau()}`='{$id}';
                EOD;
                
                $GLOBALS[g_baseDeDonnee]->requete($requeteSQL);
            } 
        }
        else if (isset($_POST["sauvegarder"]))
        {         
            $id = $_POST["id"];

            if ($id === $_SESSION[g_utilisateurSession]->recuperer_donnee("id"))
            {
                $_SESSION["messageErreur"] = "Pour modifier votre profil il faut aller dans la page de modification de profil";
            }
            else
            {
                $_SESSION["formulaireValide"] = $formulaireAdmin->exploiter_formulaire($_POST);
                $_SESSION["postFormulaireUtilisateurAdmin"] = $formulaireAdmin;
            }
        }
    }

    redirection ("/pages/administration/utilisateur_admin.php");
?>