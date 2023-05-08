<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_produit.php")
    ));

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $formulaireAdmin = new FormulaireProduit(Etat::ModificationAdmin);

        if (isset($_POST["suppression"]))
        {
            $id = $_POST["id"];
    
            // On supprime aussi l'image lié à la boutique
            $requeteSQL = <<<EOD
            SELECT `image_{$formulaireAdmin->recuperer_nom_tableau()}`
            FROM `{$formulaireAdmin->recuperer_nom_tableau()}`
            WHERE `id_{$formulaireAdmin->recuperer_nom_tableau()}`='{$id}';
            EOD;
    
            supprimer_fichier($GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0][0]);
    
            $requeteSQL = <<<EOD
            DELETE FROM `{$formulaireAdmin->recuperer_nom_tableau()}` 
            WHERE `id_{$formulaireAdmin->recuperer_nom_tableau()}`='{$id}';
            EOD;
            
            $GLOBALS[g_baseDeDonnee]->requete($requeteSQL);
        }
        else if (isset($_POST["sauvegarder"]))
        {         
            $_SESSION["formulaireValide"] = $formulaireAdmin->exploiter_formulaire($_POST, $_FILES);
            $_SESSION["postFormulaireProduitAdmin"] = $formulaireAdmin;
        }
    }

    redirection ("/pages/administration/boutique_admin.php");
?>