<?php
    inclure_fichier("/base-de-donnee/chaine_sql.php");

    final class Etat
    {
        const Creation = 0;
        const Connexion = 1;
        const ModificationPerso = 2;
        const ModificationAdmin = 3;
    }

    abstract class DonneesFormulaire
    {
        protected array $donnees;

        // Les données unique lorsque il y'a un besoin de mofifier ces donnees pour s'en souvenir
        protected array $donneesPrecedentes;

        // Les donnees constituant le formulaire permettant à l'utilisateur de se connecter
        protected array $donneesConnexion;

        // Le nom du tableau dans la base de données SQL
        protected string $nomTableau;

        // Prend des valeurs de la classe (Enumeration) Etat
        protected int $etat;

        // Savoir si on doit prendre en compte le captcha de google
        protected bool $captchaIntegre;

        // Message qui s'affiche en haut du formulaire dans le cas d'une erreur
        protected string $messageErreur;

        // .was-validated ou .needs-validation
        private string $classeFormulaire;
        
        public function __construct(int $etat, bool $captchaIntegre = false, array $donnees_connexion = array())
        {
            $this->donneesPrecedentes = array();

            $this->messageErreur = "";

            $this->etat = $etat;

            $this->captchaIntegre = $captchaIntegre;
            
            if ($this->etat === Etat::Connexion)
            {
                $this->donneesConnexion = $donnees_connexion;
            }

            $this->classeFormulaire = "needs-validation";
        }

        public function recuperer_donnee(string $propriete, string $type = "valeur") : string
        {
            if ($this->donnees[$propriete]["type"] === "datetime-local" && $type === "valeur")
            {
                return $this->convertir_datetime_en_sql($this->donnees[$propriete][$type]);
            }

            return $this->donnees[$propriete][$type];
        }

        public function recuperer_donnees_completes(): array
        {
            return $this->donnees;
        }

        public function recuperer_nom_tableau() : string
        {
            return $this->nomTableau;
        }

        public function recuperer_classe_formulaire () : string
        {
            return $this->classeFormulaire;
        }

        public function fixer_message_erreur(string $messageErreur) : void
        {
            $this->messageErreur = $messageErreur;
        }
        

        public function exploiter_formulaire (
            array $postFormulaire, 
            array $filesFormulaire = array()) : bool
        {
            $this->classeFormulaire = "was-validated";

            // Captcha Google
            if ($this->captchaIntegre === true && 
            $this->verifier_captcha($postFormulaire['captcha-response']) === false)
            {
                return false;
            }
            
            $erreur = false;
            
            foreach ($this->donnees as $propriete => $valeur)
            {
                if ($this->etat !== Etat::ModificationAdmin && $valeur["secret"] === true ||
                    $propriete === "produitsAchetes" || $propriete === "evenementsParticipes")
                {
                    continue;
                }
                else if ($valeur["type"] === "file")
                {
                    if ($this->verification_fichier($filesFormulaire, $propriete) === false)
                    {
                        // echo "erreur fichier";
                        $this->messageErreur = "Problème avec le fichier";
                        $erreur = true;
                    }

                    continue;
                }

                // Sert dans le cas de la modification du profil
                if ($valeur["unique"] === true &&
                    $this->etat_modification()
                )
                {
                    $this->donneesPrecedentes[$propriete] = $postFormulaire[$propriete . '_precedente'];
                }
                
                // On ajoute la valeur du formulaire dans le tableau de données
                if ($this->fixer_valeur($propriete, $postFormulaire[$propriete]) === false)
                {
                    $erreur = true;
                }
            }

            if ($erreur === true) 
            { 
                if ($this->nomTableau === "utilisateur")
                {
                    $this->donnees["motDePasse"]["valeur"] = "";
                }

                return false; 
            }

            // Cas spécifique pour le remplissage du nouveau mot de passe dans une modification
            if ($this->etat === Etat::ModificationPerso &&
                isset($postFormulaire["nouveauMotDePasse"]) &&
                !empty(simplification_donnee($postFormulaire["nouveauMotDePasse"])))
            {
                if ($this->fixer_valeur("motDePasse", $postFormulaire["nouveauMotDePasse"], true) === false)
                {
                    return false;
                }

                // if ($erreur === true) { return false; }
            }

            if ($this->etat !== Etat::Creation || $this->nomTableau !== "utilisateur")
            {
                $this->finalisation_formulaire();
            }
            return true;
        }
        
        // Encapsulateur du tableau dimensionnelle "donnees"
        protected function fixer_valeur(string $propriete, string $valeur, bool $hasherMDP = false) : bool
        {              
            $valeur = simplification_donnee($valeur);  

            // Cela nous permet de ne pas trop surchargé la fonction
            $tableauVerification = $this->verification_additionnel($propriete, $valeur);

            foreach($tableauVerification as $verification)
            {
                if ($verification[1])
                {
                    $this->donnees[$propriete]["erreur"] = $verification[0];

                    if ($this->messageErreur === "")
                    {
                        $this->messageErreur = "Une donnée est erronée ({$this->donnees[$propriete]['nom']})";
                    }
                    else
                    {
                        $this->messageErreur = "Plusieurs données sont erronées";
                    }

                    return false;
                }
            }

            if (($this->donnees[$propriete]["type"] === "password" &&
                $this->etat === Etat::Creation) ||
                $hasherMDP === true)
            {
                $valeur = password_hash($valeur, PASSWORD_DEFAULT);
            }

            // Si on est toujours pas sortie de la fonction la valeur est bonne
            $this->donnees[$propriete]["valeur"] = $valeur;
            return true;
        }

        protected function verification_additionnel_enfant(string $propriete, string $valeur) : array
        {
            return array();
        }

        private function verification_additionnel(string $propriete, string $valeur) : array /* (bool , string) */
        {
            $tableauBooleen  = array();
            $tableauMessageErreur = array();

            $tableauBooleen[] = (
                (!isset($valeur) || 
                    (
                        $this->donnees[$propriete]["type"] !== "number" && 
                        empty($valeur)
                    )) &&
                $this->donnees[$propriete]["requis"] === true
            );
            $tableauMessageErreur[] = "Vous devez remplir cette donnée";
        
            $tableauBooleen[] = (
                (
                    $this->donnees[$propriete]["type"] === "text" ||
                    $this->donnees[$propriete]["type"] === "textarea" ||
                    $this->donnees[$propriete]["type"] === "password"
                ) && 
                preg_replace('/\s+/', '', $valeur) === "" &&
                $valeur !== ""
            );
            $tableauMessageErreur[] = "Il ne peut pas y avoir une donnée constituée seulement d'espace";

            $tableauBooleen[] = (
                (
                    $this->donnees[$propriete]["type"] === "text" ||
                    $this->donnees[$propriete]["type"] === "textarea"
                ) && 
                !preg_match("/^[a-zÀ-ÿ0-9-'_ .]*$/i", $valeur)
            );
            $tableauMessageErreur[] = "Seulement les lettres, chiffres, espace, '-', '_' autorisés";

            $tableauBooleen[] = ($this->donnees[$propriete]["type"] === "number"  && 
                !is_numeric((string)$valeur)
            );
            $tableauMessageErreur[] = "Seulement des chiffres et nombres autorisés";

            $tableauBooleen[] = (
                $this->donnees[$propriete]["type"] === "password" && 
                !preg_match("/^[a-zÀ-ÿ0-9'-_ .$\/]*$/i", $valeur)
            );
            $tableauMessageErreur[] = "Seulement les lettres, chiffres, espace, '-', '_', '.', '$', '/' autorisés";

            $tableauBooleen[] = (
                $this->donnees[$propriete]["unique"] === true && 
                (
                    $this->etat === Etat::Creation || 
                    (
                        $this->etat_modification() &&
                        $this->donneesPrecedentes !== array() &&
                        (string)$valeur !== $this->donneesPrecedentes[$propriete]
                    ) 
                ) &&
                $GLOBALS[g_baseDeDonnee]->recherche_tableau(
                    $this->nomTableau, 
                    array($propriete), 
                    array($GLOBALS[g_baseDeDonnee]->echapper_chaine($valeur))
                ) !== array()
            );
            $tableauMessageErreur[] = "Un utilisateur à déjà choisi cette valeur, veuillez en choisir une autre";

            $tableauVerification = combinaison_tableau($tableauMessageErreur, $tableauBooleen);
            $tableauVerificationParent = $this->verification_additionnel_enfant($propriete, $valeur);
            foreach ($tableauVerificationParent as $valeur)
            {
                $tableauVerification[] = $valeur;
            }

            return $tableauVerification;
        }

        // Instruction final lorsque qu'on sait que les données du formulaire ont été validés
        public function finalisation_formulaire() : void
        {
            if ($this->etat === Etat::Creation)
            {
                // echo $this->donnees["id"]["valeur"];
                $GLOBALS[g_baseDeDonnee]->requete(ChaineSQL::ajouter_donnees($this->nomTableau, $this->donnees));
            }
            else if ($this->etat_modification())
            {
                $GLOBALS[g_baseDeDonnee]->requete(ChaineSQL::actualiser_donnees($this->nomTableau, $this->donnees));
            }
        }

        public function recuperer_chemin_image ($propriete) : string
        {
            $requeteSQL = <<<EOD
            SELECT `{$propriete}_{$this->nomTableau}`
            FROM `{$this->nomTableau}`
            WHERE `id_{$this->nomTableau}`='{$this->donnees["id"]["valeur"]}';
            EOD;

            return $GLOBALS[g_baseDeDonnee]->requete($requeteSQL)[0][0]; 
        }

        private function verification_fichier (array $filesFormulaire, string $propriete) : bool
        {
            if (!isset($filesFormulaire) ||
                !isset($filesFormulaire[$propriete]) ||
                $filesFormulaire[$propriete]["name"] === "")
            {
                // Dans un état de modification, le fichier peut resté le même si l'utilisateur ne veut pas le changer
                if ($this->etat_modification() === true)
                {
                    $this->donnees[$propriete]["valeur"] = $this->recuperer_chemin_image($propriete);
                    return true;
                }
                else if ($this->donnees[$propriete]["requis"] === false)
                {
                    $this->donnees[$propriete]["valeur"] = "/styles/images/image-produit-non-disponible.svg";
                    return true;
                }

                $this->donnees[$propriete]["erreurs"] = "Le fichier n'a pas été remplis";
                $this->messageErreur = "Le fichier n'a pas été précisé";
                $this->donnees[$propriete]["valeur"] = "";
                return false;
            }
            
            // Si on souhaite changer de fichier il faut supprimer l'ancien
            else if ($this->etat_modification() === true)
            {
                supprimer_fichier($this->recuperer_chemin_image($propriete));
            }

            $fichier = $this->initialiser_donnees_fichier($filesFormulaire[$propriete]);
            foreach ($fichier as $donnee)
            {
                if (empty($donnee))
                {
                    $this->donnees[$propriete]["erreurs"] = "Le fichier n'est pas pris en charge";
                    $this->donnees[$propriete]["valeur"] = "";
                    return false;
                }
            }

            // echo '| ' . $fichier["nom"] . '| <br>';
            // echo '| ' . $fichier["taille"] . '| <br>';
            // echo '| ' . $fichier["cheminTemporaire"] . '| <br>';
            // echo '| ' . $fichier["type"] . '| <br>';

            // Check if image file is a actual image or fake image
            if (!getimagesize($fichier["cheminTemporaire"])) 
            {
                $this->donnees[$propriete]["erreurs"] = "Le fichier doit être une image";
                $this->donnees[$propriete]["valeur"] = "";
                return false;
            } 
            else if (file_exists($fichier["cheminFinal"])) 
            {
                $this->donnees[$propriete]["erreurs"] = "Ce fichier existe déjà";
                $this->donnees[$propriete]["valeur"] = "";
                return false;
            }
            else if ($fichier["taille"] > 500000) 
            {
                $this->donnees[$propriete]["erreurs"] = "Le fichier est trop grand";
                $this->donnees[$propriete]["valeur"] = "";
                return false;
            }
            else if ($fichier["type"] != "jpg" && 
                $fichier["type"] != "png" && 
                $fichier["type"] != "jpeg" &&
                $fichier["type"] != "svg" && 
                $fichier["type"] != "gif" 
            ) 
            {
                $this->donnees[$propriete]["erreurs"] = "Désolé, seulement les images JPG, SVG, JPEG, PNG et GIF sont autorisés.";
                $this->donnees[$propriete]["valeur"] = "";
                return false;
            }
            else if (!move_uploaded_file($fichier["cheminTemporaire"], $fichier["cheminFinal"])) 
            {
                $this->donnees[$propriete]["erreurs"] = "Il y a eu une erreur lors de l'enregistrement de votre fichier, veuillez recommencer.";
                $this->donnees[$propriete]["valeur"] = "";
                return false;
            } 

            $this->donnees[$propriete]["valeur"] = str_replace('../..', '', $fichier["cheminFinal"]);
            return true;
        }

        private function initialiser_donnees_fichier (array $fichierFormulaire) : array
        {
            $repertoireImages = "../../base-de-donnee/images-boutique/";
            if (!is_dir($repertoireImages)) { mkdir($repertoireImages); }

            $fichier = array (
                "nom" => simplification_donnee($fichierFormulaire['name']),
                "taille" => $fichierFormulaire['size'],
                "cheminTemporaire"  => $fichierFormulaire['tmp_name'],
            );
            
            $fichier["type"] = strtolower(pathinfo($fichier["nom"], PATHINFO_EXTENSION));
            $fichier["cheminFinal"] = $repertoireImages . $fichier["nom"]; 

            return $fichier;
        }

        public function verifier_captcha ($formulaireCaptcha) : bool
        {
            if (!isset($formulaireCaptcha) || empty($formulaireCaptcha))
            {
                $this->messageErreur = "Validation du robot n'est pas accepter";
                return false;
            }

            $cleSecrete = "6Lc0OeoaAAAAALRHGte13umOmXLbgM3JzMFNBk1A";
            
            $verifierReponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='
                . $cleSecrete
                . '&response='
                . $formulaireCaptcha
            );

            $responseData = json_decode($verifierReponse);

            if(!$responseData->success)
            {
                $this->messageErreur = "La validation du robot n'a pas fonctionné, veuillez réessayer";
                return false;
            }

            return true;
        }

        
        public function fixation_donnees_sql (array $tableauDeDonneesSQL) : void
        {
            foreach ($tableauDeDonneesSQL as $numeroPropriete => $valeur) 
            {
                $chainePropriete = $this::convertir_numero_en_propriete($numeroPropriete);

                if ( $this->donnees[$chainePropriete]["type"] === "datetime-local")
                {
                    $this->convertir_datetime_en_html($valeur);
                }

                $this->donnees[$chainePropriete]["valeur"] = $valeur;
            }
        }

        // Permet de simplifier l'écriture
        protected function etat_modification () : bool
        {
            return (
                $this->etat === Etat::ModificationAdmin ||
                $this->etat === Etat::ModificationPerso
            );
        }

        protected function convertir_datetime_en_sql (string &$datetimeHTML) : void
        {
            $datetimeHTML[10] = ' ';
        }

        protected function convertir_datetime_en_html (string &$datetimeSQL) : void
        {
            $datetimeSQL[10] = 'T';
        }

        abstract public static function convertir_numero_en_propriete(int $numeroPropriete) : string;
    }
?>