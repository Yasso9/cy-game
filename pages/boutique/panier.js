import {Cookie} from '../../script-js/cookie.js';

function est_entier(valeur) 
{
    return (!isNaN(valeur) && 
        parseInt(Number(valeur)) == valeur && 
        !isNaN(parseInt(valeur, 10))
    );
}

class Panier
{
    constructor ()
    {
        // Conteneur du panier qu'il faudra caché ou affiché
        this.conteneurPanier = document.querySelector(".conteneur-panier");
        // Endroit où on va afficher le panier
        this.panierHTML = this.conteneurPanier.querySelector(".panier");
        // Bouton submit du formulaire
        // this.boutonCommander = this.conteneurPanier.querySelector(".bouton-commander-produits");
        // Prix tu total des articles commandés
        this.totalPanier = this.conteneurPanier.querySelector(".total-panier");
        this.totalPanierAffichage = this.conteneurPanier.querySelector(".total-panier-affichage");

        this.initialiser_panier();
        this.initialiser_boutons();

        this.initialiser_panier_cookie();
    }

    initialiser_panier ()
    {
        // Au debut il ne doit rien avoir dans le panier
        $("#formulairePanier").collapse('hide');
        this.panierHTML.innerHTML = "";
        this.totalPanier.value = "0";
        this.totalPanierAffichage.innerHTML = "0";

        // Les ID des produits acheter
        this.nombreProduits = new Array();

    }

    // Active tout les boutons de la boutique liés au panier
    initialiser_boutons ()
    { 
        // Ajouter un article au panier
        let ajouter_panier = this.ajouter_panier.bind(this);
        // Effacer le panier revient à le réinitialiser
        let effacer_panier = this.initialiser_panier.bind(this);
        let alerte_non_membre = this.alerte_non_membre.bind(this);
        
        let boutonAjouterPanier = document.querySelectorAll(".bouton-ajouter-panier");
        for (let bouton of boutonAjouterPanier)
        {
            bouton.addEventListener("click", ajouter_panier);
        }
        let boutonAjouterPanierNon = document.querySelectorAll(".bouton-ajouter-panier-non");
        for (let bouton of boutonAjouterPanierNon)
        {
            bouton.addEventListener("click", alerte_non_membre);
        }


        let boutonEffacerPanier = document.querySelector(".bouton-effacer-panier");
        boutonEffacerPanier.addEventListener("click", effacer_panier);
    }

    initialiser_panier_cookie ()
    {
        let panierJSON = this.conteneurPanier.querySelector(".donnees-panier").dataset.panier;
        if (!panierJSON || panierJSON.length === 0 || panierJSON === "{}")
        {
            return;
        }

        let panier = JSON.parse(panierJSON);
        for (let idProduit in panier)
        {
            this.ajouter_panier_session(idProduit, panier[idProduit]);
        }

        $("#formulairePanier").collapse('show');
    }

    // Utilisé pour l'ajout d'élément dans le panier grace au variable de session PHP
    ajouter_panier_session (idProduit, tableauInfo)
    {
        let imageHTML = tableauInfo["image"];
        let prixDeBase = tableauInfo["prix"];
        let stock = tableauInfo["stock"];
        let prixTotal = prixDeBase * stock;

        this.creer_article(idProduit, imageHTML, prixDeBase, prixTotal, stock);
        this.totalPanier.value = Number(this.totalPanier.value) + prixTotal;
        this.totalPanierAffichage.innerHTML = this.totalPanier.value;
    }

    ajouter_panier (evenement)
    {
        // On reprend les éléments qui nous intéresse à partir de l'article
        let idProduit = evenement.currentTarget.id;
        let prix = Number(evenement.currentTarget.parentElement.querySelector(".prix-produit").innerHTML); 
        
        // Le total du panier augmente
        this.totalPanier.value = Number(this.totalPanier.value) + Number(prix);
        this.totalPanierAffichage.innerHTML = this.totalPanier.value;
        
        // Si le produit est déjà dans le panier
        if (this.nombreProduits.indexOf(idProduit) !== -1)
        {
            this.traiter_repetition_produit(idProduit, prix);
        }
        else
        {
            let imageHTML = evenement.currentTarget.parentElement.parentElement.querySelector(".image-produit").outerHTML;
            this.creer_article(idProduit, imageHTML, prix, prix, 1);
        }

        $("#formulairePanier").collapse('show');
    }

    enlever_panier (evenement)
    {
        let conteneurProduit = evenement.currentTarget.closest(".produit-panier");
        let idProduit = conteneurProduit.id;
        let prixTotalProduit = conteneurProduit.querySelector('.prix').innerHTML;

        conteneurProduit.remove();
        this.totalPanier.value = Number(this.totalPanier.value) - Number(prixTotalProduit);
        this.totalPanierAffichage.innerHTML = this.totalPanier.value;

        let index = this.nombreProduits.indexOf(idProduit);
        if (index > -1) 
        {
            this.nombreProduits.splice(index, 1);
        }

        // On sauvegarde la suppression dans le cookie
        this.supprimer_produit_cookie (idProduit);

        if(this.totalPanier.value === "0" || !this.totalPanier.value)
        {
            this.initialiser_panier();
            Cookie.deleteCookie("panier");
        }
    }

    alerte_non_membre()
    {
        document.querySelector(".alerte-non-autorise").hidden = false;
    }

    creer_article (idProduit, imageHTML, prixDeBase, prixTotal, stock)
    {
        let htmlCode = `
        <article class="produit-panier card m-1 bg-secondary" id="${idProduit}">

            <div class="card-body p-0">
                ${imageHTML}

                <div class="card-img-overlay p-0 pr-2 text-right">
                    <span class="bouton-enlever-article fa fa-window-close text-dark" aria-hidden="true"></span>
                </div>
            </div>

            <div class="card-footer p-0 text-center">
                <div class="w-100">
                    <span class="prix-de-base">${prixDeBase}</span> &euro; | 
                    <span class="prix">${prixTotal}</span> &euro;
                </div>
                <input class="nombre-produit text-center w-100 position-relative" type="number" value="${stock}" 
                    min="1" max="100" step="1" required>
            </div>

            <input type="hidden" name="produit_${idProduit}" value="${idProduit}|${stock}">
        </article>
        `;

        this.panierHTML.insertAdjacentHTML('beforeend', htmlCode);

        // On met a jour ce tableau pour les autres ajout s'il y'en a
        this.nombreProduits.push(idProduit);
        // On ajoute les événement du nouveau bouton
        this.initialiser_boutons_produit(idProduit);
        // On sauvegarde le panier dans un cookie pour que php le traite
        this.mettre_a_jour_cookie(idProduit, prixDeBase, stock, imageHTML);
    }

    initialiser_boutons_produit (idProduit)
    {
        let boutonNombreProduit = document.querySelector(`.produit-panier[id="${idProduit}"] .nombre-produit`);
        let mettre_prix_a_jour = this.mettre_prix_a_jour.bind(this);
        boutonNombreProduit.addEventListener("change", mettre_prix_a_jour);

        let boutonEnleverProduit = document.querySelector(`.produit-panier[id="${idProduit}"] .bouton-enlever-article`);
        let enlever_panier = this.enlever_panier.bind(this);
        boutonEnleverProduit.addEventListener("click", enlever_panier);
    }

    mettre_prix_a_jour (evenement)
    {
        let elementParent = evenement.currentTarget.parentElement;
        let idProduit = elementParent.parentElement.id;

        let prixDeBase = elementParent.querySelector(".prix-de-base").innerHTML;

        let nombreProduit = this.verification_nombre_produit(evenement.currentTarget.value);
        evenement.currentTarget.value = nombreProduit;

        // On met à jour pour le formulaire
        this.mettre_a_jour_stock_cookie(idProduit, nombreProduit);

        let prixProduitDebut = elementParent.querySelector(".prix").innerHTML;
        elementParent.querySelector(".prix").innerHTML = Number(prixDeBase) * Number(nombreProduit);
        let prixProduitFin = elementParent.querySelector(".prix").innerHTML;

        this.panierHTML.querySelector(`.produit-panier[id="${idProduit}"]`).querySelector("input[type=hidden]").value = `${idProduit}|${nombreProduit}`;
        
        let totalPanier = this.totalPanier.value;
        this.totalPanier.value = Number(totalPanier) - Number(prixProduitDebut) + Number(prixProduitFin);
        this.totalPanierAffichage.innerHTML = this.totalPanier.value;
    }

    verification_nombre_produit (nombre)
    {
        if (nombre == false ||
            (typeof(nombre) !== "string" && typeof(nombre) !== "number") ||
            !est_entier(nombre) ||
            nombre < 1)
        {
            return 1;
        }

        return nombre;
    }

    traiter_repetition_produit (idProduit, prix)
    {
        let produitHTML = this.panierHTML.querySelector(`.produit-panier[id="${idProduit}"]`);

        let nombreProduit = Number(produitHTML.querySelector(".nombre-produit").value) + 1;
        prix *= nombreProduit;

        // On met à jour les valeurs du produit du panier
        produitHTML.querySelector(".nombre-produit").value = nombreProduit;
        produitHTML.querySelector(".prix").innerHTML = prix; 
        produitHTML.querySelector("input[type=hidden]").value = `${idProduit}|${nombreProduit}`;

        this.mettre_a_jour_stock_cookie(idProduit, nombreProduit);
    }

    supprimer_produit_cookie (idProduit)
    {
        let panier = Cookie.getCookie("panier");
        if (!panier)
        {
            throw "Le panier doit être rempli si on veut supprimer un produit";
        }
        
        panier = JSON.parse(panier);
        
        delete panier[idProduit];
        Cookie.setCookie("panier", JSON.stringify(panier));
    }

    mettre_a_jour_cookie (id, prix, stock, image)
    {
        let panier = Cookie.getCookie("panier");

        if (panier !== undefined)
        {
            panier = JSON.parse(panier);
        }
        else
        {
            panier = new Object();
        }

        panier[id] = {
            "prix" : prix,
            "stock" : stock,
            "image" : image
        }

        Cookie.setCookie("panier", JSON.stringify(panier));
    }

    mettre_a_jour_stock_cookie (id, stock)
    {
        let panier = Cookie.getCookie("panier");
        if (panier === undefined)
        {
            throw ("Le panier doit exister");
        }
        
        panier = JSON.parse(panier)

        panier[id]["stock"] = stock;
        Cookie.setCookie("panier", JSON.stringify(panier));
    }
}

let panier = new Panier();