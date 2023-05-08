<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_evenement.php")
    ));

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $formulaireAdmin = new FormulaireEvenement(Etat::ModificationAdmin);

        if (isset($_POST["suppression"]))
        {
            $id = $_POST["id"];
            
            $requeteSQL = <<<EOD
            DELETE FROM `{$formulaireAdmin->recuperer_nom_tableau()}` 
            WHERE `id_{$formulaireAdmin->recuperer_nom_tableau()}`='{$id}';
            EOD;
            
            $GLOBALS[g_baseDeDonnee]->requete($requeteSQL);
        }
        else if (isset($_POST["sauvegarder"]))
        {     
            $_SESSION["formulaireValide"] = $formulaireAdmin->exploiter_formulaire($_POST);    
            $_SESSION["postFormulaireEvenementAdmin"] = $formulaireAdmin;
        }
    }

    redirection ("/pages/administration/evenement_admin.php");
?>