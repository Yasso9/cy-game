<?php
    final class Selection
    {
        private string $type;

        public function __construct (string $type)
        {
            if ($type !== "role" && $type !== "sexe")
            {
                throw new Exception("Le type {$type} n'est pas un type de selection");
            }

            $this->type = $type;
        }

        public function liste () : array
        {
            if ($this->type === "role")
            {
                return array(
                    "membre" => "Membre", 
                    "admin" => "Admin", 
                    "tresorier" => "Tresorier"
                );
            }
            else if ($this->type === "sexe")
            {
                return array(
                    "homme" => "Homme", 
                    "femme" => "Femme", 
                    "autre" => "Autre"
                );
            }
        }
    }
?>