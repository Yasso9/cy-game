<?php
    final class InclusionFichier
    {
        // A mettre au début de la page
        public static function debut (string $titre, bool $afficherCaptcha = false, string $fichierCSS = "") : void
        {
            // Tableau de chaines
            $css  = array(
                "/styles/css/global.css", 
                "/styles/css/haut_de_page.css",
                "/styles/css/pied_de_page.css"
            );

            if (!empty($fichierCSS))
            {
                $css[] = "/styles/css/{$fichierCSS}";
            }

            inclure_fichier(
                "/annexes-page/debut_html.php", 
                array(
                    "titre" => $titre, 
                    "captcha" => $afficherCaptcha,
                    "css" => $css
                )
            );
    
            inclure_fichier("/annexes-page/haut_de_page.php");
        }
    
        // A mettre à la fin de la page
        public static function fin() : void
        {
            inclure_fichier("/annexes-page/fin_html.php");
            inclure_fichier("/annexes-page/pied_de_page.php");
        }
    }
?>