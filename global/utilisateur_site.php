<?php
    final class UtilisateurSite
    {
        private array $donnees;

        public function __construct()
        {
            $this->deconnexion_rapide();
        }

        // Verifie si toute les propriétés sont bien créerpour la variable de session
        // Au cas ou la classe change et on voudrait rajouté des propriétés
        public function est_cree() : bool
        {
            return (isset($this->donnees));
        }

        public function recuperer_donnee(string $propriete) : string
        {
            return $this->donnees[$propriete];
        }

        public function recuperer_donnees_completes(): array
        {
            return $this->donnees;
        }

        public function fixer_donnee (string $propriete, string $valeur) : void
        {
            $this->donnees[$propriete] = $valeur;
        }

        // public function fixer_donnees_complete (array $tableauDonnees) : void
        // {
        //     $this->donnees = $tableauDonnees;
        // }

        public function est_connecte () : bool
        {
            if ($this->donnees === array())
            {
                return false;
            }
    
            return true;
        }

        public function deconnexion_rapide () : void
        {
            $this->donnees = array();
        }

        public function deconnexion_complete () : void
        {
            $this->deconnexion_rapide();
            Session::effacer_session();
            redirection_page_principale();
        }

        public function forcer_deconnexion () : void
        {
            // Enfaite ça sert à rien mais on garde quand même on sait jamais
            // Permet de lire les données du formulaires (post même si l'utilisateur viens de se connecter)
            // if ($this->nomPagePrecedente !== "" &&
            //     page_actuel() === $this->nomPagePrecedente && 
            //     $_SERVER['REQUEST_METHOD'] === 'POST')
            // {
            //     return;
            // }

            if ($this->est_connecte() === true)
            {
                $this->deconnexion_complete();
                return;
            }
        }

        public function forcer_connexion () : void
        {
            if ($this->est_connecte() !== true)
            {
                redirection_page_principale();
                return;
            }
        }

        // Empeche quelqu'un de rentré dans une page si il n'a pas le bon role
        public function verification_role_page (string $role) : void
        {
            $this->forcer_connexion();
            
            if ($this->donnees["role"] !== $role)
            {
                redirection_page_principale();
                return;
            }
        }

        // Verifie juste si quelqu'un à le bon role pour pouvoir affiché des éléments spécifiques dans une page
        public function verification_role_element (string $role) : bool
        {
            return 
            (
                $this->est_connecte() &&
                $this->donnees["role"] === $role
            );
        }

        static function reinitialiser_utilisateur () : void
        {
            $_SESSION[g_utilisateurSession] = new UtilisateurSite();
        }
    }
?>