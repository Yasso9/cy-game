<?php
    inclure_fichier("/formulaire/formulaire_produit.php");

    final class AchatProduit
    {
        // Tableau associatif (clé = ID et valeur = stockAcheter)
        private array $stocksProduitAcheter;
        private array $donneesProduits;
        private string $messageErreur;
        private string $requeteSQLProduit;
        private string $totalPanier;

        public function __construct()
        {
            $this->stocksProduitAcheter = array();
            $this->donneesProduits = array();
            $this->messageErreur = "";
            $this->requeteSQLProduit = "";
        }  

        public function recuperer_erreur () : string
        {
            return $this->messageErreur;
        }

        public function recuperer_total_panier () : string
        {
            return $this->totalPanier;
        }

        public function exploiter_panier (array $postFormulaire) : bool
        {
            if ($this->initialiser_produit($postFormulaire) === false)
            {
                return false;
            }

            if ($this->test_final() === false)
            {
                return false;
            }

            return true;
        }
        
        private function initialiser_produit (array $postFormulaire) : bool
        {
            foreach ($postFormulaire as $element => $chaineIdEtStockAcheter)
            {
                // Nom du boutton submit, on a pas besoin de sa valeur
                if ($element === "commander_produits")
                {
                    continue;
                }
                else if ($element === "total_panier")
                {
                    // Dans ce cas la valeur de l'input est le total du panier
                    $this->totalPanier = $chaineIdEtStockAcheter;
                    continue;
                }

                // La valeur des inputs est de type id|stockacheter
                $tableau = preg_split('/[|]/', $chaineIdEtStockAcheter);

                if (sizeof($tableau) !== 2)
                {
                    throw new Exception("La valeur des inputs des articles doit être séparé en deux parties par un '|'");
                }

                // echo 'id : ' . $tableau[0] . '<br>';
                // echo 'stock : ' . $tableau[1] . '<br>';

                $this->stocksProduitAcheter[$tableau[0]] = $tableau[1];
            }

            if (empty($this->stocksProduitAcheter))
            {
                $this->messageErreur = "Vous n'avez commandez aucun produit";
                return false;
            }

            return true;
        }

        private function recuperer_donnees_sql () : void
        {
            foreach(array_keys($this->stocksProduitAcheter) as $idProduit)
            {
                $requeteSQL = <<<EOD
                SELECT * FROM `produit` WHERE `id_produit`='{$idProduit}';
                EOD;

                $this->donneesProduits[] = $this->etablir_tableau($GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0]);
            }
        }

        private function test_final () : bool
        {
            $this->recuperer_donnees_sql();

            foreach($this->donneesProduits as $produitAchete)
            {
                $nouveauStocks = $produitAchete["stock"] - $this->stocksProduitAcheter[$produitAchete["id"]];

                // echo 'id final : ' . $produitAchete["id"] . '<br>';
                // echo $this->stocksProduitAcheter[$produitAchete["id"]] . '<br>';
                // echo $produitAchete["stock"] . '<br>';
                // echo $nouveauStocks . '<br>';

                if ($nouveauStocks <= 0)
                {
                    $this->messageErreur = "Désolé, notre stock de '{$produitAchete["nom"]}' n'est pas assez élevé pour satisfaire votre commande";
                    return false;
                }

                $nouveaunombreAchat = $produitAchete["nombreAchat"] + $this->stocksProduitAcheter[$produitAchete["id"]];

                $this->requeteSQLProduit .= <<<EOD
                UPDATE `produit`
                SET 
                    `stock_produit`='{$nouveauStocks}', 
                    `nombreAchat_produit`='{$nouveaunombreAchat}'
                WHERE `id_produit`='{$produitAchete["id"]}';
                EOD;
            }

            return true;
        }

        public function finalisation_commande () : void
        {
            $this->mettre_a_jour_tableau_produit();
            $this->mettre_a_jour_tableau_utilisateur();
        }

        private function mettre_a_jour_tableau_produit () : void
        {   
            $GLOBALS[g_baseDeDonnee]->requete($this->requeteSQLProduit);
        }

        private function mettre_a_jour_tableau_utilisateur () : void
        {
            $idUtilisateur = $_SESSION[g_utilisateurSession]->recuperer_donnee("id");

            $requeteSQL = <<<EOD
            SELECT `produitsAchetes_utilisateur`
            FROM `utilisateur` 
            WHERE `id_utilisateur`='{$idUtilisateur}';
            EOD;

            $chaineProduitSQL = $GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0][0];
            // echo 'chaine recuperer : ' . $chaineProduitSQL . '<br>';
            
            foreach ($this->stocksProduitAcheter as $idProduit => $stocksAchete)
            {
                // echo 'chaine produit etape 0 :' . $chaineProduitSQL . '<br>';

                $ancienStocksAchetes = $this->ancien_produits_acheter_utilisateur($idProduit, $chaineProduitSQL);

                // echo 'chaine produit etape 1 :' . $chaineProduitSQL . '<br>';

                $stockFinal = (int)$stocksAchete + (int)$ancienStocksAchetes;
                // echo 'stock final :' . $stockFinal . '<br>';
                $chaineProduitSQL .= "|{$idProduit}-{$stockFinal}|";

                // echo 'chaine produit etape 2 :' . $chaineProduitSQL . '<br>';
            }

            $requeteSQL = <<<EOD
            UPDATE `utilisateur`
            SET `produitsAchetes_utilisateur`='{$chaineProduitSQL}'
            WHERE `id_utilisateur`='{$idUtilisateur}';
            EOD;

            $GLOBALS[g_baseDeDonnee]->requete($requeteSQL);
        }

        private function ancien_produits_acheter_utilisateur (string $idProduit, string &$chaineSQL) : string
        {
            $dernierePositionID = 0;
            while (($dernierePositionID = strpos($chaineSQL, $idProduit, $dernierePositionID)) !== false)
            {
                // echo 'dernier position chaine : ' . $dernierePositionID . '<br>';

                if ($dernierePositionID !== 0 && $chaineSQL[$dernierePositionID - 1] === '|')
                {
                    // echo 'position id : ' . $dernierePositionID . '<br>';
                    break;
                }

                $dernierePositionID = $dernierePositionID + 1;
            }

            
            $positionIDchaine = false;
            if ($dernierePositionID !== 0)
            {
                $positionIDchaine = $dernierePositionID;
            }
            
            $ancienStocksAchetes = "";
            if ($positionIDchaine !== false &&
                $chaineSQL[$positionIDchaine - 1] === '|')
            {
                $commencementAncienStock = false;

                $character = $positionIDchaine;
                while ($chaineSQL[$character] !== '|' && isset($chaineSQL[$character]))
                {
                    if ($commencementAncienStock === true)
                    {
                        $ancienStocksAchetes .= $chaineSQL[$character];
                    }

                    if ($chaineSQL[$character] === '-')
                    {
                        $commencementAncienStock = true;
                    }

                    $chaineSQL = substr_replace($chaineSQL, '', $character, 1);
                    // echo 'chaine recuperer en cour : ' .$chaineSQL . '<br>';
                }

                // echo 'ancien stock : ' .$ancienStocksAchetes . '<br>';

                // echo 'chaine recuperer 2 : ' .$chaineSQL . '<br>';

                $chaineSQL = substr_replace($chaineSQL, '', $character-1, 1);
                $chaineSQL = substr_replace($chaineSQL, '', $character, 1);
            }

            return $ancienStocksAchetes;
        }

        // On change les clé de notre tableau pour avoir un code plus clair
        private function etablir_tableau (array $tableauSQL) : array
        {
            $tableauNomPropriete = array();

            for ($cleSQL = 0; $cleSQL <= 7; ++$cleSQL)
            {
                $tableauNomPropriete[] = FormulaireProduit::convertir_numero_en_propriete($cleSQL);
            }

            return array_combine($tableauNomPropriete, array_values($tableauSQL));
        }
    }

?>