<?php
// PAGE D'ACCUEIL
require_once 'includes/config.php';
session_start();

// RÉCUPÉRATION DES 9 DERNIERS PRODUITS

$sql = "SELECT id, nom, description, prix, image, stock FROM produits ORDER BY id DESC LIMIT 9";
$stmt = $connexion->query($sql);
$produits = $stmt->fetchAll();

// Sécurité : si aucun produit, on évite une erreur d'affichage
if ($produits === false) {
    $produits = [];
}

include 'includes/header.php';
?>

<!-- BANNIÈRE PRINCIPALE -->
<div class="hero-banner">
    <img src="/pieces_auto/ressources/images/icons/banner.png" alt="Banner APIS RAPIDO" class="banner-img">
    <a href="produits.php" class="btn-hero-commander">Commander</a>
</div>
<!-- SECTION DES DERNIÈRES PIÈCES -->
<div class="accueil-container">
    <h2>Nos dernières pièces</h2>
    <p class="section-subtitle">Découvrez les pièces automobiles récemment ajoutées à notre catalogue</p>

    <?php if (empty($produits)): ?>
        <p>Aucun produit disponible pour le moment.</p>
    <?php else: ?>
        <div class="products-grid home-products-grid">
            <?php foreach ($produits as $p): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($p['nom']) ?></h3>
                        <p class="product-desc-short"><?= htmlspecialchars($p['description']) ?></p>

                        <p class="price"><?= number_format($p['prix'], 2, ',', ' ') ?> DA</p>
                        <?php if ($p['stock'] <= 0): ?>
                            <p class="stock-rupture">Rupture de stock</p>
                        <?php endif; ?>

                        <div class="card-actions-gap">
                            <?php if ($p['stock'] > 0): ?>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button class="btn-add add-to-cart" data-id="<?= $p['id'] ?>">Ajouter au panier</button>
                                    <a href="acheter_maintenant.php?id=<?= $p['id'] ?>" class="btn-buy-now">Acheter</a>
                                <?php else: ?>
                                    <a href="connexion.php" class="btn-outline-small">Se connecter pour acheter</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn-disabled" disabled>Indisponible</button>
                            <?php endif; ?>

                            <a href="produit_detail.php?id=<?= $p['id'] ?>" class="btn-detail">Voir détail</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="see-all-container">
            <a href="produits.php" class="btn-see-all">Voir tous les produits</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
