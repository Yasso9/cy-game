<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/base.php";
    inclure_fichier("/global/global.php", array(
        "fichiersAvantSession" => array(
            "/pages/paiement/formulaire_paiement.php",
            "/formulaire/formulaire_utilisateur.php",
            "/pages/evenement/participation_evenement.php",
            "/pages/boutique/achat_produit.php")
    ));

    if (!isset($_SESSION["formulairePaiement"]) || 
        !isset($_SESSION["paiementRedirection"]) ||
        !isset($_SESSION["nomPaiement"]))
    {
        redirection ("/index.php");
    }

    $paiement = new Paiement();
    if (isset($_SESSION["postFormulairePaiement"]))
    {
        $paiement = $_SESSION["postFormulairePaiement"];
    }
    unset($_SESSION["postFormulairePaiement"]);

    InclusionFichier::debut("Paiement", false, "paiement.css");

    // function convertir_datetime_en_html (string &$datetimeSQL) : void
    // {
    //     $datetimeHTML[10] = 'T';
    //     return $datetimeHTML
    // }
?>
<?php echo $paiement->recuperer_donnee("dateExpiration"); ?>

<main class="d-flex flex-column mt-4">
    <h2 class="text-center">Procédure de paiement</h2>

    <form 
        class="mt-3 p-4 w-75 align-self-center non-selection rounded bg-dark shadow non-selection 
        <?php echo $paiement->recuperer_classe_formulaire(); ?>"
        method="post" 
        action="/pages/paiement/requete_paiement.php" 
        novalidate>

        <fieldset class="d-flex flex-column">

            <legend>
                Paiement d'un total de 
                <?php echo $_SESSION["formulairePaiement"]->recuperer_total_panier() ?> 
                &euro;
            </legend>

            <div class="erreur"><?php echo $paiement->recuperer_erreur_general(); ?></div>
            <small class="form-text text-muted mb-2">Note : '*' indique que la case est requise</small>
            <small class="form-text text-muted mb-2">
                Note : aucune données ne sera récupéré, 
                vous pouvez donc rentrer n'importe quelle code
            </small>

            <div class="form-group">
                <label for="numeroCarte">Numero Carte Bleu</label>
                <div class="form-row">

                    <div class="col">
                        <input class="form-control" name="numeroCarte1"
                            type="number" placeholder="XXXX" id="label-numeroCarte" required
                            step="1" maxlength="4" size="4"
                            value='<?php echo $paiement->recuperer_donnee("numeroCarte1"); ?>'>

                        <div class="invalid-feedback">
                            <?php echo $paiement->recuperer_erreur("numeroCarte1"); ?>
                        </div>
                    </div>

                    <div class="col">
                        <input class="form-control" name="numeroCarte2"
                            type="number" placeholder="XXXX" id="label-numeroCarte" required
                            maxlength="4" size="4" step="1"
                            value='<?php echo $paiement->recuperer_donnee("numeroCarte2"); ?>'>

                        <div class="invalid-feedback">
                            <?php echo $paiement->recuperer_erreur("numeroCarte2"); ?>
                        </div>
                    </div>

                    <div class="col">
                        <input class="form-control" name="numeroCarte3"
                            type="number" placeholder="XXXX" id="label-numeroCarte" required
                            maxlength="4" size="4" step="1"
                            value='<?php echo $paiement->recuperer_donnee("numeroCarte3"); ?>'>

                        <div class="invalid-feedback">
                            <?php echo $paiement->recuperer_erreur("numeroCarte3"); ?>
                        </div>
                    </div>

                    <div class="col">
                        <input class="form-control" name="numeroCarte4"
                            type="number" placeholder="XXXX" id="label-numeroCarte" required
                            maxlength="4" size="4" step="1"
                            value='<?php echo $paiement->recuperer_donnee("numeroCarte4"); ?>'>

                        <div class="invalid-feedback">
                            <?php echo $paiement->recuperer_erreur("numeroCarte4"); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col from-group">
                    <label for="dateExpiration">Date d'expiration</label>

                    <input class="form-control" name="dateExpiration"
                        type="date" id="label-dateExpiration" required
                        value='<?php echo $paiement->recuperer_donnee("dateExpiration"); ?>'>

                    <div class="invalid-feedback">
                        <?php echo $paiement->recuperer_erreur("dateExpiration"); ?>
                    </div>
                </div>

                <div class="col from-group">
                    <label for="code">Cryptogramme</label>
                    <input class="form-control" name="cryptogramme"
                        type="number" id="label-cryptogramme" required
                        placeholder="code à 3 chiffres au dos de votre carte"
                        maxlength="3" size="3" step="1"
                        value='<?php echo $paiement->recuperer_donnee("cryptogramme"); ?>'>

                    <div class="invalid-feedback">
                        <?php echo $paiement->recuperer_erreur("cryptogramme"); ?>
                    </div>
                </div>
            </div>

            <input class="btn btn-success align-self-end pb-2 mt-4" type="submit" name="paiement" value="Payer">

        </fieldset>
    </form>

    <section class="d-sm-flex flex-row align-self-center mt-4">
        <figure>
            <img class="my-4 mr-4" src="/styles/images/paiement/visa.png" alt="Visa">
        </figure>
    
        <figure>
            <img class="mr-4" src="/styles/images/paiement/mastercard.png" alt="Visa">
        </figure>
    </section>

    
</main>

<?php
    InclusionFichier::fin();
?>