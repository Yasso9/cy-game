import {AideCalendrier} from './annexe_evenement.js';
import {CreationCalendrier} from './creation_calendrier.js';
import {initialiser_formulaire} from './script_formulaire_evenement.js';
import {Cookie} from '../../script-js/cookie.js';
import {RemplissageFormulaireEvenement} from './remplissage_formulaire.js';

export class CalendrierFinal extends CreationCalendrier
{
    constructor (date, elementSelectionner)
    {
        super(date, elementSelectionner);

        this.creer_bouton();

        // Couleur dégradé
        this.rouge = 130,
        this.vert = 180,
        this.bleu = 210;

        this.changementRouge = -5,
        this.changementVert = 5,
        this.changementBleu = 5;

        this.coloriser_jours();

        if (AideCalendrier.meme_mois(this.date, this.dateActuel))
        {
            let jourActuel = document.getElementById(this.dateActuel.getDate())
            jourActuel.classList.add(".jour-actuel");
        }

        let formulaire = new RemplissageFormulaireEvenement();
        formulaire.initialisation_boutons();
    }

    creer_bouton ()
    {
        let bouttonChangementAnnee = document.querySelectorAll(".changer-annee");

        // On lie les fonctions à leurs classes pour que le this à l'intérieur 
        // de ces fonctions soit correctement défini
        let annee_precedente = this.annee_precedente.bind(this);
        let annee_suivante = this.annee_suivante.bind(this);
        bouttonChangementAnnee[0].addEventListener("click", annee_precedente);
        bouttonChangementAnnee[1].addEventListener("click", annee_suivante);


        let bouttonChangementMois = document.querySelectorAll(".changer-mois");

        // On lie les fonctions à leurs classes pour que le this à l'intérieur 
        // de ces fonctions soit correctement défini
        let mois_precedent = this.mois_precedent.bind(this);
        let mois_suivant = this.mois_suivant.bind(this);
        bouttonChangementMois[0].addEventListener("click", mois_precedent);
        bouttonChangementMois[1].addEventListener("click", mois_suivant);


        let mise_en_avant_jour = this.mise_en_avant_jour.bind(this);
        let retour_normal_jour = this.retour_normal_jour.bind(this);

        let listeDesJours = document.querySelectorAll(".jour-mois");
        for (let jour of listeDesJours)
        {
            jour.addEventListener("click", initialiser_formulaire);
            jour.addEventListener("mouseover", mise_en_avant_jour);
            jour.addEventListener("mouseout", retour_normal_jour);
        }

        let afficher_evenement = this.evenementsMois.afficher_evenement.bind(this.evenementsMois);
        let boutonsEvenementsAfficher = document.querySelectorAll(".bouton-afficher-evenement");
        for (let jour of boutonsEvenementsAfficher)
        {
            jour.addEventListener("click", afficher_evenement);
        }

        // let preparer_modification_evenement = this.preparer_modification_evenement.bind(this);
        // let boutonsEvenementsModifier = document.querySelectorAll(".bouton-modifier-evenement");
        // for (let jour of boutonsEvenementsModifier)
        // {
        //     jour.addEventListener("click", preparer_modification_evenement);
        // }
    }

    // preparer_modification_evenement (evenement)
    // {
    //     Cookie.setCookie("modification_evenement", `${evenement.currentTarget.id}`);
    // }

    mise_en_avant_jour (evenement)
    {
        evenement.currentTarget.classList.add("shadow-lg");
        evenement.currentTarget.style.zIndex = 1;
    }

    retour_normal_jour (evenement)
    {
        evenement.currentTarget.classList.remove("shadow-lg");
        evenement.currentTarget.style.zIndex = "auto";
    }


    coloriser_jours ()
    {
        let joursMois = document.querySelectorAll(".jour-mois");
        for (let jour of joursMois)
        {
            if (this.rouge <= 100 || this.rouge >= 220)
            {
                this.changementRouge = -this.changementRouge;
            }

            if (this.vert <= 100 || this.vert >= 220)
            {
                this.changementVert = -this.changementVert;
            }

            if (this.bleu <= 100 || this.bleu >= 220)
            {
                this.changementBleu = -this.changementBleu;
            }

            this.rouge += this.changementRouge;
            this.vert += this.changementVert;
            this.bleu += this.changementBleu;

            // background-image: linear-gradient( to top, #ABDCFF 5%, #0396FF 95%);
            jour.style.backgroundImage = `linear-gradient(
                260deg, 
                rgb(${this.rouge - 30}, ${this.vert - 30}, ${this.bleu - 30}) 20%, 
                rgb(${this.rouge}, ${this.vert}, ${this.bleu}) 80%)`;
            jour.style.transition = `filter 1s linear`;
            jour.classList.add('transition');
        }
    }

    mois_suivant ()
    {
        let nouvelleDate = new Date(this.date);
        nouvelleDate.setMonth(nouvelleDate.getMonth() + 1);

        this.initialiser_calendrier(nouvelleDate);
    }

    mois_precedent ()
    {
        let nouvelleDate = new Date(this.date);
        nouvelleDate.setMonth(nouvelleDate.getMonth() - 1);

        this.initialiser_calendrier(nouvelleDate);

    }


    annee_suivante ()
    {
        let nouvelleDate = new Date(this.date);
        nouvelleDate.setFullYear(nouvelleDate.getFullYear() + 1);

        this.initialiser_calendrier(nouvelleDate);
    }

    annee_precedente ()
    {
        let nouvelleDate = new Date(this.date);
        nouvelleDate.setFullYear(nouvelleDate.getFullYear() - 1);

        this.initialiser_calendrier(nouvelleDate);
    }


    initialiser_calendrier (date)
    {
        super.initialiser_calendrier(date);

        this.creer_bouton();

        this.coloriser_jours();

        if (AideCalendrier.meme_mois(this.date, this.dateActuel))
        {
            let jourActuel = document.getElementById(this.dateActuel.getDate())
            jourActuel.classList.add(".jour-actuel");
        }

        let formulaire = new RemplissageFormulaireEvenement();
        formulaire.initialisation_boutons();
    }
}