function convertir_date_en_html (date)
{
    return new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString().slice(0, -1);
}

// Marque dans le formulaire les valeurs du jour
export function initialiser_formulaire (evenement)
{
    // On défini la date
    let numeroJour = evenement.currentTarget.id;
    let numeroMois = document.querySelector(".nom-mois").id;
    let numeroAnnee = document.querySelector(".nom-annee").textContent;

    let dateDebut = new Date(numeroAnnee, numeroMois, numeroJour, 8);
    let dateFin = new Date(numeroAnnee, numeroMois, numeroJour, 18);

    let saisiesFormulaire = document.querySelectorAll(".creation-evenement input");
    for (let saisie of saisiesFormulaire)
    {
        saisie.value = "";
        if (saisie.matches('input[name$="dateDebut"]')) 
        {
            // On doit enlever un Z qui se met à la fin
            saisie.value = convertir_date_en_html(dateDebut);
        }
        else if (saisie.matches('input[name$="dateFin"]')) 
        {
            saisie.value = convertir_date_en_html(dateFin);
        }
    }
}