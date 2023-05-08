<?php
    function recuperer_id_bouton(string $chaineBoutton) : string
    {
        // La valeur d'un boutton sera de la forme "id - pseudo"
        // On ne voudra récupérer que l'ID

        $positionFinID = strpos($chaineBoutton, ' ');
        // Si il n'y a pas d'espace notre algorithme ne marche pas
        if (!$positionFinID)
        {
            $messageErreur = "Impossible d'identifier l'ID du boutton clique";
            echo $messageErreur;
        }

        // On enlève tout ce qu'il y'a après l'id
        return substr_replace($chaineBoutton, '', $positionFinID);
    }
?>