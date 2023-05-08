<?php
    inclure_fichier("/pages/tresorerie/calcul_tresorerie.php");

    final class TableauTresorerie extends CalculTresorerie
    {
        public function creer_tableau () : string
        {
            $htmlCode = "";

            $htmlCode .= $this->creer_tableau_head();

            $htmlCode .= $this->creer_tableau_body();

            $htmlCode .= $this->creer_tableau_foot();

            return $htmlCode;
        } 

        private function creer_tableau_head () : string
        {
            $htmlCode = <<<HTML

            <h2 class="display-4 text-center mt-5">Récapitulatif des recettes, dépenses et bénéfices</h2>

            <thead>
                <tr>
                    <th>Catégorie</th>
            HTML;

            foreach(array_keys($this::Comptabilites) as $nom)
            {
                $htmlCode .= "<th>{$nom}</th>";
            }

            $htmlCode .= <<<HTML
                </tr>
            </thead>
            <tbody>
            HTML;

            return $htmlCode;
        }

        private function creer_tableau_body () : string
        {
            $htmlCode = "";

            foreach($this::Categorie as $nom => $categorie)
            {
                $classBenefice = "";
                if ($this->tableauTresorerie["benefices"][$categorie] < 0)
                {
                    $classBenefice = "table-danger";
                }
                else
                {
                    $classBenefice = "table-success";
                }

                $htmlCode .= <<<HTML
                    
                    <tr class="bouton-tresorerie" data-toggle="collapse" data-target="#{$categorie}">
                        <th>$nom</th>

                        <td class="table-success">{$this->tableauTresorerie["recettes"][$categorie][0]} &euro;</td>
                        <td class="table-danger">{$this->tableauTresorerie["depenses"][$categorie][0]} &euro;</td>
                        <td class="{$classBenefice}">{$this->tableauTresorerie["benefices"][$categorie]} &euro;</td>
                    </tr>
                   
                    
                    <tr id="{$categorie}" class="collapse">
                        <th></th>
                        <td>{$this->tableauTresorerie["recettes"][$categorie][1]}</td>
                        <td>{$this->tableauTresorerie["depenses"][$categorie][1]}</td>
                        <td></td>
                    </tr>
                HTML;
            }

            return $htmlCode;
        }

        private function creer_tableau_foot () : string
        {
            $classeTotal = "";
            if ($this->totaux["benefices"] < 0)
            {
                $classeTotal = "table-danger";
            }
            else
            {
                $classeTotal = "table-success";
            }

            return <<<HTML
            </tbody>

            <tfoot>
                <tr>
                    <th>Totaux</th>

                    <td class="table-success">{$this->totaux["recettes"]} &euro;</td>
                    <td class="table-danger">{$this->totaux["depenses"]} &euro;</td>
                    <td class="{$classeTotal}">{$this->totaux["benefices"]} &euro;</td>
                </tr>
            </tfoot>
            HTML;
        }
    }

?>