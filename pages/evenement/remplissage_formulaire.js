function convertir_date_sql (chaineDateSQL)
{
    return chaineDateSQL.replace(' ', 'T');
}

export class RemplissageFormulaireEvenement
{
    constructor ()
    {
        this.baseDeDonnees = JSON.parse(document.querySelector('.donnees-evenements').dataset.tableau);
        this.formulaire = document.querySelector("#modalModifierEvenement form");
    }

    initialisation_boutons ()
    {
        let remplir_formulaire = this.remplir_formulaire.bind(this);
        let boutonsActivation = document.querySelectorAll(".jours > .evenement");
        for (let bouton of boutonsActivation)
        {
            bouton.addEventListener("click", remplir_formulaire)
        }
    }

    remplir_formulaire (evenement)
    {
        let inputs = this.formulaire .querySelectorAll(`
            .form-group > .form-control, 
            .form-group > input[type="hidden"]
        `);

        let numeroBaseDeDonnee = 0;
        for (let donnee of this.baseDeDonnees)
        {
            if (String(evenement.currentTarget.id) === String(donnee[0]))
            {
                break;
            }
            ++numeroBaseDeDonnee;
        }

        let numeroPropriete = 0;
        for (let input of inputs)
        {
            if (input.type === "hidden")
            {
                input.value = this.baseDeDonnees[numeroBaseDeDonnee][numeroPropriete-1];
                continue;
            }
            else if (input.hidden === true)
            {
                continue;
            }
            else if (input.type === "datetime-local")
            {
                input.value = convertir_date_sql(this.baseDeDonnees[numeroBaseDeDonnee][numeroPropriete]);
            }
            else
            {
                input.value = this.baseDeDonnees[numeroBaseDeDonnee][numeroPropriete];
            }

            ++numeroPropriete;
        }
    }
}