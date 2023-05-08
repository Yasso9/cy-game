<?php
    inclure_fichier("/formulaire/donnees_formulaire.php");

    abstract class Formulaire extends DonneesFormulaire
    {
        public function creer_formulaire_html () : string
        {
            $htmlCode = <<<HTML
            <div class="erreur">{$this->messageErreur}</div>
            <small class="form-text text-muted mb-2">Note : '*' indique que la case est requise</small>
            HTML;

            $proprietes = $this->fixer_propriete_formulaire();

            foreach ($proprietes as $propriete)
            {
                // Vérifications où on affiche pas la propriété
                if ($this->est_ignorer($propriete))
                {
                    continue;
                }

                $htmlCode .= $this->creer_debut_donnee($propriete);

                $htmlCode .= $this->creer_input_complet($propriete);

                $htmlCode .= $this->creer_fin_donnee($propriete);
            }

            if ($this->etat === Etat::ModificationPerso)
            {
                $htmlCode .= $this->creer_input_nouveau_mot_de_passe();
            }

            if ($this->captchaIntegre === true)
            {
                $htmlCode .= <<<HTML
                <div class="g-recaptcha" data-sitekey="6Lc0OeoaAAAAAG6GSbmJoek2RID09-FrfmJLrGYk" data-badge="inline" data-size="invisible" data-callback="setResponse"></div>
                <input type="hidden" id="captcha-response" name="captcha-response">
                HTML;
            }

            return $htmlCode;
        }

        private function fixer_propriete_formulaire () : array
        {
            if ($this->etat === Etat::Connexion)
            {
                return $this->donneesConnexion;
            }

            return array_keys($this->donnees);
        }

        private function creer_debut_donnee (string $propriete) : string
        {  
            $donneeRequise = $this->chaine_requise_afficher($propriete);

            $cacherInput = "";
            if ($this->etat_modification() &&
                $this->donnees[$propriete]["type"] === "password")
            {
                $cacherInput = "hidden";
            }

            return <<<HTML
            <div class="form-group" {$cacherInput}>
            <label for="label-{$propriete}">
                {$donneeRequise}{$this->donnees[$propriete]["nom"]}
            </label>
            HTML;
        }

        private function creer_fin_donnee (string $propriete) : string
        {
            $image = "";
            if ($this->donnees[$propriete]["type"] === "file" && 
                $this->etat_modification() === true &&
                empty($this->donnees[$propriete]["valeur"]) === false
            )
            {
                $image .= <<<HTML
                <figure>
                    <img class="image-{$this->nomTableau}"
                        src="{$this->donnees[$propriete]['valeur']}" 
                        alt="{$this->donnees[$propriete]['nom']} {$this->nomTableau}">
                    <figcaption>Image Téléchargé</figcaption>
                    <div>Note : Si vous ne téléchargé pas d'image, cette image sera gardée</div>
                </figure>
                HTML;
            }

            return <<<HTML
            <div class="valid-feedback">Cette donnée est remplie</div>
            <div class="invalid-feedback">{$this->donnees[$propriete]["erreur"]}</div>
            {$image}
            </div>
            HTML;
        }
        
        private function creer_input_complet(string $propriete) : string
        {
            if ($this->donnees[$propriete]["type"] === "select")
            {
                return $this->creer_input_select($propriete);
            }

            $htmlInput = "";
            $htmlInput .= $this->creer_input_de_base($propriete);

            // On creer un boutton caché pour aider la recuperation des données du formulaire 
            // lorsque l'utilisateur va devoir re-validé des donnés qui devrait être unique
            if ($this->donnees[$propriete]["unique"] === true &&
                $this->etat_modification() === true
            )
            {
                $htmlInput .= $this->creer_input_cachee($propriete);
            }

            if ($this->donnees[$propriete]["type"] === "password")
            {
                $htmlInput .= $this->creer_input_afficher_mot_de_passe();
            }


            return $htmlInput;
        }

        private function creer_input_de_base(string $propriete) : string
        {
            // required nous sert juste pour la mise en form css
            // $requis = $this->chaine_requise_html($propriete);
            $requis ="required";
            $modifiable = $this->chaine_modifiable_html($propriete);
            $autocompletion = $this->chaine_autocompletion_html($propriete);

            $classe = "";
            if ($this->donnees[$propriete]['type'] === "file")
            {
                $classe = "form-control-file";
            }
            else
            {
                $classe = "form-control form-control-sm";
            }

            return <<<HTML
            <input 
                class="{$classe} bouton-noirci"
                id="label-{$propriete}" 
                type="{$this->donnees[$propriete]['type']}" 
                name="{$propriete}" 
                value="{$this->donnees[$propriete]['valeur']}"
                placeholder="{$this->donnees[$propriete]['placeholder']}"
                {$requis}
                {$modifiable}
                {$autocompletion}>
            HTML;
        }

        private function creer_input_afficher_mot_de_passe () : string
        {
            return <<<HTML
            <div class="custom-control custom-checkbox">
                <input 
                class="custom-control-input"
                type="checkbox" 
                onclick="afficher_cacher_mot_de_passe()"
                id="label-check-motDePasse">
                
                <label class="custom-control-label" for="label-check-motDePasse">
                        Afficher mot de passe
                </label>

                <script async defer src="/script-js/mot_de_passe.js"></script>
            </div>
            HTML;
        }

        private function creer_input_cachee (string $propriete) : string
        {
            return <<<HTML
            <input 
                type="hidden" 
                name="{$propriete}_precedente" 
                value="{$this->donnees[$propriete]["valeur"]}">
            HTML;
        }

        private function creer_input_select (string $propriete) : string
        {
            $selection = new Selection($propriete);
            $requis = $this->chaine_requise_html($propriete);

            $htmlInputSelect = <<<HTML
            <select class="form-control form-control-sm bouton-noirci" name="{$propriete}" id="label-{$propriete}" {$requis}>
            <option value="">--Merci de choisir une option--</option>
            HTML;

            foreach ($selection->liste() as $valeur => $nom)
            {
                $selectionner = "";
                if ($this->donnees[$propriete]["valeur"] === $valeur)
                {
                    $selectionner = "selected";
                }

                $htmlInputSelect .= <<<HTML
                <option value="{$valeur}" {$selectionner}>{$nom}</option>
                HTML;
            }

            $htmlInputSelect .= <<<HTML
            </select>
            HTML;

            return $htmlInputSelect;
        }

        public function creer_input_nouveau_mot_de_passe () : string
        {
            return <<<HTML
            <div class="form-group">

            <label for="label-nouveauMotDePasse">
                Nouveau Mot de Passe
            </label>

            <input 
                class="form-control form-control-sm bouton-noirci" 
                id="label-nouveauMotDePasse" 
                type="password" 
                name="nouveauMotDePasse" 
                value="">

            <div class="invalid-feedback">{$this->donnees["motDePasse"]["erreur"]}</div>
            <div class="info-donnee">Si la case reste vide, vous ne changerez pas de mot de passe.</div>

            </div>

            <div>
                <input 
                    type="checkbox" 
                    onclick="afficher_cacher_mot_de_passe()"
                    id="label-check-nouveauMotDePasse">

                <label for="label-check-nouveauMotDePasse">
                    Afficher mot de passe
                </label>

                <script async defer src="/formulaire/mot_de_passe.js"></script>
            </div>
            HTML;
        }


        private function est_ignorer (string $propriete) : bool
        {  
            return
            (
                ($this->donnees[$propriete]["type"] === "tableau") ||
                $this->donnees[$propriete]["secret"] === true &&
                (
                    (
                        $this->etat === Etat::ModificationPerso &&
                        $propriete !== "id"
                    ) ||
                    $this->etat === Etat::Creation
                )
            );
        }


        private function chaine_autocompletion_html (string $propriete) : string
        {  
            if ($propriete === "motDePasse")
            {
                if ($this->etat === Etat::Connexion)
                {
                    return "autocomplete=\"current-password\"";
                }
                else if ($this->etat === Etat::Creation ||
                    $this->etat === Etat::ModificationPerso
                )
                {
                    return "autocomplete=\"new-password\"";
                }
            }
            else if ($propriete === "pseudo")
            {
                return "autocomplete=\"username\"";
            }

            return "";
        }

        private function chaine_requise_afficher (string $propriete) : string
        {  
            if ($this->donnees[$propriete]["requis"] === true)
            {
                return "<span class='text-danger'>* </span>";
            }
            
            return "";
        }

        private function chaine_requise_html (string $propriete) : string
        {  
            if ($this->donnees[$propriete]["requis"] === true)
            {
                return "required";
            }
            
            return "";
        }

        private function chaine_modifiable_html (string $propriete) : string
        {  
            if ($this->donnees[$propriete]["modifiable"] === false &&
                $this->etat_modification()
            )
            {
                return "readonly";
            }
            
            return "";
        }
    }
?>