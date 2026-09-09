<?php
/**
 * ============================================================
 * ADMIN - GESTION DES COMMANDES
 * ============================================================
 */
require_once '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

if (isset($_POST['changer_statut'])) {
    $commande_id = (int)$_POST['commande_id'];
    $nouveau_statut = $_POST['statut'];

    $sql = "UPDATE commandes SET statut = :statut WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([':statut' => $nouveau_statut, ':id' => $commande_id]);

    header('Location: gerer_commandes.php');
    exit;
}

include '../includes/admin_header.php';

if (isset($_GET['id'])) {
    $commande_id = (int)$_GET['id'];

    $sql = "SELECT c.*, u.prenom, u.nom, u.email, u.telephone, u.adresse, u.wilaya
            FROM commandes c
            JOIN utilisateurs u ON c.utilisateur_id = u.id
            WHERE c.id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([':id' => $commande_id]);
    $commande = $stmt->fetch();

    if (!$commande) {
        echo '<div class="admin-layout"><div class="admin-main"><div class="admin-panel">';
        echo '<h1>Commande introuvable</h1>';
        echo '<p><a href="gerer_commandes.php" class="admin-link-small">Retour à la liste</a></p>';
        echo '</div></div></div>';
        include '../includes/admin_footer.php';
        exit;
    }

    $sql_details = "SELECT d.*, p.nom as produit_nom, p.reference
                    FROM details_commandes d
                    JOIN produits p ON d.produit_id = p.id
                    WHERE d.commande_id = :id";
    $stmt = $connexion->prepare($sql_details);
    $stmt->execute([':id' => $commande_id]);
    $details = $stmt->fetchAll();
    ?>

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-title">Administration</div>
            <nav class="admin-sidebar-nav">
                <a href="index.php"><ion-icon name="grid-outline"></ion-icon> Tableau de bord</a>
                <a href="gerer_produits.php"><ion-icon name="cube-outline"></ion-icon> Produits</a>
                <a href="gerer_commandes.php" class="active"><ion-icon name="receipt-outline"></ion-icon> Commandes</a>
                <a href="gerer_utilisateurs.php"><ion-icon name="people-outline"></ion-icon> Utilisateurs</a>
                <a href="../index.php"><ion-icon name="storefront-outline"></ion-icon> Retour au site</a>
            </nav>
        </aside>

        <div class="admin-main">
            <div class="admin-topbar">
                <h1>Commande #<?= $commande['id'] ?></h1>
            </div>

            <div class="admin-panel">
                <div class="admin-order-summary">
                    <p><strong>Client :</strong> <?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($commande['email']) ?></p>
                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($commande['telephone'] ?? 'Non renseigné') ?></p>
                    <p><strong>Adresse de livraison :</strong> <?= nl2br(htmlspecialchars($commande['adresse_livraison'])) ?></p>
                    <p><strong>Wilaya :</strong> <?= htmlspecialchars($commande['wilaya']) ?></p>
                    <p><strong>Date :</strong> <?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></p>
                    <p><strong>Statut :</strong>
                        <span class="status-badge status-<?= $commande['statut'] ?>"><?= ucfirst(str_replace('_', ' ', $commande['statut'])) ?></span>
                    </p>
                    <p><strong>Total :</strong> <?= number_format($commande['total'], 2, ',', ' ') ?> DA</p>
                </div>
            </div>

            <div class="admin-panel">
                <div class="admin-panel-header">
                    <h2>Produits commandés</h2>
                </div>
                <?php if (empty($details)): ?>
                    <p class="admin-empty">Aucun détail trouvé pour cette commande.</p>
                <?php else: ?>
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $detail): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($detail['reference']) ?></td>
                                        <td><?= htmlspecialchars($detail['produit_nom']) ?></td>
                                        <td><?= $detail['quantite'] ?></td>
                                        <td><?= number_format($detail['prix_unitaire'], 2, ',', ' ') ?> DA</td>
                                        <td><?= number_format($detail['quantite'] * $detail['prix_unitaire'], 2, ',', ' ') ?> DA</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <p><a href="gerer_commandes.php" class="admin-link-small">← Retour à la liste des commandes</a></p>
            </div>
        </div>
    </div>

    <?php
    include '../includes/admin_footer.php';
    exit;
}

$commandes = $connexion->query("
    SELECT c.*, u.prenom, u.nom, u.email
    FROM commandes c
    JOIN utilisateurs u ON c.utilisateur_id = u.id
    ORDER BY c.date_commande DESC
")->fetchAll();
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">Administration</div>
        <nav class="admin-sidebar-nav">
            <a href="index.php"><ion-icon name="grid-outline"></ion-icon> Tableau de bord</a>
            <a href="gerer_produits.php"><ion-icon name="cube-outline"></ion-icon> Produits</a>
            <a href="gerer_commandes.php" class="active"><ion-icon name="receipt-outline"></ion-icon> Commandes</a>
            <a href="gerer_utilisateurs.php"><ion-icon name="people-outline"></ion-icon> Utilisateurs</a>
            <a href="../index.php"><ion-icon name="storefront-outline"></ion-icon> Retour au site</a>
        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1>Gestion des commandes</h1>
        </div>

        <div class="admin-panel">
            <?php if (empty($commandes)): ?>
                <p class="admin-empty">Aucune commande pour le moment.</p>
            <?php else: ?>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Détail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commandes as $commande): ?>
                            <tr>
                                <td>#<?= $commande['id'] ?></td>
                                <td><?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']) ?></td>
                                <td><?= htmlspecialchars($commande['email']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></td>
                                <td><?= number_format($commande['total'], 2, ',', ' ') ?> DA</td>
                                <td>
                                    <form method="POST" class="admin-status-form">
                                        <input type="hidden" name="commande_id" value="<?= $commande['id'] ?>">
                                        <select name="statut">
                                            <option value="en_attente" <?= $commande['statut'] == 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                            <option value="expediee" <?= $commande['statut'] == 'expediee' ? 'selected' : '' ?>>Expédiée</option>
                                            <option value="livree" <?= $commande['statut'] == 'livree' ? 'selected' : '' ?>>Livrée</option>
                                        </select>
                                        <button type="submit" name="changer_statut" class="btn-admin-small">Modifier</button>
                                    </form>
                                </td>
                                <td><a href="gerer_commandes.php?id=<?= $commande['id'] ?>" class="admin-link-small">Voir</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
