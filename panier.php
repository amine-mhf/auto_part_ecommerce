<?php

 // PANIER.PHP - GESTION DU PANIER D'ACHAT

require_once 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$erreur = '';

if (isset($_GET['vider'])) {
    $stmt = $connexion->prepare("DELETE FROM panier WHERE utilisateur_id = ?");
    $stmt->execute([$user_id]);
    header('Location: panier.php');
    exit;
}

if (isset($_GET['supprimer'])) {
    $produit_id = (int)$_GET['supprimer'];
    $stmt = $connexion->prepare("DELETE FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
    $stmt->execute([$user_id, $produit_id]);
    header('Location: panier.php');
    exit;
}

// MISE À JOUR DE LA QUANTITÉ (AJAX) - renvoie aussi le nouveau compteur global du panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $produit_id = (int)$_POST['produit_id'];
    $quantite = (int)$_POST['quantite'];

    if ($quantite <= 0) {
        $stmt = $connexion->prepare("DELETE FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
        $stmt->execute([$user_id, $produit_id]);
    } else {
        $stmt = $connexion->prepare("SELECT stock FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $stock = $stmt->fetchColumn();

        if ($quantite > $stock) {
            echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
            exit;
        }

        $stmt = $connexion->prepare("UPDATE panier SET quantite = ? WHERE utilisateur_id = ? AND produit_id = ?");
        $stmt->execute([$quantite, $user_id, $produit_id]);
    }

    // Nouveau total d'articles dans le panier (pour le badge du header)
    $stmt = $connexion->prepare("SELECT COALESCE(SUM(quantite), 0) FROM panier WHERE utilisateur_id = ?");
    $stmt->execute([$user_id]);
    $nouveau_compteur = (int) $stmt->fetchColumn();

    echo json_encode(['success' => true, 'removed' => $quantite <= 0, 'cart_count' => $nouveau_compteur]);
    exit;
}

$sql = "SELECT p.id, p.nom, p.prix, p.image, pa.quantite, p.stock as stock_disponible
        FROM panier pa
        JOIN produits p ON pa.produit_id = p.id
        WHERE pa.utilisateur_id = ?";
$stmt = $connexion->prepare($sql);
$stmt->execute([$user_id]);
$panier = $stmt->fetchAll();

$total_general = 0;
foreach ($panier as $item) {
    $total_general += $item['prix'] * $item['quantite'];
}

include 'includes/header.php';
?>

<div class="cart-container">
    <h1>Mon panier</h1>

    <?php if ($message): ?>
        <div class="alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if (empty($panier)): ?>
        <div class="empty-cart">
            <ion-icon name="cart-outline"></ion-icon>
            <p>Votre panier est vide.</p>
            <a href="produits.php" class="btn">Découvrir nos pièces</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items" id="cart-table">
                <?php foreach ($panier as $item):
                    $total_ligne = $item['prix'] * $item['quantite'];
                ?>
                    <div class="cart-item-card" data-id="<?= $item['id'] ?>" data-prix="<?= $item['prix'] ?>">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['nom']) ?>" class="cart-item-img">

                        <div class="cart-item-info">
                            <h3 class="cart-item-name"><?= htmlspecialchars($item['nom']) ?></h3>
                            <p class="cart-item-unit-price"><?= number_format($item['prix'], 2, ',', ' ') ?> DA / unité</p>
                            <?php if ($item['quantite'] > $item['stock_disponible']): ?>
                                <span class="stock-warning">Stock limité à <?= $item['stock_disponible'] ?></span>
                            <?php endif; ?>
                            <a href="?supprimer=<?= $item['id'] ?>" class="remove-link">
                                <ion-icon name="trash-outline"></ion-icon> Supprimer
                            </a>
                        </div>

                        <div class="cart-item-qty">
                            <div class="qty-stepper" data-id="<?= $item['id'] ?>" data-stock="<?= $item['stock_disponible'] ?>">
                                <button type="button" class="qty-btn qty-minus" aria-label="Diminuer la quantité">−</button>
                                <input type="number" class="qty-input" value="<?= $item['quantite'] ?>"
                                       min="0" max="<?= $item['stock_disponible'] ?>"
                                       data-id="<?= $item['id'] ?>" inputmode="numeric">
                                <button type="button" class="qty-btn qty-plus" aria-label="Augmenter la quantité">+</button>
                            </div>
                        </div>

                        <div class="cart-item-total prix-unitaire" data-label="Prix unitaire" style="display:none;"><?= number_format($item['prix'], 2, ',', ' ') ?> DA</div>
                        <div class="cart-item-total total-ligne"><?= number_format($total_ligne, 2, ',', ' ') ?> DA</div>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside class="cart-summary-sidebar">
                <h2>Résumé de la commande</h2>
                <div class="cart-summary-row">
                    <span>Articles</span>
                    <span><?= array_sum(array_column($panier, 'quantite')) ?></span>
                </div>
                <div class="cart-summary-row cart-summary-total">
                    <span>Total général</span>
                    <span id="total-general"><?= number_format($total_general, 2, ',', ' ') ?> DA</span>
                </div>

                <a href="commande.php" class="btn-primary cart-checkout-btn">Passer commande</a>
                <a href="?vider=1" class="btn-empty" onclick="return confirm('Vider tout le panier ?')">Vider le panier</a>
                <a href="produits.php" class="cart-continue-link">← Continuer mes achats</a>
            </aside>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
