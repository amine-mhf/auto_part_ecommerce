<?php

// HEADER - EN-TÊTE COMMUN À TOUTES LES PAGES

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// COMPTEUR DYNAMIQUE DU PANIER (nombre total d'articles pour l'utilisateur connecté)
$nb_articles_panier = 0;
if (isset($_SESSION['user_id']) && isset($connexion)) {
    $stmt_panier_count = $connexion->prepare("SELECT COALESCE(SUM(quantite), 0) FROM panier WHERE utilisateur_id = ?");
    $stmt_panier_count->execute([$_SESSION['user_id']]);
    $nb_articles_panier = (int) $stmt_panier_count->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APIS RAPIDO - Pièces détachées automobiles</title>

    <!-- CSS  -->

    <link rel="stylesheet" href="/pieces_auto/ressources/css/style.css?v=8">

    <!-- CSS admin uniquement si on est dans le dossier /admin/ -->

    <?php if (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false): ?>
        <link rel="stylesheet" href="/pieces_auto/ressources/css/admin.css?v=8">
    <?php endif; ?>

    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300..700&display=swap" rel="stylesheet">
</head>

<body>

    <!--  HEADER PRINCIPAL (LOGO, RECHERCHE, COMPTE)-->
    <header class="main-header">
        <div class="container">
            <a href="/pieces_auto/index.php" class="logo">
                <img src="/pieces_auto/ressources/images/logo/logo.png" alt="APIS RAPIDO" class="logo-img">
            </a>

            <form class="search-form" action="/pieces_auto/produits.php" method="GET">
                <input type="search" name="recherche" placeholder="Rechercher un produit...">
                <button type="submit">
                    <img src="/pieces_auto/ressources/images/icons/loupe.png" alt="Rechercher" class="nav-icon">
                </button>
            </form>

            <button type="button" class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu">
                <span></span><span></span><span></span>
            </button>

            <div class="user-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/pieces_auto/profil.php" class="icon-link" title="Mon profil">
                        <img src="/pieces_auto/ressources/images/icons/profil.png" alt="Mon profil" class="nav-icon">
                    </a>
                    <a href="/pieces_auto/panier.php" class="icon-link cart-icon-link" title="Panier">
                        <img src="/pieces_auto/ressources/images/icons/panier.png" alt="Panier" class="nav-icon">
                        <span class="cart-count-badge" id="cartCountBadge" <?= $nb_articles_panier === 0 ? 'style="display:none;"' : '' ?>><?= $nb_articles_panier ?></span>
                    </a>
                    <a href="/pieces_auto/deconnexion.php" class="icon-link" title="Déconnexion">
                        <img src="/pieces_auto/ressources/images/icons/deconnexion.png" alt="Déconnexion" class="nav-icon">
                    </a>
                <?php else: ?>
                    <a href="/pieces_auto/connexion.php" class="btn-outline">Connexion</a>
                    <a href="/pieces_auto/inscription.php" class="btn-outline">Inscription</a>
                    <a href="/pieces_auto/panier.php" class="icon-link cart-icon-link" title="Panier">
                        <img src="/pieces_auto/ressources/images/icons/panier.png" alt="Panier" class="nav-icon">
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!--  MENU DE NAVIGATION PRINCIPAL -->
    <nav class="main-nav" id="mainNav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="/pieces_auto/index.php">Accueil</a></li>

                <li class="dropdown">
                    <a href="#">Catégories ▾</a>
                    <ul class="dropdown-menu">

                        <!-- Moteur -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=3">Moteur ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=8">Pistons</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=9">Segment</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=10">Joint de culasse</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=11">Vilebrequin</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=12">Arbre à cames</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=13">Soupapes</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=14">Turbo</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=15">Pompe à huile</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=16">Pompe à eau</a></li>
                            </ul>
                        </li>

                        <!-- Embrayage -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=4">Embrayage ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=17">Kit embrayage</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=18">Volant moteur</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=19">Câble d'embrayage</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=20">Émetteur hydraulique</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=21">Récepteur hydraulique</a></li>
                            </ul>
                        </li>

                        <!-- Suspension -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=5">Suspension ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=22">Amortisseur</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=23">Ressort-amortisseur</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=24">Biellette de suspension</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=25">Triangle de suspension</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=26">Rotule</a></li>
                            </ul>
                        </li>

                        <!-- Freinage -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=2">Freinage ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=4">Plaquettes de frein</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=5">Disques de frein</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=6">Tambours</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=7">Liquide de frein</a></li>
                            </ul>
                        </li>

                        <!-- Filtre -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=1">Filtre ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=1">Filtre à huile</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=2">Filtre à air</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=3">Filtre habitacle</a></li>
                            </ul>
                        </li>

                        <!-- Carrosserie -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=6">Carrosserie ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=27">Pare-chocs</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=28">Aile</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=29">Rétroviseur</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=30">Calandre</a></li>
                            </ul>
                        </li>

                        <!-- Électricité -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=8">Électricité ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=35">Alternateur</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=36">Bobine d'allumage</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=41">Capteurs</a></li>
                            </ul>
                        </li>

                        <!-- Huiles et fluides -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=7">Huiles et fluides ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=31">Huile moteur</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=32">Liquide de refroidissement</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=33">Lave-glace</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=34">Huile de boîte</a></li>
                            </ul>
                        </li>

                        <!-- Courroies, chaînes, galets -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=10">Courroies, chaînes, galets ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=40">Courroie d'alternateur</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=41">Chaîne de distribution</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=42">Tendeur</a></li>
                            </ul>
                        </li>

                        <!-- Refroidissement -->
                        <li class="dropdown-sub">
                            <a href="/pieces_auto/produits.php?categorie=9">Refroidissement ▸</a>
                            <ul class="dropdown-submenu">
                                <li><a href="/pieces_auto/produits.php?sous_categorie=38">Radiateur</a></li>
                                <li><a href="/pieces_auto/produits.php?sous_categorie=39">Ventilateur</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <li><a href="/pieces_auto/produits.php">Boutique</a></li>

                <li><a href="/pieces_auto/panier.php">Panier</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/pieces_auto/mes_commandes.php">Mes commandes</a></li>
                <?php endif; ?>

                <li><a href="/pieces_auto/contact.php">Contact</a></li>

                <!-- Lien ADMINISTRATION (visible uniquement pour les administrateurs) -->

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="/pieces_auto/admin/index.php" class="admin-link">Administration</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="main-content">
