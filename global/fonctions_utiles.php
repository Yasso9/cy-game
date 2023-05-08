<?php

// Fichier qui contient des fonctions utile qui peuvent être utilisé dans tout le programme 

function redirection (string $cheminFichier) : void
{
    header("Location: {$cheminFichier}");
    exit;
}

function redirection_page_principale () : void
{
    redirection(g_pagePrincipale);
}

function recharger_page () : void
{
    header("Refresh: 0");
}

function chemin_fichier_existe (string $cheminFichier) : bool
{
    return file_exists($_SERVER['DOCUMENT_ROOT'] . $cheminFichier);
}

// Test si le fichier existe avant de le supprimer
function supprimer_fichier (string $cheminFichier) : bool
{
    // On ne supprime pas ce fichier là
    if ($cheminFichier === "/styles/images/image-produit-non-disponible.svg")
    {
        return false;
    }

    $cheminFinal = $_SERVER['DOCUMENT_ROOT'] . $cheminFichier;
    if (!file_exists($cheminFinal))
    {
        return false;
    }
    
    unlink($cheminFinal);
    return true;
}

function page_actuel () : string
{
    return htmlspecialchars($_SERVER['PHP_SELF']);;
}

function combinaison_tableau (array $tableauCles, array $tableauValeurs) : array
{
    $tableauFinal = array();

    foreach(array_combine($tableauCles, $tableauValeurs) as $cle => $valeur) 
    {
        $tableauFinal[] = array($cle, $valeur);
    }

    return $tableauFinal;
}

function simplification_donnee (string $donnee) : string
{
    // Supprime les espaces en début et fin de chaîne
    $donnee = trim($donnee);
    // Supprime tout les antislash qui pourrait être néfaste
    $donnee = stripslashes($donnee);
    // Convertie tout les caractère spéciaux en caractère HTML
    $donnee = htmlspecialchars($donnee);

    return $donnee;
}

function supprimer_cookie (string $nomCookie) : void
{
    unset($_COOKIE[$nomCookie]); 
    setcookie($nomCookie, null, -1, '/');
}