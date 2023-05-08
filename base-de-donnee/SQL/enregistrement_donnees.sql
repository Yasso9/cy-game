CREATE DATABASE IF NOT EXISTS `cygame_db`;
USE `cygame_db`;

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur`
(
    `id_utilisateur` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `pseudo_utilisateur` VARCHAR(30) NOT NULL UNIQUE,
    `sexe_utilisateur` VARCHAR(6) NOT NULL,
    `naissance_utilisateur` DATE NOT NULL,
    `profession_utilisateur` VARCHAR(25) NOT NULL,
    `ville_utilisateur` VARCHAR(25) NOT NULL,
    `role_utilisateur` VARCHAR(25) NOT NULL,

    `nom_utilisateur` VARCHAR(25) NOT NULL,
    `prenom_utilisateur` VARCHAR(25) NOT NULL,
    `adresse_utilisateur` VARCHAR(100) NOT NULL,
    `motDePasse_utilisateur` VARCHAR(255) NOT NULL,
    `produitsAchetes_utilisateur` VARCHAR(500) DEFAULT '',
    `evenementsParticipes_utilisateur` VARCHAR(500) DEFAULT ''
);


INSERT INTO `utilisateur` 
(
    `pseudo_utilisateur`, 
    `sexe_utilisateur`, 
    `naissance_utilisateur`, 
    `profession_utilisateur`,
    `ville_utilisateur`,
    `role_utilisateur`,

    `nom_utilisateur`,
    `prenom_utilisateur`,
    `adresse_utilisateur`,
    `motDePasse_utilisateur`,
    `produitsAchetes_utilisateur`,
    `evenementsParticipes_utilisateur`
)
VALUES
('Yasso', 'homme', '2001-09-25', 'Etudiant', 'LIsle Adam', 'admin', 'Turki', 'Ilyas', '33 rue du Rossignol', '$2y$10$yLaZYNdx8XTqJs4Qaz6zU.UTfXURZiUoqqeOA6PwvAwaWgaYk8bXy', '|1-1||2-1||3-2|', '|1||2||3|'),
('ChapeauDePaille', 'homme', '1997-07-22', 'Pirate', 'Fushia', 'membre', 'Monkey D', 'Luffy', 'Wa no Kuni', '$2y$10$li39fWdJtniAul.GEkFGXu2faOnhsa.ItWQFs/zxpuJ6nWpkzCM82', '', ''),
('Sarko', 'homme', '1955-01-29', 'Mytho', 'Paris', 'membre', 'Sarkozy', 'Nicolas', 'Non Connu', '$2y$10$INPDymRpusf6bWXRNPCWK.6.epyYBwES7iiCoN25371FrYdWRIhKe', '', '');




         
DROP TABLE IF EXISTS `evenement`;
CREATE TABLE `evenement`
(
    `id_evenement` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nom_evenement` VARCHAR(100) NOT NULL,
    `lieu_evenement` VARCHAR(30) NOT NULL,
    `dateDebut_evenement` DATETIME NOT NULL,
    `dateFin_evenement` DATETIME NOT NULL,
    `description_evenement` VARCHAR(500),
    `prixParPersonne_evenement` INT NOT NULL,
    `cout_evenement` INT NOT NULL,
    `nombreParticipants_evenement` INT DEFAULT 0
);


INSERT INTO `evenement` 
(
    `nom_evenement`,
    `lieu_evenement`,
    `dateDebut_evenement`,
    `dateFin_evenement`,
    `description_evenement`,
    `prixParPersonne_evenement`,
    `cout_evenement`
)
VALUES
('Le Grand Tournois', 'Cergy', '2022-05-29 09:00', '2021-05-29 18:00:00', 'Tournois sur plusieurs jeux', '5', '200'),
('FIFA', 'Cergy', '2021-09-29 09:00:00', '2022-09-29 18:00:00', 'Tournois FIFA', '5', '400'),
('Vente Special de jeux', 'Cergy', '2022-05-05 09:00', '2021-05-05 18:00', 'Ventes', '10', '350'),
('Sortie au musee des arts', 'Paris', '2022-06-02 09:00', '2021-06-04 06:00', 'Sortie speciale', '20', '400');







DROP TABLE IF EXISTS `produit`;
CREATE TABLE `produit`
(
    `id_produit` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nom_produit` VARCHAR(30) NOT NULL,
    `image_produit` VARCHAR(100) DEFAULT '/styles/images/image-produit-non-disponible.svg',
    `marque_produit` VARCHAR(30) DEFAULT 'Inconnu',
    `prix_produit` INT NOT NULL,
    `coutFabrication_produit` INT NOT NULL,
    `stock_produit` INT NOT NULL,
    `nombreAchat_produit` INT DEFAULT 0
);

INSERT INTO `produit` 
(
    `nom_produit`,
    `image_produit`,
    `marque_produit`,
    `prix_produit`,
    `coutFabrication_produit`,
    `stock_produit`
)
VALUES
('Carte PSN 5 euros', '/base-de-donnee/images-boutique/carte-psn-5.jpg', 'Sony', '5', '4', '20'),
('Carte PSN 50 euros', '/base-de-donnee/images-boutique/carte-psn-50.jpg', 'Sony', '50', '46', '5'),
('Station de recharge manette', '/base-de-donnee/images-boutique/station-recharge.jpg', 'Echtpower', '30', '25', '10'),
('Sacoche de transport Switch', '/base-de-donnee/images-boutique/sacoche-switch.jpg', 'Nintendo', '10', '7', '30'),
('Veste Homme', '/base-de-donnee/images-boutique/veste-homme.jpg', 'Male Alpha Romeo', '90', '50', '20'),
('Altere Magnet Plus', '/base-de-donnee/images-boutique/altere-magnet-plus.png', 'Magnet Plus', '30', '25', '10'),
('Airpods de luxe', '/base-de-donnee/images-boutique/wireless.png', 'Fit', '700', '30', '20'),
('Produit Inconnu', '/styles/images/image-produit-non-disponible.svg', 'Inconnu', '1000', '1', '200');
