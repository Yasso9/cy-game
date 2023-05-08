<?php
    inclure_fichier("/formulaire/donnees_globales.php");
    inclure_fichier("/formulaire/formulaire.php");

    final class FormulaireEvenement extends Formulaire
    {
        public function __construct(int $etat)
        {
            $this->donnees = Donnees::donnees_evenement();
            $this->nomTableau = "evenement";

            parent::__construct($etat);
        }

        public static function convertir_numero_en_propriete(int $numeroPropriete) : string
        {
            switch($numeroPropriete)
            {
            case 0:
                return "id";
                break;
            case 1:
                return "nom";
                break;
            case 2:
                return "lieu";
                break;
            case 3:
                return "dateDebut";
                break;
            case 4:
                return "dateFin";
                break;
            case 5:
                return "description";
                break;
            case 6:
                return "prixParPersonne";
                break;
            case 7:
                return "cout";
                break;
            case 8:
                return "nombreParticipants";
                break;
            default:
                throw new Exception ("Le numero {$numeroPropriete} ne peut pas être converti en chaine représentant une propriété");
                return "";
                break;
            }
        }
    }
?>