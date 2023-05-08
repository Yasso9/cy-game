export class AideCalendrier
{
    static recuperer_jour_semaine (date)
    {
        let jourSemaine = date.getDay();
    
        if (jourSemaine === 0)
        {
            return 7;
        }
    
        return jourSemaine;
    }
    
    // Converti une chaine en une date qui provient d'une base de donnée SQL
    static convertir_date_sql (chaineDateSQL)
    {
        return chaineDateSQL.replace(' ', 'T');
    }
    
    // Verifie si deux date sont du meme mois de la même années
    static meme_mois (dateA, dateB)
    {
        return (
            dateA.getMonth() === dateB.getMonth() &&
            dateA.getFullYear() === dateB.getFullYear()
        );
    }

    // Calcul la date à laquelle le mois se termine
    static jour_fin_mois (date)
    {
        let finMois = new Date(date);

        // Les fins de mois commence à partir de 28 (pour fevrier)
        let jour = 27;
        while (finMois.getMonth() === date.getMonth())
        {
            finMois.setDate(++jour);
        }

        // A cause de la boucle finMois est allé
        // au premier jour du mois suivant
        finMois.setMonth(date.getMonth());
        // On retourne aussi au jour précedent
        finMois.setDate(--jour);
        // De même pour annee (si on est en decembre)
        finMois.setFullYear(date.getFullYear());

        return finMois.getDate();
    }
}