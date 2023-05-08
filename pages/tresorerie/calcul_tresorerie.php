<?php
	final class EnumEvenement
	{
		public const Cout = 0;
		public const PrixParPersonne = 1;
		public const NombreParticipants = 2;
		public const Nom = 3;
	}

	class CalculTresorerie
	{
        protected const Categorie = array(
            "Utilisateur" => "utilisateur",
            "Évènement" => "evenement", 
            "Boutique" => "boutique"
        );
        protected const Comptabilites = array(
            "Recettes" => "recettes", 
            "Dépenses" => "depenses", 
            "Bénéfices" => "benefices"
        );

		protected array $tableauTresorerie = array();
        protected array $totaux = array();

		public function __construct()
		{
			$this->calcul_utilisateur();
			$this->calcul_evenement();
			$this->calcul_boutique();

            $this->calcul_benefices();

            $this->calcul_totaux();
		}

		private function calcul_utilisateur () : void
		{
			$requeteSQL = <<<EOD
			SELECT `pseudo_utilisateur` FROM `utilisateur`;
			EOD;

			$nombreUtilisateur = sizeof($GLOBALS[g_baseDeDonnee]->requete($requeteSQL));

			$prixCotisation = g_prixCotisation;

			$chaineRecettesUtilisateur = <<<HTML
			<ul>
				<li>
					Nombre d'utilisateur : {$nombreUtilisateur}
				</li>
				<li>
				Prix des cotisation : {$prixCotisation} &euro;
				</li>
			</ul>
			HTML;

			$this->tableauTresorerie["recettes"]["utilisateur"] = array(
				((int)$nombreUtilisateur * (int)g_prixCotisation),
				$chaineRecettesUtilisateur
			);

			$this->tableauTresorerie["depenses"]["utilisateur"] = array(
				0, "<p>Les utilisateurs ne nous coûtent rien</p>"
			);
		}

		private function calcul_evenement () : void
		{
			$requeteSQL = <<<EOD
			SELECT 
				`cout_evenement`, 
				`prixParPersonne_evenement`, 
				`nombreParticipants_evenement`,
				`nom_evenement` 
			FROM `evenement`;
			EOD;

			$tableauDesEvenements = $GLOBALS[g_baseDeDonnee]->requete($requeteSQL);

			$revenusEvenement = 0;
			$depenseEvenement = 0;
			$chaineInfoRecettes = "";
			$chaineInfoDépenses = "";
			foreach($tableauDesEvenements as $evenement)
			{
				$revenusEvenement += $evenement[EnumEvenement::PrixParPersonne] * $evenement[EnumEvenement::NombreParticipants];
				$depenseEvenement += $evenement[EnumEvenement::Cout];

				$chaineInfoRecettes .= <<<HTML
				<ul>
					<li>
						<h4>{$evenement[EnumEvenement::Nom]}</h4>
					</li>
					<li>
						Nombre de participants : {$evenement[EnumEvenement::NombreParticipants]}
					</li>
					<li>
						Coût de l'évenement par personne : {$evenement[EnumEvenement::PrixParPersonne]} &euro;
					</li>
				</ul>
				HTML;

				$chaineInfoDépenses .= <<<HTML
				<ul>
					<li>
						<h4>{$evenement[EnumEvenement::Nom]}</h4>
					</li>
					<li>
						Coût de l'évenement pour CY GAME : {$evenement[EnumEvenement::Cout]} &euro;
					</li>
				</ul>
				HTML;
			}

			$this->tableauTresorerie["recettes"]["evenement"] = array(
				$revenusEvenement, $chaineInfoRecettes
			);

			$this->tableauTresorerie["depenses"]["evenement"] = array(
				$depenseEvenement, $chaineInfoDépenses
			);
		}

		private function calcul_boutique () : void
		{
			$requeteSQL = <<<EOD
			SELECT 
				`prix_produit`, 
				`coutFabrication_produit`, 
				`stock_produit`, 
				`nombreAchat_produit`,
				`nom_produit`
			FROM `produit`;
			EOD;

			$tableauDesProduits = $GLOBALS[g_baseDeDonnee]->requete($requeteSQL);

			$revenusProduit = 0;
			$depenseProduit = 0;
			$chaineInfoRecettes = "";
			$chaineInfoDépenses = "";
			foreach($tableauDesProduits as $produit)
			{
				$revenusProduit += (int)$produit[0] * (int)$produit[3];
				$depenseProduit += (int)$produit[1] * ((int)$produit[2] + (int)$produit[3]);

				$chaineInfoRecettes .= <<<HTML
				<ul>
					<li>
						<h4>{$produit[4]}</h4>
					</li>
					<li>
						Nombre d'achat : {$produit[3]} &euro;
					</li>
					<li>
						Prix du produit : {$produit[0]} &euro;
					</li>
				</ul>
				HTML;

				$nombresProduitsAchetes = (int)$produit[2] + (int)$produit[3];
				$chaineInfoDépenses .= <<<HTML
				<ul>
					<li>
						<h4>{$produit[4]}</h4>
					</li>
					<li>
						Coût de fabrication du produit : {$produit[1]} &euro;
					</li>
					<li>
						Nombre de produits acheté en tout : {$nombresProduitsAchetes}
					</li>
				</ul>
				HTML;
			}

			$this->tableauTresorerie["recettes"]["boutique"] = array(
				$revenusProduit, $chaineInfoRecettes
			);

			$this->tableauTresorerie["depenses"]["boutique"] = array(
				$depenseProduit, $chaineInfoDépenses
			);
		}

        private function calcul_benefices () : void
		{
			foreach($this::Categorie as $categorie)
            {
                $this->tableauTresorerie["benefices"][$categorie] = $this->tableauTresorerie["recettes"][$categorie][0] - $this->tableauTresorerie["depenses"][$categorie][0];
            }
		}

        private function calcul_totaux () : void
		{
			foreach($this::Comptabilites as $comptabilite)
            {
                $this->totaux[$comptabilite] = 0;

                if ($comptabilite === "benefices")
                {
                    foreach($this::Categorie as $categorie)
                    {
                        $this->totaux[$comptabilite] += $this->tableauTresorerie[$comptabilite][$categorie];
                    }

                    continue;
                }
                
                foreach($this::Categorie as $categorie)
                {
                    $this->totaux[$comptabilite] += $this->tableauTresorerie[$comptabilite][$categorie][0];
                }
            }
		}
	}
?>