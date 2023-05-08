<?php

    Final class Paiement
    {
        protected array $donnees;

        protected string $messageErreur;

        private string $classeFormulaire;
        
        public function __construct()
        {
            $this->messageErreur = "";
            $this->classeFormulaire = "needs-validation";
            $this->donnees = array(
                "numeroCarte1" => array("", ""),
                "numeroCarte2" => array("", ""),
                "numeroCarte3" => array("", ""),
                "numeroCarte4" => array("", ""),
                "dateExpiration" => array("", ""),
                "cryptogramme" => array("", ""),
            );
        }

        public function recuperer_donnee (string $propriete) : string
        {
            return $this->donnees[$propriete][0];
        }

        public function recuperer_donnees () : array
        {
            return $this->donnees;
        }

        public function recuperer_erreur_general () : string
        {
            return $this->messageErreur;
        }

        public function recuperer_erreur (string $propriete) : string
        {
            return $this->donnees[$propriete][1];
        }

        public function recuperer_classe_formulaire () : string
        {
            return $this->classeFormulaire;
        }

        public function verifier_formulaire (array $postFormulaire) : bool
        {
            $this->classeFormulaire = "was-validated";
            $erreur = false;

            foreach ($postFormulaire as $propriete => $valeur)
            {
                // echo 'valeur : ' . $valeur . '<br>';
                if ($valeur === "Payer")
                {
                    continue;
                }

                // if ($propriete === "dateExpiration")
                // {
                //     $this->donnees[$propriete] = array($valeur . 'T', "");
                // }
                // else
                // 
                // }

                if ($this->verification_valeur($propriete, $valeur) === true)
                {
                    $erreur = true;
                }
            }

            if ($erreur === true)
            {
                return false;
            }
            return true;
        }

        private function verification_valeur (string $propriete, string $valeur) : bool
        {
            if (!isset($valeur) || empty($valeur))
            {
                $this->messageErreur = "Il y a une erreur dans le formulaire de paiement";
                $this->donnees[$propriete][1] = "Vous devez remplir cette donnée";
                $this->donnees[$propriete][0] = "";
                return true;
            }
            else if (($propriete !== "dateExpiration" && !is_numeric((string)$valeur)))
            {
                $this->messageErreur = "Il y a une erreur dans le formulaire de paiement";
                $this->donnees[$propriete][1] = "Ne peut pas être autre chose qu'un chiffre ou un nombre";
                $this->donnees[$propriete][0] = "";
                return true;
            }
            else if (
                (
                    $propriete === "numeroCarte1" ||
                    $propriete === "numeroCarte2" ||
                    $propriete === "numeroCarte3" ||
                    $propriete === "numeroCarte4"
                ) &&
                ((int)$valeur > 9999 || (int)$valeur < 1000)
            )
            {
                $this->messageErreur = "Chaque case de la carte bleu doit être constitué de 4 chiffres";
                $this->donnees[$propriete][1] = "Doit être constitué de 4 chiffres";
                $this->donnees[$propriete][0] = "";
                return true;
            }
            else if (
                $propriete === "cryptogramme" && 
                ((int)$valeur > 999 || (int)$valeur < 100)
            )
            {
                $this->messageErreur = "Le cryptogramme derrière votre carte bleu doit faire 3 chiffres";
                $this->donnees[$propriete][1] = "Doit être constitué de 3 chiffres";
                $this->donnees[$propriete][0] = "";
                return true;
            }
            else if (
                $propriete === "dateExpiration" &&
                (
                    (int)substr($valeur, 0, 4) < (int)date("Y") ||
                    (int)substr($valeur, 0, 4) === (int)date("Y") &&
                    (
                        (int)substr($valeur, 5, 2) < (int)date("m") ||
                        (
                            (int)substr($valeur, 5, 2) === (int)date("m") &&
                            (int)substr($valeur, 8, 2) <= (int)date("d")
                        )
                    )
                )
            )
            {
                $this->messageErreur = "Votre carte est expirée";
                $this->donnees[$propriete][1] = "Doit expiré après aujourd'hui";
                $this->donnees[$propriete][0] = "";
                return true;
            }

            $this->donnees[$propriete] = array($valeur, "");

            return false;
        }
    }


?>