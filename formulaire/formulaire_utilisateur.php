<?php
    inclure_fichier("/formulaire/donnees_globales.php");
    inclure_fichier("/formulaire/formulaire.php");

    final class FormulaireUtilisateur extends Formulaire
    {
        public function __construct(int $etat, bool $captchaIntegre = false)
        {
            $this->donnees = Donnees::donnees_utilisateur();
            $this->nomTableau = "utilisateur";

            parent::__construct($etat, $captchaIntegre, array("pseudo", "motDePasse"));
        }

        public function exploiter_formulaire (
            array $postFormulaire, 
            array $filesFormulaire = array()
        ) : bool
        {
            if ($this->etat === Etat::Connexion)
            {
                return $this->exploiter_formulaire_connexion($postFormulaire);
            }

            return parent::exploiter_formulaire($postFormulaire);
        }

        private function exploiter_formulaire_connexion (array $postFormulaire) : bool
        {
            $erreur = false;

            foreach ($this->donneesConnexion as $propriete)
            {
                if ($this->fixer_valeur($propriete, $postFormulaire[$propriete]) === false)
                {
                    $erreur = true;
                }
            }

            if ($erreur === true) { return false; }

            if ($this->connexion() === false) { return false; }

            $this->finalisation_formulaire();

            return true;
        }

        private function connexion() : bool
        {
            // On recuprère l'utilisateur demandé dans la requete SQL
            $donneesUtilisateur = $this->recuperer_donnees_sql_connexion();

            if ($donneesUtilisateur !== array())
            {
                $donneesUtilisateur = $donneesUtilisateur[0];
            }

            if ($this->etat === Etat::Connexion &&
                (
                    $donneesUtilisateur === array() ||
                    !password_verify($this->donnees["motDePasse"]["valeur"], $donneesUtilisateur[10])
                )
            )
            {
                $this->messageErreur = "Le pseudo et le mot de passe ne forment pas une bonne combinaison";
                return false;
            }

            // Les données sont maintenant validés, on peut effectuer les fonctions de fin
            $this->fixation_donnees_sql($donneesUtilisateur);
            return true;
        }

        private function recuperer_donnees_sql_connexion() : array
        {
            // CE CODE SERVAIT A RENDRE CETTE FONCTION ENCORE PLUS GENERIQUE

            // $donneesConnexionValeurs = array();
            // foreach ($this->donneesConnexion as $propriete)
            // {
            //     // Le mot de passe doit être vérifier à l'aide d'une table de hashage
            //     if ($this->donnees[$propriete]["type"] === "password")
            //     {
            //         continue;
            //     }

            //     $donneesConnexionValeurs[] = $this->donnees[$propriete]["valeur"];
            // }

            // var_dump($donneesConnexionValeurs);

            // On recupère l'utilisateur demandé dans la requete SQL
            return $GLOBALS[g_baseDeDonnee]->recherche_tableau(
                $this->nomTableau, 
                array("pseudo"), 
                array($this->donnees["pseudo"]["valeur"])
            );
        }

        public function finalisation_formulaire() : void
        {
            parent::finalisation_formulaire(function () {});

            // Pour que l'id soit pris directement en compte quand on s'inscrit
            // Il faut donc se connecter avant
            if ($this->etat === Etat::Creation)
            {
                if ($this->connexion() === false)
                {
                    throw new Exception("La connexion ne doit pas echoué si les données de l'utilisateur ont déjà été vérifié");
                }
            }

            $this->creation_session();
        }

        private function creation_session() : void
        {
            if ($this->etat !== Etat::ModificationAdmin)
            {
                $_SESSION[g_utilisateurSession]->deconnexion_rapide();
                foreach ($this->donnees as $propriete => $tableauDeValeur)
                {
                    $_SESSION[g_utilisateurSession]->fixer_donnee($propriete, $tableauDeValeur["valeur"]);
                }
            }
        }

        protected function verification_additionnel_enfant(string $propriete, string $valeur) : array /* (bool , string) */
        {
            $tableauBooleen  = array();
            $tableauMessageErreur = array();

            $tableauBooleen[] = (
                $this->donnees[$propriete]["type"] === "date" &&
                (int)(explode('-', $valeur)[0]) < 1850
            );
            $tableauMessageErreur[] = "Vous ne pouvez pas être aussi vieux";

            $tableauBooleen[] = (
                $this->donnees[$propriete]["type"] === "date" &&
                (int)(explode('-', $valeur)[0]) > 2021
            );
            $tableauMessageErreur[] = "Vous ne pouvez pas être aussi jeune";

            $tableauBooleen[] = ($this->donnees[$propriete] === "sexe" &&
                $valeur !== "homme" && 
                $valeur !== "femme" &&
                $valeur !== "autre"
            );
            $tableauMessageErreur[] = "Sexe non reconnu";

            $tableauBooleen[] = ($this->donnees[$propriete] === "role" &&
                $valeur !== "membre" && 
                $valeur !== "admin" &&
                $valeur !== "tresorier"
            );
            $tableauMessageErreur[] = "Rôle non reconnu";
            
            return combinaison_tableau($tableauMessageErreur, $tableauBooleen);   
        }

        public function recuperer_total_panier () : string
        {
            return g_prixCotisation;
        }

        public static function convertir_numero_en_propriete(int $numeroPropriete) : string
        {
            switch($numeroPropriete)
            {
            case 0:
                return "id";
                break;
            case 1:
                return "pseudo";
                break;
            case 2:
                return "sexe";
                break;
            case 3:
                return "naissance";
                break;
            case 4:
                return "profession";
                break;
            case 5:
                return "ville";
                break;
            case 6:
                return "role";
                break;
            case 7:
                return "nom";
                break;
            case 8:
                return "prenom";
                break;
            case 9:
                return "adresse";
                break;
            case 10:
                return "motDePasse";
                break;
            case 11:
                return "produitsAchetes";
                break;
            case 12:
                return "evenementsParticipes";
                break;
            default:
                throw new Exception("Le numero {$numeroPropriete} ne peut pas être converti en chaine représentant une propriété");
                return "";
                break;
            }
        }   
    }
?>