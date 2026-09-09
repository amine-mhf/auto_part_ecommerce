--------------------------------------------------------------------------------------------------------
----------------------------------------Création des tables 
--------------------------------------------------------------------------------------------------------

-- 1.Table utilisateur pour formulaire d'inscription et connexion:

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(10) NULL,
    wilaya VARCHAR(50) NOT NULL,
    adresse TEXT NOT NULL,
    role ENUM('client', 'admin') DEFAULT 'client',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. CATÉGORIES PRINCIPALES (ex: Filtre, Freinage)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

--3. SOUS-CATÉGORIES (ex: Filtre à huile, Plaquettes)

CREATE TABLE sous_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    categorie_id INT NOT NULL,
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
);

-- 4. MARQUES DE VÉHICULES CIBLÉES (ex: Hyundai, Standard)
CREATE TABLE marques_vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

-- 5. FABRICANTS DES PRODUITS (ex: Bosch, Mann)
CREATE TABLE fabricants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

-- 6. PRODUITS (table principale) Contient toutes les infos + clés étrangères

CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(50) UNIQUE NOT NULL,
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    sous_categorie_id INT NOT NULL,
    fabricant_id INT NOT NULL,
    marque_vehicule_id INT NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sous_categorie_id) REFERENCES sous_categories(id),
    FOREIGN KEY (fabricant_id) REFERENCES fabricants(id),
    FOREIGN KEY (marque_vehicule_id) REFERENCES marques_vehicules(id)
);

-- 7. TABLE COMMANDES

CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'expediee', 'livree') DEFAULT 'en_attente',
    total DECIMAL(10,2) NOT NULL,
    adresse_livraison TEXT NOT NULL,
    wilaya VARCHAR(50) NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);


-- 8. TABLE DETAILS_COMMANDES

CREATE TABLE  details_commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- 9. TABLE PANIER

CREATE TABLE panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);


--------------------------------------------------------------------------------------
--------------------------------Remplissage des tables 
--------------------------------------------------------------------------------------

-- 1. CATÉGORIES PRINCIPALES (ex: Filtre, Freinage)

INSERT INTO categories (id, nom) VALUES
(1, 'Filtre'),
(2, 'Freinage'),
(3, 'Moteur'),
(4, 'Embrayage'),
(5, 'Suspension'),
(6, 'Carrosserie'),
(7, 'Huiles et fluides'),
(8, 'Électricité'),
(9, 'Refroidissement'),
(10, 'Courroies, chaînes, galets');

--2. SOUS-CATÉGORIES (ex: Filtre à huile, Plaquettes)

INSERT INTO sous_categories (id, nom, categorie_id) VALUES
(1, 'Filtre à huile', 1),
(2, 'Filtre à air', 1),
(3, 'Filtre habitacle', 1),
(4, 'Plaquettes de frein', 2),
(5, 'Disques de frein', 2),
(6, 'Tambours', 2),
(7, 'Liquide de frein', 2),
(8, 'Pistons', 3),
(9, 'Segment', 3),
(10, 'Joint de culasse', 3),
(11, 'Vilebrequin', 3),
(12, 'Arbre à cames', 3),
(13, 'Soupapes', 3),
(14, 'Turbo', 3),
(15, 'Pompe à huile', 3),
(16, 'Pompe à eau', 3),
(17, 'Kit embrayage', 4),
(18, 'Volant moteur', 4),
(19, 'Câble d''embrayage', 4),
(20, 'Émetteur hydraulique', 4),
(21, 'Récepteur hydraulique', 4),
(22, 'Amortisseur', 5),
(23, 'Ressort-amortisseur', 5),
(24, 'Biellette de suspension', 5),
(25, 'Triangle de suspension', 5),
(26, 'Rotule', 5),
(27, 'Pare-chocs', 6),
(28, 'Aile', 6),
(29, 'Rétroviseur', 6),
(30, 'Calandre', 6),
(31, 'Huile moteur', 7),
(32, 'Liquide de refroidissement', 7),
(33, 'Lave-glace', 7),
(34, 'Huile de boîte', 7),
(35, 'Alternateur', 8),
(36, 'Bobine d''allumage', 8),
(37, 'Capteurs (PMH, ABS, pluie, etc.)', 8),
(38, 'Radiateur', 9),
(39, 'Ventilateur', 9),
(40, 'Courroie d''alternateur', 10),
(41, 'Chaîne de distribution', 10),
(42, 'Tendeur', 10);

-- 3. MARQUES DE VÉHICULES CIBLÉES (ex: Hyundai, Standard)

INSERT INTO marques_vehicules (id, nom) VALUES
(1, 'Chevrolet (CHEV)'),
(2, 'Kia'),
(3, 'Suzuki (SUZ)'),
(4, 'Hyundai (HY)'),
(5, 'Toyota (TY)'),
(6, 'Standard (Kia / Hyundai)'),
(7, 'Standard (toutes marques)');

-- 4. FABRICANTS DES PRODUITS (ex: Bosch, Mann)

INSERT INTO fabricants (id, nom) VALUES
(1, 'GM'),
(2, 'CSA'),
(3, 'MOBIS'),
(4, 'GENUINE'),
(5, 'MGP'),
(6, 'TOYOTA'),
(7, 'MANDO'),
(8, '7S'),
(9, 'ICRBI'),
(10, 'OGT'),
(11, 'G AUTO'),
(12, 'PERFECT'),
(13, 'TOPIC'),
(14, 'ONNURI'),
(15, 'KOREA STAR'),
(16, 'SGM'),
(17, 'KR/GHF'),
(18, 'SYNCRONIX'),
(19, 'VALEO'),
(20, 'NOVEOM'),
(21, 'TOTAL'),
(22, 'ELF'),
(23, 'ARECA'),
(24, 'DEMSTIQUE'),
(25, 'GARRET'),
(26, 'FORCE+'),
(27, 'PMC'),
(28, 'NPC'),
(29, 'PREMIUM'),
(30, 'FORCE GOLD'),
(31, 'MOTO GATE'),
(32, 'KINGA'),
(33, 'PHC KR'),
(34, 'GATES'),
(35, 'JCP'),
(36, 'OEM'),
(37, 'CTR');


-- 5. PRODUITS (table principale) Contient toutes les infos + clés étrangères

INSERT INTO produits (id, reference, nom, description, prix, stock, image, sous_categorie_id, fabricant_id, marque_vehicule_id) VALUES
(1, '66321-G6010', 'Aile', 'Aile avant droite Kia Picanto GT Line / Ex', 19000, 2, 'ressources/images/Carrosserie/66321-G6010.png', 28, 4, 2),
(2, '57611M55R10', 'Aile', 'Aile avant droite Suzuki Swift Nv 2022', 13500, 2, 'ressources/images/Carrosserie/57611M55R10.png', 28, 5, 3),
(3, '13579666', 'Alternateur', 'Alternateur Chevrolet Cruze 1.8 Ess / Sonic / Trax', 31500, 2, 'ressources/images/Électricité/13579666.png', 35, 1, 1),
(4, '37300-4A300', 'Alternateur', 'Alternateur Kia Sorento Vgt', 38000, 2, 'ressources/images/Électricité/37300-4A300.png', 35, 3, 2),
(5, '81780-B4000', 'Amortisseur malle', 'Amortisseur malle droite Hyundai Grand I10', 1800, 2, 'ressources/images/SUSPENSION/81780-B4000.png', 22, 8, 4),
(6, '48530-80384', 'Amortisseur arrière', 'Amortisseur arrière Toyota Yaris 2007-2013 5v 2pcs', 14500, 2, 'ressources/images/SUSPENSION/48530-80384.png', 22, 6, 5),
(7, 'EX54651-C5000', 'Amortisseur', 'Amortisseur avant Kia Sorento Lh 2015-2020', 14800, 2, 'ressources/images/SUSPENSION/EX54651-C5000.png', 22, 7, 2),
(8, '24100-03052', 'Arbre à cames', 'Arbre à cames admission Kia Picanto 4', 17500, 2, 'ressources/images/MOTEUR/24100-03052.png', 12, 4, 2),
(9, '9002821', 'Arbre à cames', 'Arbre à cames admission Chevrolet Sail 1.2 X26', 7500, 2, 'ressources/images/MOTEUR/9002821.png', 12, 16, 1),
(10, '48820-42030', 'Biellette de suspension', 'Biellette de suspension avant Toyota Rav4 2007-2015 / Corolla D4d / Auris 2pcs', 5300, 10, 'ressources/images/SUSPENSION/48820-42030.png', 24, 12, 5),
(11, '95947829', 'Biellette de suspension', 'Biellette de suspension avant Chevrolet Spark 3', 1350, 10, 'ressources/images/SUSPENSION/95947829.png', 24, 8, 1),
(12, '27300-2E000', 'Bobine d''allumage', 'Bobine d''allumage Kia Sportage 3 / Hyundai Tucson 3 / I40 Essence', 4600, 4, 'ressources/images/Électricité/27300-2E000.png', 36, 13, 6),
(13, '33400M68K30', 'Bobine d''allumage', 'Bobine d''allumage Suzuki Celerio / Swift / K10', 5900, 4, 'ressources/images/Électricité/33400M68K30.png', 36, 5, 3),
(14, '41510-02010', 'Câble d''embrayage', 'Câble d''embrayage Hyundai Atos 4', 2550, 7, 'ressources/images/EMBRAYAGE/41510-02010.png', 19, 15, 4),
(15, '41510-0X000', 'Câble d''embrayage', 'Câble d''embrayage Hyundai I10', 3200, 8, 'ressources/images/EMBRAYAGE/41510-0X000.png', 19, 15, 4),
(16, '86350-G6010', 'Calandre', 'Calandre Kia Picanto Nv Ex Chromet', 13000, 2, 'ressources/images/Carrosserie/86350-G6010.png', 30, 4, 2),
(17, '86351-D3000', 'Calandre', 'Calandre Hyundai Tucson 3 2017-2019 2wd', 11800, 2, 'ressources/images/Carrosserie/86351-D3000.png', 30, 10, 4),
(18, '58940-G6300', 'Capteur Abs', 'Capteur Abs Kia Picanto Gt Line Rh', 4500, 4, 'ressources/images/Électricité/58940-G6300.png', 37, 3, 2),
(19, '96868917', 'Capteur arbre à cames', 'Capteur arbre à cames Chevrolet Captiva 2.2', 9500, 3, 'ressources/images/Électricité/96868917.png', 37, 1, 1),
(20, '9025331', 'Capteur vilebrequin', 'Capteur vilebrequin Chevrolet Sail 1.2', 3200, 5, 'ressources/images/Électricité/9025331.png', 37, 16, 1),
(21, '35170-22600', 'Capteur corps injection', 'Capteur corps injection Hyundai Accent 2', 3200, 6, 'ressources/images/Électricité/35170-22600.png', 37, 17, 4),
(22, '24361-2F000', 'Chaîne distribution', 'Chaîne distribution Hyundai Tucson 3 Ix35 / Kia Sportage / Sorento 3', 14500, 8, 'ressources/images/Courroies, chaînes, galets/24361-2F000.png', 41, 4, 6),
(23, '6PK2220', 'Courroie alternateur', 'Courroie alternateur Chevrolet Captiva 2.2 / Cruze 2.2', 2800, 6, 'ressources/images/Courroies, chaînes, galets/6PK2220.png', 40, 18, 1),
(24, '6PK1555', 'Courroie alternateur', 'Courroie alternateur Chevrolet Cruze 1.8 / Trax / Sonic', 2200, 5, 'ressources/images/Courroies, chaînes, galets/6PK1555.png', 40, 18, 1),
(25, '96625948', 'Disque de frein', 'Disque de frein avant Chevrolet Captiva', 13000, 2, 'ressources/images/FREINAGE/96625948.png', 5, 8, 1),
(26, '41712-C1000', 'Disque de frein', 'Disque de frein avant Hyundai Tucson 4 / Kia Sportage 4', 13000, 2, 'ressources/images/FREINAGE/41712-C1000.png', 5, 11, 6),
(27, '24537285', 'Émetteur d''embrayage', 'Émetteur d''embrayage Chevrolet Optra 3', 5500, 2, 'ressources/images/EMBRAYAGE/24537285.png', 20, 16, 1),
(28, '41610-1D100', 'Émetteur d''embrayage', 'Émetteur d''embrayage Kia Carens 3 MTL', 9500, 2, 'ressources/images/EMBRAYAGE/41610-1D100.png', 20, 3, 2),
(29, '96440878', 'Filtre habitacle', 'Filtre habitacle Chevrolet Captiva', 1200, 12, 'ressources/images/FILTRE/96440878.png', 3, 26, 1),
(30, '28113-0X000', 'Filtre à air', 'Filtre à air Hyundai I10 1.1', 900, 9, 'ressources/images/FILTRE/28113-0X000.png', 2, 9, 4),
(31, '96395221D', 'Filtre à huile', 'Filtre à huile Chevrolet Spark / Aveo 1.5 / Optra / Cruze 1.6 Essence', 700, 7, 'ressources/images/FILTRE/96395221D.png', 1, 14, 1),
(32, '26320-2U000', 'Filtre à huile', 'Filtre à huile Hyundai Elantra Nv / I30 Nv / Cerato Nc', 1200, 9, 'ressources/images/FILTRE/26320-2U000.png', 1, 8, 6),
(33, '28113-D3300', 'Filtre à air', 'Filtre à air Hyundai Tucson 4 / Kia Sportage 4 Essence', 1500, 11, 'ressources/images/FILTRE/28113-D3300.png', 2, 15, 6),
(34, '97133-F2000', 'Filtre habitacle', 'Filtre habitacle Hyundai New Elantra 2017 / Kia Rio 5', 1400, 11, 'ressources/images/FILTRE/97133-F2000.png', 3, 27, 6),
(35, 'ARECA_75w80', 'Huile de boîte de vitesse', 'Huile de boîte de vitesse 75w80 1l', 1450, 9, 'ressources/images/HUILE ET LIQUIDES/ARECA_75w80.png', 34, 23, 7),
(36, 'ELF_75w80', 'Huile de boîte de vitesse', 'Huile de boîte de vitesse 75w80 1l', 1650, 9, 'ressources/images/HUILE ET LIQUIDES/ELF_75w80.png', 34, 22, 7),
(37, 'TOTAL_10w40', 'Huile moteur', 'Huile moteur 10w40 5l Dz', 6000, 9, 'ressources/images/HUILE ET LIQUIDES/TOTAL_10w40.png', 31, 21, 7),
(38, 'ELF_15w40', 'Huile moteur', 'Huile moteur 15w40 5l Fr', 6500, 9, 'ressources/images/HUILE ET LIQUIDES/ELF_15w40.png', 31, 22, 7),
(39, '55562233', 'Joint de culasse métal', 'Joint de culasse métal Chevrolet Sonic 1.2 Nv', 11500, 9, 'ressources/images/MOTEUR/55562233.png', 10, 1, 1),
(40, '22311-03445', 'Joint de culasse papier', 'Joint de culasse papier Hyundai Rb Nv 1.2 / Kia Rio 5', 2000, 5, 'ressources/images/MOTEUR/22311-03445.png', 10, 8, 6),
(41, 'HDK-169', 'Kit d''embrayage', 'Kit d''embrayage Hyundai H1 1.3', 31000, 2, 'ressources/images/EMBRAYAGE/HDK-169.png', 17, 19, 4),
(42, 'HDK-180', 'Kit d''embrayage', 'Kit d''embrayage Kia Picanto 4.5 / I10 1.2 / I20 1.2 / Grand I10 / Rio 4', 22000, 3, 'ressources/images/EMBRAYAGE/HDK-180.png', 17, 19, 6),
(43, 'LAVE_1', 'Lave glace', 'Lave glace 5l Demstique', 350, 13, 'ressources/images/HUILE ET LIQUIDES/LAVE_1.png', 33, 24, 7),
(44, 'valeo3', 'Liquide de frein', 'Liquide de frein Dot3 55', 700, 14, 'ressources/images/FREINAGE/valeo3.png', 7, 19, 7),
(45, 'valeo4', 'Liquide de frein', 'Liquide de frein Dot4 55+', 750, 9, 'ressources/images/FREINAGE/valeo4.png', 7, 19, 7),
(46, 'lq_g12', 'Liquide de refroidissement', 'Liquide de refroidissement G12 2l Noveom', 1000, 8, 'ressources/images/HUILE ET LIQUIDES/lq_g12.png', 32, 20, 7),
(47, 'lq_g13', 'Liquide de refroidissement', 'Liquide de refroidissement G13 5l Noveom', 2500, 5, 'ressources/images/HUILE ET LIQUIDES/lq_g13.png', 32, 20, 7),
(48, '86511-C7500', 'Pare-chocs', 'Pare-chocs avant Hyundai I20 3', 17500, 2, 'ressources/images/Carrosserie/86511-C7500.png', 27, 4, 4),
(49, '86511-G6300', 'Pare-chocs', 'Pare-chocs avant Kia Picanto 5 Gtl', 12500, 2, 'ressources/images/Carrosserie/86511-G6300.png', 27, 10, 2),
(50, '9048491', 'Piston', 'Piston 050 Chevrolet Sail 1.4', 4500, 2, 'ressources/images/MOTEUR/9048491.png', 8, 28, 1),
(51, '23041-03401', 'Piston', 'Piston Kia Std Picanto 4 / I10 1.2', 23500, 3, 'ressources/images/MOTEUR/23041-03401.png', 8, 3, 2),
(52, '96626069', 'Plaquettes de frein', 'Plaquettes de frein avant Chevrolet Captiva', 5300, 7, 'ressources/images/FREINAGE/96626069.png', 4, 29, 1),
(53, '04465-02222', 'Plaquettes de frein', 'Plaquettes de frein avant Toyota Corolla 3 2008-2012', 3400, 8, 'ressources/images/FREINAGE/04465-02222.png', 4, 30, 5),
(54, '25100-2U000', 'Pompe à eau', 'Pompe à eau Hyundai Creta Crdi 2021 / Kia Sportage Gtl 1.6', 17500, 2, 'ressources/images/MOTEUR/25100-2U000.png', 16, 3, 6),
(55, 'EWPD0012', 'Pompe à eau', 'Pompe à eau Chevrolet Sail 1.2 / Aveo Sport / Spark 3', 5800, 2, 'ressources/images/MOTEUR/EWPD0012.png', 16, 7, 1),
(56, '25193452', 'Pompe à huile', 'Pompe à huile Chevrolet Sail 1.2 / Aveo Sport / Spark 3', 25500, 2, 'ressources/images/MOTEUR/25193452.png', 15, 26, 1),
(57, '21340-42106', 'Pompe à huile', 'Pompe à huile Hyundai H100 2 / H1 3', 22500, 2, 'ressources/images/MOTEUR/21340-42106.png', 15, 8, 4),
(58, '25310-07500', 'Radiateur eau', 'Radiateur eau Kia Picanto 2', 11500, 4, 'ressources/images/Refroidissement/25310-07500.png', 38, 31, 2),
(59, '97606-B4000', 'Radiateur climatiseur', 'Radiateur climatiseur Hyundai Accent Rb / Kia Rio 4 / Grand I10 Essence', 18500, 2, 'ressources/images/Refroidissement/97606-B4000.png', 38, 32, 6),
(60, '96675855', 'Radiateur chauffage', 'Radiateur chauffage Chevrolet Spark 3', 5600, 2, 'ressources/images/Refroidissement/96675855.png', 38, 2, 1),
(61, '41710-23050', 'Récepteur d''embrayage', 'Récepteur d''embrayage Kia Rio 5', 14000, 3, 'ressources/images/EMBRAYAGE/41710-23050.png', 21, 3, 2),
(62, '41710-23000', 'Récepteur d''embrayage', 'Récepteur d''embrayage Hyundai Accent 4 / Kia Rio 3 / Cerato / Rb / I20', 4800, 2, 'ressources/images/EMBRAYAGE/41710-23000.png', 21, 19, 6),
(63, '96545713', 'Rétroviseur électrique', 'Rétroviseur électrique droit Chevrolet Optra', 5500, 2, 'ressources/images/Carrosserie/96545713.png', 29, 10, 1),
(64, '85101-4F100', 'Rétroviseur', 'Rétroviseur intérieur Hyundai H100 2', 2800, 2, 'ressources/images/Carrosserie/85101-4F100.png', 29, 3, 4),
(65, '96424024', 'Ressort d''amortisseur', 'Ressort d''amortisseur avant Chevrolet Spark 2', 3900, 2, 'ressources/images/SUSPENSION/96424024.png', 23, 2, 1),
(66, '54630-07200', 'Ressort d''amortisseur', 'Ressort d''amortisseur avant Kia Picanto', 3900, 2, 'ressources/images/SUSPENSION/54630-07200.png', 23, 2, 2),
(67, '56820-4N000', 'Rotule direction', 'Rotule direction gauche Hyundai Eon', 1300, 2, 'ressources/images/SUSPENSION/56820-4N000.png', 26, 2, 4),
(68, '56820-G6000', 'Rotule direction', 'Rotule direction Kia Picanto Ex / Gt Line', 8400, 2, 'ressources/images/SUSPENSION/56820-G6000.png', 26, 37, 2),
(69, '23040-22902', 'Segment', 'Segment 0.50 Hyundai Accent 1.5', 7500, 3, 'ressources/images/MOTEUR/23040-22902.png', 9, 3, 4),
(70, '23040-2B950', 'Segment', 'Segment Std Hyundai Creta Essence / I30 Nv 2018', 22000, 2, 'ressources/images/MOTEUR/23040-2B950.png', 9, 4, 4),
(71, '96335947', 'Soupape', 'Soupape Chevrolet Aveo 1.5', 4000, 4, 'ressources/images/MOTEUR/96335947.png', 13, 8, 1),
(72, '22212-26050', 'Soupape', 'Soupape Hyundai Accent 4 / Kia Rio 3 / Getz / Cerato', 6200, 4, 'ressources/images/MOTEUR/22212-26050.png', 13, 8, 6),
(73, '58411-02501', 'Tambour', 'Tambour arrière Hyundai Atos 2-3-4', 4300, 2, 'ressources/images/FREINAGE/58411-02501.png', 6, 33, 4),
(74, '58411-G6000', 'Tambour', 'Tambour arrière Kia Picanto Ex / Gt Line', 16000, 2, 'ressources/images/FREINAGE/58411-G6000.png', 6, 4, 2),
(75, '96416303', 'Tendeur de chaîne', 'Tendeur de chaîne Chevrolet Aveo 16v', 3800, 4, 'ressources/images/Courroies, chaînes, galets/96416303.png', 42, 36, 1),
(76, '25281-27401', 'Tendeur de courroie', 'Tendeur de courroie Hyundai Santa Fe / Kia Carens 3', 5400, 4, 'ressources/images/Courroies, chaînes, galets/25281-27401.png', 42, 34, 6),
(77, 'CAH0076D', 'Triangle de suspension', 'Triangle de suspension avant droit Hyundai Accent 4 / Kia Rio 3', 6600, 2, 'ressources/images/SUSPENSION/CAH0076D.png', 25, 7, 6),
(78, '55220-2E500', 'Triangle de suspension', 'Triangle de suspension arrière droit Hyundai Tucson / Kia Sportage', 4500, 2, 'ressources/images/SUSPENSION/55220-2E500.png', 25, 3, 6),
(79, '96440365', 'Turbo', 'Turbo Chevrolet Captiva', 96000, 1, 'ressources/images/MOTEUR/96440365.png', 14, 25, 1),
(80, '25380-B4000', 'Ventilateur', 'Ventilateur complet Hyundai Grand I10', 11500, 2, 'ressources/images/Refroidissement/25380-B4000.png', 39, 10, 4),
(81, '17100M67L00', 'Ventilateur', 'Ventilateur moteur Suzuki K10', 13000, 2, 'ressources/images/Refroidissement/17100M67L00.png', 39, 5, 3),
(82, '23111-05002', 'Vilebrequin', 'Vilebrequin Hyundai Eon', 39000, 2, 'ressources/images/MOTEUR/23111-05002.png', 11, 8, 4),
(83, '23110-03221', 'Vilebrequin', 'Vilebrequin Hyundai I10 1.2 / Kia Picanto 4', 23000, 2, 'ressources/images/MOTEUR/23110-03221.png', 11, 8, 6),
(84, '12620M79F01', 'Volant moteur', 'Volant moteur Suzuki Alto', 5800, 1, 'ressources/images/EMBRAYAGE/12620M79F01.png', 18, 35, 3);

---------------      Fin SQL ---------------------------------------