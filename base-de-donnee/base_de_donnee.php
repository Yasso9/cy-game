<?php
    final class BaseDeDonnee
    {
        private string $nomServeur;
        private string $nomUtilisateur;
        private string $motDePasse;

        private mysqli $mysqli;

        function __construct ()
        {
            $this->nomServeur = "127.0.0.1";
            $this->nomUtilisateur = "root";
            $this->motDePasse = "";

            if (!function_exists('mysqli_init') && !extension_loaded('mysqli')) {
                echo 'We don\'t have mysqli!!!';
            }

            // A voir si on enlève à la fin du projet
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->mysqli = new mysqli($this->nomServeur, $this->nomUtilisateur, $this->motDePasse);

            if ($this->mysqli->connect_error)
            {
                throw new Exception("Connection à la base de donnée echouée: " . $this->mysqli->connect_error);
            }

            // Requete a faire pour les test du projet, à enlever pour le rendu final
            $this->requete_fichier("base-de-donnee/SQL/enregistrement_donnees.sql");
            // $this->requete_fichier("/home/cygame/www/base-de-donnee/SQL/enregistrement_donnees.sql");

            // On utilise notre base de données (celle du site)
            $requeteSQL = <<<EOD
            USE `cygame_db`;
            EOD;
            $this->requete($requeteSQL);

        }

        function __destruct () 
        {
            $this->mysqli->close();
        }

        public function requete (string $requeteSQL) : array
        {      
            if (!$this->mysqli->multi_query($requeteSQL))
            {
                echo "Erreur lors de l'envoie de la requête: " . $this->mysqli->error;
                return array();
            }

            // On recupère les resulats de la requete (s'il y'en a)
            $tableau = array();
            do 
            {
                if ($result = $this->mysqli->store_result()) 
                {
                    while ($row = $result->fetch_row()) 
                    {
                        array_push($tableau, $row);
                    }

                    $result->free();
                }
            } while ($this->mysqli->next_result());

            return $tableau;
        }

        public function requete_fichier (string $cheminFichier) : array
        {
            $cheminFichier = $_SERVER['DOCUMENT_ROOT'] . $cheminFichier;

            $fichier = fopen($cheminFichier, "r") or die("Impossible d'ouvrir le fichier ${cheminFichier}");
            $requete = fread($fichier, filesize($cheminFichier));
            fclose($fichier);

            return $this->requete($requete);
        }

        public function recherche_tableau (string $nomTableau, array $proprietes = array(), array $valeurs = array()) : array
        {
            $chaineCondition = "";
            
            if ($proprietes !== array() || $valeurs !== array())
            {
                $chaineCondition .= " WHERE ";
                foreach (array_combine($proprietes, $valeurs) as $propriete => $valeur)
                {
                    // BINARY sert a prendre en considération la casse
                    $chaineCondition .= "`{$propriete}_{$nomTableau}` = BINARY '{$valeur}' AND ";
                }
    
                // On efface le " AND " (5 caractères) qu'il reste à la fin de la chaine
                $chaineCondition = substr_replace($chaineCondition, "", -5, 4);
            }

            $requeteSQL = <<<EOD
            SELECT * FROM `{$nomTableau}`{$chaineCondition};
            EOD;

            // echo $requeteSQL;
            
            return $this->requete($requeteSQL);
        }

        // Creer des bouttons qui permette la gestion de la base de donnee du tableau entree en parametre
        public function creer_boutons_gestion (string $nomTableau) : string
        {
            $tableauDesUtilisateurs = $this->recherche_tableau($nomTableau);

            $htmlCode = "";

            foreach ($tableauDesUtilisateurs as $donnees) 
            {
                $htmlCode .= <<<HTML
                <li class="list-group-item list-group-item-info text-center p-0">
                    <input 
                        class="bouton_admin w-100 h-100 py-0" 
                        id="{$donnees[0]}" 
                        type="button"
                        value="{$donnees[0]} - {$donnees[1]}"
                        data-toggle="modal" data-target="#formulaireAdmin">
                </li>
                HTML;
            }

            return $htmlCode;
        }

        public function echapper_chaine (string $chaine) : string
        {
            return $this->mysqli->real_escape_string($chaine);
        }
    }
?>