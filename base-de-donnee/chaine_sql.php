<?php
    class ChaineSQL
    {
        public static function ajouter_donnees(string $table, array $donnees): string
        {
            $donneesChaine = "";
            $valeursChaine = "";
            foreach ($donnees as $propriete => $tableauValeur)
            {
                if ($propriete === 'id')
                {
                    continue;
                }

                $donneesChaine .= "`${propriete}_{$table}`,";
                $valeursChaine .= '\'' . $GLOBALS[g_baseDeDonnee]->echapper_chaine($tableauValeur["valeur"]) . '\',';
            }

            // On enlève les dernieres virgule
            $donneesChaine = substr_replace($donneesChaine ,"", -1);
            $valeursChaine = substr_replace($valeursChaine ,"", -1);

            return <<<EOD
            INSERT INTO `{$table}` (${donneesChaine})
            VALUES (${valeursChaine});
            EOD;
        }

        public static function actualiser_donnees(string $table, array $donnees): string
        {
            $chaine = "";
            foreach ($donnees as $propriete => $tableauValeur)
            {
                $chaine .= "`${propriete}_{$table}` = ";
                $chaine .= '\'' . $GLOBALS[g_baseDeDonnee]->echapper_chaine($tableauValeur["valeur"]) . '\',';
            }

            // On enlève les dernieres virgule
            $chaine = substr_replace($chaine ,"", -1);

            $id = $donnees["id"]["valeur"];

            return <<<EOD
            UPDATE `{$table}`
            SET ${chaine}
            WHERE `id_{$table}`={$id};
            EOD;
        }
    }
?>