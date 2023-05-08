import {AideCalendrier} from './annexe_evenement.js';

export class EvenementMois
{
    constructor (date)
    {
        // On récupère les événements dans une division ou PHP a envoyé ses infos
        let chaineEvenements = document.querySelector(".donnees-evenements").dataset.evenements;
        this.baseDeDonnees = JSON.parse(chaineEvenements);

        // On change change les valeurs de date pour pouvoir plus facilement les utilisés
        for (let elementTableau of this.baseDeDonnees)
        {
            elementTableau["dateDebut"]["valeur"] = new Date( AideCalendrier.convertir_date_sql(elementTableau["dateDebut"]["valeur"]) );
            elementTableau["dateFin"]["valeur"] = new Date( AideCalendrier.convertir_date_sql(elementTableau["dateFin"]["valeur"]) );  
        }

        // console.log(JSON.stringify(this.baseDeDonnees, null, 2));

        this.initialiser_evenements(date);
    }

    initialiser_evenements (date)
    {
        // Tableau qui retient quelles éléments de la base de donnée font partie du même mois
        this.evenementsMois = new Array();

        for (let numeroTableau = 0; numeroTableau < this.baseDeDonnees.length; ++numeroTableau)
        {
            if (AideCalendrier.meme_mois(date, this.baseDeDonnees[numeroTableau]["dateDebut"]["valeur"]) === true ||
                AideCalendrier.meme_mois(date, this.baseDeDonnees[numeroTableau]["dateFin"]["valeur"]))
            {
                // C'est un tableau associatif qui contient des tableaux associatifs
                this.evenementsMois.push(numeroTableau);
            }
        }
    }

    // Verifie si il y a un evenement à la date donnée en paramètre
    verifier_jour (numeroJour)
    {
        // Si il y'a des événements dans le mois en cours
        if (this.evenementsMois.length > 0)
        {
            for (let numeroTableau of this.evenementsMois)
            {
                if (numeroJour >= this.baseDeDonnees[numeroTableau]["dateDebut"]["valeur"].getDate() &&
                    numeroJour <= this.baseDeDonnees[numeroTableau]["dateFin"]["valeur"].getDate())
                {
                    return new Array(
                        this.baseDeDonnees[numeroTableau]["id"]["valeur"],
                        this.baseDeDonnees[numeroTableau]["nom"]["valeur"]
                    );
                }
            }
        }

        return new Array ();
    }

    rechercher_id (id)
    {
        for (let numeroTableau of this.evenementsMois)
        {
            if (id === this.baseDeDonnees[numeroTableau]["id"]["valeur"])
            {
                return numeroTableau;
            }
        }
    }

    afficher (evenementHTML)
    {
        numeroTableau = this.rechercher_id(evenementHTML.currentTarget.parentNode.id);

        let listeNomEvenement = ``;
        let listeValeurEvenement = ``;
        for(let donneeEvenement of this.baseDeDonnees[numeroTableau])
        {
            listeNomEvenement += `<th>${donneeEvenement[nom]}</th>`;
            listeValeurEvenement += `<td>${donneeEvenement[valeur]}</td>`;
        }

        let htmlTableauEvenement = `
        <table>
            <caption>Évenement</caption>

            <thead>
                <tr>
                    ${listeNomEvenement}
                </tr>
            </thead>

            <tbody>
                <tr>
                    ${listeValeurEvenement}
                </tr>
            </tbody>
        </table>
        `;

        document.body.insertAdjacentHTML('beforeend', htmlTableauEvenement);
    }

    afficher_evenement (evenement)
    {
        let idEvenement = evenement.currentTarget.id;

        let evenementSelectionner;
        for (let evenement of this.baseDeDonnees)
        {
            if (evenement["id"]["valeur"] === idEvenement)
            {

                evenementSelectionner = evenement;
                break;
            }
        }


        if (!evenementSelectionner)
        {
            throw ("L'évènement voulu n'existe plus");
        }

        let optionDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        let dateDebut = evenementSelectionner['dateDebut']['valeur'].toLocaleDateString('fr-FR', optionDate);
        let dateFin = evenementSelectionner['dateFin']['valeur'].toLocaleDateString('fr-FR', optionDate);


        let evenementModalHTML = `
        <header class="modal-header">
            <h4 class="modal-title">${evenementSelectionner['nom']['valeur']}</h4>

            <button type="button" class="fermeture-modal" data-dismiss="modal">
                <i class="fa fa-window-close" aria-hidden="true"></i>
            </button>
        </header>

        <main class="modal-body d-flex flex-column justify-content-center">
            <div>
                Du ${dateDebut} au ${dateFin}
            </div>

            <div>Lieu : ${evenementSelectionner['lieu']['valeur']}</div>

            <p>Description : ${evenementSelectionner['description']['valeur']}</p>

            <div>
                ${evenementSelectionner['prixParPersonne']['nom']} : 
                <span class="prix-produit">${evenementSelectionner['prixParPersonne']['valeur']}</span> 
                &euro;
            </div>
            <div>
                ${evenementSelectionner['nombreParticipants']['nom']} actuel : 
                <span class="prix-produit">${evenementSelectionner['nombreParticipants']['valeur']}</span>
            </div>
        </main>

        <footer class="modal-footer">
            <form method="post" action="/pages/evenement/requete_evenement.php">
                <button 
                    class="btn btn-success"
                    type="submit" 
                    name="participer_evenement"
                    value="${evenementSelectionner['id']['valeur']}">
                Participer !
                </button>
            </form>
        </footer>
        `;

        let endroitOuAfficher = document.querySelector('#modalAfficherEvenement .modal-content');
        endroitOuAfficher.innerHTML = "";
        endroitOuAfficher.insertAdjacentHTML('beforeend', evenementModalHTML);
    }
}