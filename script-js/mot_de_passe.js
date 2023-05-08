"use_strict"

function afficher_cacher_mot_de_passe() 
{
    let motDePasse = document.getElementById("label-nouveauMotDePasse") ?? document.getElementById("label-motDePasse");

    if (motDePasse.type === "password") {
        motDePasse.type = "text";
    } 
    else {
        motDePasse.type = "password";
    }
}