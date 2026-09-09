<?php
// PAGE HISTORIQUE DES COMMANDES DU CLIENT
require_once 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// RÉCUPÉRATION DE TOUTES LES COMMANDES DU CLIENT

$sql = "SELECT id, date_commande, total, statut, adresse_livraison, wilaya
        FROM commandes
        WHERE utilisateur_id = ?
        ORDER BY date_commande DESC";
$stmt = $connexion->prepare($sql);
$stmt->execute([$user_id]);
$commandes = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="orders-container">
    <h1>Mes commandes</h1>

    <?php if (empty($commandes)): ?>
        <!-- Aucune commande trouvée -->
        <div class="empty-orders">
            <ion-icon name="receipt-outline"></ion-icon>
            <p>Vous n'avez passé aucune commande pour le moment.</p>
            <a href="produits.php" class="btn">Découvrir nos pièces</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($commandes as $c): ?>
                <div class="order-card">
                    <!-- En-tête de la commande -->
                    <div class="order-header">
                        <span class="order-number">Commande #<?= $c['id'] ?></span>
                        <span class="order-date"><?= date('d/m/Y H:i', strtotime($c['date_commande'])) ?></span>
                    </div>

                    <!-- Récupération des produits commandés pour cette commande -->
                    <?php
                    $sql_produits = "SELECT d.quantite, d.prix_unitaire, p.nom, p.description
                                     FROM details_commandes d
                                     JOIN produits p ON d.produit_id = p.id
                                     WHERE d.commande_id = ?";
                    $stmt_prod = $connexion->prepare($sql_produits);
                    $stmt_prod->execute([$c['id']]);
                    $produits_commandes = $stmt_prod->fetchAll();
                    ?>

                    <!-- Corps de la commande -->
                    <div class="order-body">
                        <p><strong>Total :</strong> <?= number_format($c['total'], 2, ',', ' ') ?> DA</p>
                        <p><strong>Statut :</strong>
                            <span class="status-badge status-<?= $c['statut'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $c['statut'])) ?>
                            </span>
                        </p>
                        <p><strong>Livraison :</strong> <?= htmlspecialchars($c['adresse_livraison']) ?>, <?= htmlspecialchars($c['wilaya']) ?></p>

                        <!-- Liste des produits commandés -->
                        <?php if (!empty($produits_commandes)): ?>
                            <div class="order-products">
                                <p><strong>Produits commandés :</strong></p>
                                <ul>
                                    <?php foreach ($produits_commandes as $prod): ?>
                                        <li>
                                            <strong><?= htmlspecialchars($prod['nom']) ?></strong><br>
                                            <span class="product-desc-small"><?= htmlspecialchars($prod['description']) ?></span><br>
                                            <span>Quantité : <?= $prod['quantite'] ?> | Prix unitaire : <?= number_format($prod['prix_unitaire'], 2, ',', ' ') ?> DA</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>