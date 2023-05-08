function convertir_date_sql (chaineDateSQL)
{
    return chaineDateSQL.replace(' ', 'T');
}

export class RemplissageFormulaire
{
    constructor ()
    {
        this.baseDeDonnees = JSON.parse(document.querySelector('.donnees-completes').dataset.tableau);
    
        let remplir_formulaire = this.remplir_formulaire.bind(this);
        let boutonsAdmin = document.querySelectorAll(".bouton_admin");
        for (let bouton of boutonsAdmin)
        {
            bouton.addEventListener("click", remplir_formulaire)
        }
    }

    remplir_formulaire (evenement)
    {
        let inputs = document.querySelectorAll(`
            .form-group > .form-control, 
            .form-group > .form-control-file, 
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
            console.log(input);
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
            else if (input.type === "file")
            {
                console.log(this.baseDeDonnees[numeroBaseDeDonnee][numeroPropriete]);

                let imageHTML = `
                <figure class="image-produit">
                    <img class="w-50"
                        src="${this.baseDeDonnees[numeroBaseDeDonnee][numeroPropriete]}" 
                        alt="Image Produit">
                    <figcaption>Image Téléchargé</figcaption>
                    <div>Note : Si vous ne téléchargé pas de nouvelle images, cette image sera gardée</div>
                </figure>`;
                input.insertAdjacentHTML('afterend', imageHTML);
            }
            else
            {
                input.value = this.baseDeDonnees[numeroBaseDeDonnee][numeroPropriete];
            }


            ++numeroPropriete;
        }
    }
}