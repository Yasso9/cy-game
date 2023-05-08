<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/global.php";
    InclusionFichier::debut("Informations Profil", false, "information_profil.css");

    $_SESSION[g_utilisateurSession]->forcer_connexion();
?>

<main class="mt-4">
    <h2 class="text-center">Liste des produits achetés</h2>
    <section class="container-fluid d-flex flex-row flex-wrap">
        <?php
            $idUtilisateur = $_SESSION[g_utilisateurSession]->recuperer_donnee("id");

            $requeteSQL = <<<EOD
            SELECT `produitsAchetes_utilisateur`
            FROM `utilisateur`
            WHERE `id_utilisateur`='{$idUtilisateur}';
            EOD;

            $chaineProduitAcheteSQL = $GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0][0];

            // Les produits sont séparé par des '|' dans la chaine
            $tableauProduit = preg_split('/[|]/', $chaineProduitAcheteSQL);
            foreach ($tableauProduit as $produit)
            {
                if ($produit === "")
                {
                    continue;
                }

                // Un produit contient un id et un stock acheté par l'utilisateur séparé par un '-'
                $tableau = preg_split('/[-]/', $produit);

                $idProduit = $tableau[0];
                $stockAchete = $tableau[1];

                // echo 'id : ' . $idProduit . '<br>';
                // echo 'stock : ' . $stockAchete . '<br>';

                $requeteSQL = <<<EOD
                SELECT *
                FROM `produit` 
                WHERE `id_produit`='{$idProduit}';
                EOD;

                $donneesProduit = $GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0];

                if (!chemin_fichier_existe($donneesProduit[2]))
                {
                    $donneesProduit[2] = '/styles/images/image-produit-non-disponible.svg';
                }

                echo <<<HTML
                <article class="produit card bg-dark text-white my-1">

                    <header class="card-header">
                        <h5 class="text-center">{$donneesProduit[1]}</h5>
                    </header>

                    <main class="card-body d-flex justify-content-center">
                        <img 
                            class="image-produit img-thumbnail" 
                            src="{$donneesProduit[2]}" 
                            alt="Image du produit">
                    </main>

                    <footer class="card-footer text-nowrap text-center">
                            <div>Prix du produit : {$donneesProduit[4]} &euro;</div>
                            <div>Stock(s) Acheté(s) : {$stockAchete}</div>
                    </footer>

                </article>
                HTML;
            }
        ?>
    </section>

    <h2 class="text-center">Liste des événements auxquelles vous allez ou avez participés</h2>
    <section class="container-fluid d-flex flex-row flex-wrap">
        <?php
            $idUtilisateur = $_SESSION[g_utilisateurSession]->recuperer_donnee("id");

            $requeteSQL = <<<EOD
            SELECT `evenementsParticipes_utilisateur`
            FROM `utilisateur`
            WHERE `id_utilisateur`='{$idUtilisateur}';
            EOD;

            $chaineEvenementSQL = $GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0][0];

            // Les produits sont séparé par des '|' dans la chaine
            $tableauID = preg_split('/[|]/', $chaineEvenementSQL);
            foreach ($tableauID as $idEvenement)
            {
                if ($idEvenement === "")
                {
                    continue;
                }

                $requeteSQL = <<<EOD
                SELECT *
                FROM `evenement` 
                WHERE `id_evenement`='{$idEvenement}';
                EOD;

                $donneesEvenement = $GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0];

                echo <<<HTML
                <article class="evenement card bg-dark text-white my-1">

                    <header class="card-header">
                        <h5 class="text-center">{$donneesEvenement[1]}</h5>
                    </header>

                    <main class="card-body text-center">
                        <div>Lieu : {$donneesEvenement[2]}</div>

                        <p>Description : {$donneesEvenement[5]}</p>
                    </main>

                    <footer class="card-footer text-nowrap text-center">
                        <div>
                            Prix de l'événement : 
                            <span class="prix-produit">{$donneesEvenement[6]}</span> 
                            &euro;
                        </div>
                        <div>
                            Nombres de participants: 
                            <span class="prix-produit">{$donneesEvenement[8]}</span>
                        </div>
                    </footer>

                </article>
                HTML;
            }
        ?>
    </section>
</main>

<?php
    InclusionFichier::fin();
?>