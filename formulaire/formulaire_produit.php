<?php
    inclure_fichier("/formulaire/donnees_globales.php");
    inclure_fichier("/formulaire/formulaire.php");

    final class FormulaireProduit extends Formulaire
    {
        public function __construct(int $etat)
        {
            $this->donnees = Donnees::donnees_boutique();
            $this->nomTableau = "produit";

            parent::__construct($etat);
        }

        public function afficher_produit (array $listeProduitsCompletes) : string
        {
            $htmlListesProduits = "";
            foreach ($listeProduitsCompletes as $tableauProduit)
            {
                $this->fixation_donnees_sql($tableauProduit);

                if (!chemin_fichier_existe($this->donnees['image']['valeur']))
                {
                    $this->donnees['image']['valeur'] = '/styles/images/image-produit-non-disponible.svg';
                }

                $boutonHTML = "";
                if ($_SESSION[g_utilisateurSession]->verification_role_element("admin") ||
                    $_SESSION[g_utilisateurSession]->verification_role_element("membre"))
                {
                    $boutonHTML .= <<<HTML
                    <input
                        class="bouton-ajouter-panier btn btn-light btn-sm" 
                        id="{$this->donnees['id']['valeur']}"
                        type="button" 
                        value="Ajouter au panier">
                    HTML;
                }
                else
                {
                    $boutonHTML .= <<<HTML
                    <input
                        class="bouton-ajouter-panier-non btn btn-light btn-sm" 
                        type="button" 
                        value="Ajouter au panier">
                    HTML;
                }

                $htmlListesProduits .= <<<HTML
                <article class="produit card bg-dark text-white m-1">

                    <header class="card-header">
                        <h5 class="text-center">{$this->donnees["nom"]["valeur"]}</h5>
                        <small class="marque-produit">{$this->donnees["marque"]["valeur"]}</small>
                    </header>

                    <main class="card-body d-flex justify-content-center">
                        <img 
                            class="image-produit img-thumbnail" 
                            src="{$this->donnees['image']['valeur']}" 
                            alt="Image du produit">
                    </main>

                    <footer class="card-footer text-nowrap d-sm-flex flex-row justify-content-between">
                        <div>
                            <div>Stock : <span class="stock-produit">{$this->donnees["stock"]["valeur"]}</span></div>
                            <div>Prix : <span class="prix-produit">{$this->donnees["prix"]["valeur"]}</span> &euro;</div>
                        </div>

                        {$boutonHTML}
                    </footer>
                </article>
                HTML;
            }

            return $htmlListesProduits;
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
                return "image";
                break;
            case 3:
                return "marque";
                break;
            case 4:
                return "prix";
                break;
            case 5:
                return "coutFabrication";
                break;
            case 6:
                return "stock";
                break;
            case 7:
                return "nombreAchat";
                break;
            default:
                throw new Exception ("Le numero {$numeroPropriete} ne peut pas être converti en chaine représentant une propriété");
                return "";
                break;
            }
        }
    }
?>