<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array("/formulaire/formulaire_evenement.php")
    ));

    
    $formulaireEvenementAdmin = new FormulaireEvenement(Etat::ModificationAdmin);
    $afficherFormulaireAdmin = (
        isset($_SESSION["postFormulaireModiffication"]) &&
        isset($_SESSION["formulaireValideModification"]) &&
        $_SESSION["formulaireValideModification"] === false
    );
    if ($afficherFormulaireAdmin)
    {
        $formulaireEvenementAdmin = $_SESSION["postFormulaireModiffication"];
    }
    unset($_SESSION["postFormulaireModiffication"]);
    unset($_SESSION["formulaireValideModification"]);




    $formulaireEvenementCreation = new FormulaireEvenement(Etat::Creation);
    $afficherFormulaireCreation = (
        isset($_SESSION["postFormulaireCreation"]) &&
        isset($_SESSION["formulaireValideCreation"]) &&
        $_SESSION["formulaireValideCreation"] === false
    );
    if ($afficherFormulaireCreation)
    {
        $formulaireEvenementCreation = $_SESSION["postFormulaireCreation"];
    }
    unset($_SESSION["postFormulaireCreation"]);
    unset($_SESSION["formulaireValideCreation"]);

    InclusionFichier::debut("Événement", false, "evenement.css");
?>

<?php 
    if (isset($_SESSION["messageErreurEvenement"]))
    {
        echo <<<HTML
        <section class="jumbotron jumbotron-fluid bg-info text-dark">
            <div class="container">
                <h2>Problème !!!</h2>
                <p>{$_SESSION["messageErreurEvenement"]}</p>
            </div>
        </section>
        HTML;

        unset($_SESSION["messageErreurEvenement"]);
    }
    else if (isset($_SESSION["messageParticipation"]))
    {
        echo <<<HTML
        <section class="jumbotron jumbotron-fluid bg-info text-dark">
            <div class="container">
                <h2>MERCI !!!</h2>
                <p>{$_SESSION["messageParticipation"]}</p>
            </div>
        </section>
        HTML;

        unset($_SESSION["messageParticipation"]);
    }
?>

<!-- L'envoyer diretement à javascript via une variable -->
<div 
    class="donnees-evenements" 
    data-admin='<?php 
        echo $_SESSION[g_utilisateurSession]->verification_role_element ("admin");
    ?>'
    data-tableau='<?php
        $tableauDesEvenements = $GLOBALS[g_baseDeDonnee]->recherche_tableau($formulaireEvenementCreation->recuperer_nom_tableau());

        echo htmlspecialchars(json_encode($tableauDesEvenements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    ?>'
    data-evenements='<?php 
        $tableauDesEvenements = $GLOBALS[g_baseDeDonnee]->recherche_tableau(
            $formulaireEvenementCreation->recuperer_nom_tableau()
        );

        $tableauEnvoieJavascript = array();

        foreach ($tableauDesEvenements as $tableauEvenement)
        {
            $tableauTemporaire = array();

            foreach (Donnees::donnees_evenement($tableauEvenement) as $donnee => $tableauDeValeurs)
            {
                $tableauTemporaire[$donnee] = array(
                    "valeur" => $tableauDeValeurs["valeur"], 
                    "nom" => $tableauDeValeurs["nom"]
                );
            }

            $tableauEnvoieJavascript[] = $tableauTemporaire;
        }

        echo htmlspecialchars(json_encode($tableauEnvoieJavascript, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    ?>'>
</div>

<!-- Le calendrier sera créer à l'aide de javascript à l'intérieur -->
<section class="conteneur-calendrier non-selection container-fluid mt-3"></section>

<section class="modal" id="modalAfficherEvenement">
    <div class="modal-dialog">
        <div class="modal-content non-selection rounded bg-dark shadow">
        </div>
    </div>
</section>

<section class="modal creation-evenement" id="modalCreationEvenement">
    <div class="modal-dialog">
        <form class="modal-content non-selection rounded bg-dark shadow
            <?php echo $formulaireEvenementCreation->recuperer_classe_formulaire(); ?>" 
            method="post" action="/pages/evenement/requete_evenement.php" novalidate>
            <header class="modal-header">
                <legend class="modal-title">Création d'un nouvel evenement</legend>
                <button type="button" class="fermeture-modal" data-dismiss="modal">
                    <i class="fa fa-window-close" aria-hidden="true"></i>
                </button>
            </header>
            
            <fieldset class="modal-body">
                <?php
                    echo $formulaireEvenementCreation->creer_formulaire_html();
                ?>
            </fieldset>
            
            <footer class="modal-footer">
                <button class="btn btn-success" type="submit" name="creer_evenement">Créer Événement</button>
            </footer>
        </form>
    </div>
</section>

<section class="modal" id="modalModifierEvenement">
    <div class="modal-dialog">
        <form class="modal-content non-selection rounded bg-dark shadow
            <?php echo $formulaireEvenementAdmin->recuperer_classe_formulaire(); ?>" 
            method="post" action="/pages/evenement/requete_evenement.php" novalidate>
            <header class="modal-header">
                <legend class="modal-title">Modification de l'évenement</legend>
                <button type="button" class="fermeture-modal" data-dismiss="modal">
                    <i class="fa fa-window-close" aria-hidden="true"></i>
                </button>
            </header>
            
            <fieldset class="modal-body">
                <?php
                    echo $formulaireEvenementAdmin->creer_formulaire_html();
                ?>
            </fieldset>
            
            <footer class="modal-footer">
                <input class="btn btn-success" type="submit" name="modifier_evenement" value="Modifier">
            </footer>
        </form>
    </div>
</section>

<script type="module" src="./calendrier_final.js"></script>
<script type="module">
    import {CalendrierFinal} from './calendrier_final.js';

    let calendrier = new CalendrierFinal(new Date(), ".conteneur-calendrier");
</script>

<?php
    if ($afficherFormulaireAdmin)
    {                     
        echo <<<HTML
        <script>
            $('#modalModifierEvenement').modal('show');
        </script>
        HTML;

        echo <<<HTML
        <div class="post-modification" data-post="true"></div>
        HTML;
    }
    if ($afficherFormulaireCreation)
    {                     
        echo <<<HTML
        <script>
            $('#modalCreationEvenement').modal('show');
        </script>
        HTML;

        echo <<<HTML
        <div class="post-creation" data-post="true"></div>
        HTML;
    }
?>

<script>
    // Pour ne plus avoir les variables POST
    $('#modalModifierEvenement').on('hidden.bs.modal', function (evenement) 
    {
        // Seuelement dans le cas d'une sauvegarde
        if (document.querySelector(".post-modification"))
        {
            window.location = window.location.href;
        }
    });
    // Pour ne plus avoir les variables POST
    $('#modalCreationEvenement').on('hidden.bs.modal', function (evenement) 
    {
        // Seuelement dans le cas d'une sauvegarde
        if (document.querySelector(".post-creation"))
        {
            window.location = window.location.href;
        }
    });
</script>


<!-- <script src="aides_formulaire_evenement.js"></script> -->
<?php
    InclusionFichier::fin();
?>