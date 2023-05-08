import {AideCalendrier} from './annexe_evenement.js';

export class DonneesCalendrier
{
    constructor (date)
    {
        this.dateActuel = new Date();

        this.creer_donnees(date);
    }

    creer_donnees (date)
    {
        // La date à laquelle on va creer la calendrier
        this.date = new Date(date);

        // Pour nous permettre de correctement définir jourFinCalendrier
        // Sa valeur sera determiné à l'aide de debut_calendrier() et fin_calendrier()
        this.nombreJoursAfficher = AideCalendrier.jour_fin_mois(this.date);

        this.jourDebutCalendrier = this.debut_calendrier();
        // A faire après jourDebutCalendrier
        this.jourFinCalendrier = this.fin_calendrier();

        let finMoisPrecedent = new Date(this.date);
        finMoisPrecedent.setMonth(finMoisPrecedent.getMonth() - 1);
        this.jourFinMoisPrecedent = AideCalendrier.jour_fin_mois(finMoisPrecedent);
        this.jourFinMoisActuel = AideCalendrier.jour_fin_mois(this.date);

        if (this.nombreJoursAfficher !== 42)
        {
            throw "Le nombre de jours affichés n'est pas bon";
        }
    }

    // Retourne le jour de début du calendrier
    debut_calendrier ()
    {
        // On commence au debut du mois actuel
        let debutCalendrier = new Date(this.date);
        debutCalendrier.setDate(1);

        // Le debut du calendrier doit se faire un lundi (1 == lundi)
        while (AideCalendrier.recuperer_jour_semaine(debutCalendrier) !== 1)
        {
            ++this.nombreJoursAfficher;
            debutCalendrier.setDate(debutCalendrier.getDate() - 1);
        }

        return debutCalendrier.getDate();
    }

    // Retourne le jour de fin du calendrier
    fin_calendrier ()
    {
        // On commence à la fin du mois actuel
        let finCalendrier = (new Date(this.date));
        finCalendrier.setDate(AideCalendrier.jour_fin_mois(this.date));

        while (AideCalendrier.recuperer_jour_semaine(finCalendrier) !== 7 || this.nombreJoursAfficher < 42)
        {
            ++this.nombreJoursAfficher;
            finCalendrier.setDate(finCalendrier.getDate() + 1);
        }

        return finCalendrier.getDate();
    }

    initialiser_calendrier (date)
    {
        this.dateActuel = new Date();

        this.creer_donnees(date);
    }
}