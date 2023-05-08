import {DonneesCalendrier} from './donnees_calendrier.js';
import {EvenementMois} from './evenement_mois.js';

export class CreationCalendrier extends DonneesCalendrier
{
    constructor (date, elementSelectionner)
    {
        super(date);

        // Savoir si l'utilisateur à le droit de modifier des fonctionnalités des événements
        this.estAdmin = document.querySelector(".donnees-evenements").dataset.admin;

        this.conteneurHTML = document.querySelector(elementSelectionner);

        this.evenementsMois = new EvenementMois(date);

        this.initialiser_code_html();
    
        // Creer tout les éléments constituant le calendrier
        this.creer_calendrier_html();
    }

    initialiser_code_html ()
    {
        let affichageCalendrier = document.querySelector(".calendrier")

        // Vu qu'on veut recommencer tout le calendrier, il faut l'enlever si il existe déjà
        if (affichageCalendrier !== null)
        {
            affichageCalendrier.remove();
        }

        this.conteneurHTML.insertAdjacentHTML('beforeend', `<div class="calendrier"></div>`);

        // L'element du conteneur qui contiendra tout le calendrier
        this.elementHTML = document.querySelector(".calendrier");
    }


    creer_calendrier_html ()
    {
        let calendrierHTML = "";

        calendrierHTML += this.creer_annee();
        calendrierHTML += this.creer_mois();
        calendrierHTML += this.creer_semaine();
        calendrierHTML += this.creer_jours();

        this.elementHTML.insertAdjacentHTML('beforeend', calendrierHTML);
    }

    creer_annee ()
    {
        return `
        <nav class="navigation-calendrier container-fluid">
            <ul class="annee d-flex flex-row justify-content-between bg-info rounded">

                <li class="changer-annee p-2 px-3 bg-primary rounded-circle">
                    <i class="fas fa-angle-down" aria-hidden="true"></i>
                </li>

                <li class="nom-annee px-2">${this.date.getFullYear()}</li>

                <li class="changer-annee p-2 px-3 bg-primary rounded-circle">
                    <i class="fas fa-angle-up" aria-hidden="true"></i>
                </li>
            </ul>
        `;
    }

    creer_mois ()
    {
        // Permet de traduire le chiffre du mois en son nom
        let nomMois = this.date.toLocaleDateString("fr-FR", { month: "long" });
        nomMois = nomMois.charAt(0).toUpperCase() + nomMois.slice(1);

        return `
            <ul class="mois d-flex flex-row justify-content-between bg-info rounded">

                <li class="changer-mois p-2 px-3 bg-primary rounded-circle">
                    <i class="fas fa-angle-down" aria-hidden="true"></i>
                </li>

                <li class="nom-mois px-2" id="${this.date.getMonth()}">${nomMois}</li>

                <li class="changer-mois p-2 px-3 bg-primary rounded-circle">
                    <i class="fas fa-angle-up" aria-hidden="true"></i>
                </li>
            </ul>
        </nav>
        `;
    }

    creer_semaine ()
    {
        return `
        <ul class="semaine d-flex flex-row text-center mb-0 py-2 rounded-top">
            <li>Lundi</li>
            <li>Mardi</li>
            <li>Mercredi</li>
            <li>Jeudi</li>
            <li>Vendredi</li>
            <li>Samedi</li>
            <li>Dimanche</li>
        </ul>
        `;
    }


    // Partie la plus complexe de la création du calendrier
    // Contient beaucoup de fonctions imbriqué
    creer_jours ()
    {
        return `
        <ul class="jours w-100 d-flex flex-row flex-wrap text-dark mt-0">
        ${this.creer_jour_avant_mois()}
        ${this.creer_jour_mois()}
        ${this.creer_jour_fin_mois()}
        </ul>
        `;
    }


    // Jours avant le debut du mois et après le dernier lundi du mois précedent
    creer_jour_avant_mois ()
    {
        let codeHTML = ``;

        for (let jour = this.jourDebutCalendrier;
            jour <= this.jourFinMoisPrecedent;
            ++jour)
        {
            if (this.jourDebutCalendrier === 1)
            {
                break;
            }

            codeHTML += `<li class="jour-hors-mois">${jour}</li>`;
        }

        return codeHTML;
    }

    // Jours constiuant tout le mois
    creer_jour_mois ()
    {
        let codeHTML = ``;

        for (let jour = 1;
            jour <= this.jourFinMoisActuel;
            ++jour)
        {
            let tableauInfo = this.evenementsMois.verifier_jour(jour)
            if (tableauInfo.length > 0)
            {
                codeHTML += this.creer_jour_evenement(jour, tableauInfo[0], tableauInfo[1]);

                continue;
            }

            if (this.estAdmin)
            {
                codeHTML += `
                <li 
                    class="jour-mois" 
                    data-toggle="modal" 
                    data-target="#modalCreationEvenement"
                    id="${jour}">
    
                    ${jour}
                </li>
                `;
            }
            else
            {
                codeHTML += `
                <li class="jour-mois">
                    ${jour}
                </li>
                `;
            }
        }

        return codeHTML;
    }

    creer_jour_evenement (jour, id, nomEvenement)
    {
        if (this.estAdmin)
        {
            return `
            <li 
                class="jour-mois evenement"
                data-toggle="modal" 
                data-target="#modalModifierEvenement"
                id="${id}">  

                <div class="numero-jour-evenement pb-1">${jour}</div>
                <div class="nom-evenement border border-secondary bg-info rounded-lg">${nomEvenement}</div>
            </li>
            `;
        }
        else
        {
            return `
            <li class="bouton-afficher-evenement jour-mois evenement" 
                id="${id}"
                data-toggle="modal" 
                data-target="#modalAfficherEvenement">

                <div class="numero-jour-evenement pb-1">${jour}</div>
                <div class="nom-evenement border border-secondary bg-info rounded-lg">${nomEvenement}</div>
            </li>
            `;
        }
    }

    // Jours après le mois et avant le dernier dimanche du mois suivant
    creer_jour_fin_mois ()
    {
        let codeHTML = ``;

        for (let jour = 1;
            jour <= this.jourFinCalendrier;
            ++jour)
        {
            // Cas ou le dimanche est la fin du mois
            if (this.jourFinCalendrier >= 28)
            {
                break;
            }

            codeHTML += `<li class="jour-hors-mois">${jour}</li>`;
        }

        return codeHTML;

    }




    initialiser_calendrier (date)
    {
        super.initialiser_calendrier(date);

        this.evenementsMois = new EvenementMois(date);

        this.initialiser_code_html();
    
        // Creer tout les éléments constituant le calendrier
        this.creer_calendrier_html();
    }
}