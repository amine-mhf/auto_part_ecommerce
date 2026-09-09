<?php

require_once 'includes/config.php';
session_start();

// RÉCUPÉRATION DU PRODUIT PAR ID

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: produits.php');
    exit;
}

$sql = "SELECT p.*, sc.nom as sous_categorie_nom, c.nom as categorie_nom, f.nom as fabricant_nom, mv.nom as marque_vehicule_nom
        FROM produits p
        JOIN sous_categories sc ON p.sous_categorie_id = sc.id
        JOIN categories c ON sc.categorie_id = c.id
        JOIN fabricants f ON p.fabricant_id = f.id
        JOIN marques_vehicules mv ON p.marque_vehicule_id = mv.id
        WHERE p.id = ?";
$stmt = $connexion->prepare($sql);
$stmt->execute([$id]);
$produit = $stmt->fetch();

// Si le produit n'existe pas, redirection vers le catalogue

if (!$produit) { 
    header('Location: produits.php'); 
    exit; 
}

include 'includes/header.php';
?>

<!-- FICHE PRODUIT (2 COLONNES) -->

<div class="product-detail-modern">
    <div class="container">
        <div class="product-detail-grid">
            
            <!-- COLONNE GAUCHE : IMAGE -->
            <div class="product-detail-image">
                <img src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
            </div>

            <!-- COLONNE DROITE : INFORMATIONS -->
            <div class="product-detail-info">
                <!-- Badges catégories -->
                <div class="product-meta">
                    <span class="badge"><?= htmlspecialchars($produit['categorie_nom']) ?></span>
                    <span class="badge"><?= htmlspecialchars($produit['sous_categorie_nom']) ?></span>
                </div>

                <h1><?= htmlspecialchars($produit['nom']) ?></h1>
                <p class="product-reference">Réf : <?= htmlspecialchars($produit['reference']) ?></p>

                <!-- PRIX -->
                <div class="product-price-box">
                    <div class="product-price"><?= number_format($produit['prix'], 2, ',', ' ') ?> DA</div>
                </div>

                <!-- ACTIONS : AJOUTER AU PANIER (AJAX) / ACHETER MAINTENANT -->
                <div class="product-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($produit['stock'] > 0): ?>
                            <button class="btn-primary add-to-cart" data-id="<?= $produit['id'] ?>">Ajouter au panier</button>
                            <a href="acheter_maintenant.php?id=<?= $produit['id'] ?>" class="btn-buy-now">Acheter maintenant</a>
                        <?php else: ?>
                            <button class="btn-disabled" disabled>Rupture de stock</button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="connexion.php" class="btn-outline">Se connecter pour acheter</a>
                    <?php endif; ?>
                </div>

                <!-- CARACTÉRISTIQUES TECHNIQUES -->
                <div class="product-specs">
                    <h3>Caractéristiques techniques</h3>
                    <ul>
                        <li><strong>Fabricant :</strong> <?= htmlspecialchars($produit['fabricant_nom']) ?></li>
                        <li><strong>Marque véhicule :</strong> <?= htmlspecialchars($produit['marque_vehicule_nom']) ?></li>
                        <li><strong>Stock :</strong> 
                            <?= $produit['stock'] > 0 ? $produit['stock'] . ' disponible(s)' : '<span class="stock-rupture">Rupture</span>' ?>
                        </li>
                    </ul>
                </div>

                <!-- DESCRIPTION -->
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
                </div>

                <!-- LIEN RETOUR AU CATALOGUE -->
                <div class="product-back">
                    <a href="produits.php" class="btn-back">← Retour au catalogue</a>
                </div>
            </div>
        </div>

        <!-- PRODUITS SIMILAIRES -->

        <div class="similar-products">
            <h3>Produits similaires</h3>
            <div class="similar-grid">
                <?php
                $similar = $connexion->prepare("SELECT id, nom, prix, image, stock FROM produits WHERE sous_categorie_id = ? AND id != ? LIMIT 4");
                $similar->execute([$produit['sous_categorie_id'], $produit['id']]);
                $similaires = $similar->fetchAll();

                if (empty($similaires)): ?>
                    <div class="empty-state">
                        <ion-icon name="cube-outline"></ion-icon>
                        <p>Aucun produit similaire trouvé.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($similaires as $sim): ?>
                        <div class="similar-card">
                            <img src="<?= htmlspecialchars($sim['image']) ?>" alt="<?= htmlspecialchars($sim['nom']) ?>">
                            <h4><?= htmlspecialchars($sim['nom']) ?></h4>
                            <p class="price"><?= number_format($sim['prix'], 2, ',', ' ') ?> DA</p>
                            <?php if ($sim['stock'] <= 0): ?>
                                <p class="stock-rupture">Rupture</p>
                            <?php endif; ?>
                            <a href="produit_detail.php?id=<?= $sim['id'] ?>" class="btn-small">Voir</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>