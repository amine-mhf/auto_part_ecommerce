<?php
// ============================================================
// ADMIN - TABLEAU DE BORD (DASHBOARD)
// ============================================================
require_once '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

include '../includes/admin_header.php';

// STATISTIQUES RÉELLES BASÉES SUR LA BASE DE DONNÉES

$totalProduits      = (int) $connexion->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$produitsRupture    = (int) $connexion->query("SELECT COUNT(*) FROM produits WHERE stock = 0")->fetchColumn();
$totalUtilisateurs  = (int) $connexion->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client'")->fetchColumn();
$totalAdmins        = (int) $connexion->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'admin'")->fetchColumn();
$totalCommandes     = (int) $connexion->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$commandesEnAttente = (int) $connexion->query("SELECT COUNT(*) FROM commandes WHERE statut = 'en_attente'")->fetchColumn();
$chiffreAffaires    = (float) $connexion->query("SELECT COALESCE(SUM(total), 0) FROM commandes")->fetchColumn();

$commandes = $connexion->query("
    SELECT c.*, u.prenom, u.nom, u.email
    FROM commandes c
    JOIN utilisateurs u ON c.utilisateur_id = u.id
    ORDER BY c.date_commande DESC
    LIMIT 8
")->fetchAll();
?>

<div class="admin-layout">

    <!-- BARRE LATÉRALE ADMIN -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">Administration</div>
        <nav class="admin-sidebar-nav">
            <a href="index.php" class="active"><ion-icon name="grid-outline"></ion-icon> Tableau de bord</a>
            <a href="gerer_produits.php"><ion-icon name="cube-outline"></ion-icon> Produits</a>
            <a href="gerer_commandes.php"><ion-icon name="receipt-outline"></ion-icon> Commandes</a>
            <a href="gerer_utilisateurs.php"><ion-icon name="people-outline"></ion-icon> Utilisateurs</a>
            <a href="../index.php"><ion-icon name="storefront-outline"></ion-icon> Retour au site</a>
        </nav>
    </aside>

    <!-- CONTENU PRINCIPAL -->
    <div class="admin-main">
        <div class="admin-topbar">
            <div>
                <h1>Tableau de bord</h1>
                <p class="admin-subtitle">Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?> — vue d'ensemble de votre boutique</p>
            </div>
        </div>

        <!-- CARTES STATISTIQUES -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue"><ion-icon name="cube-outline"></ion-icon></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $totalProduits ?></span>
                    <span class="stat-label">Produits au catalogue</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-red"><ion-icon name="alert-circle-outline"></ion-icon></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $produitsRupture ?></span>
                    <span class="stat-label">Produits en rupture</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-green"><ion-icon name="people-outline"></ion-icon></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $totalUtilisateurs ?></span>
                    <span class="stat-label">Clients inscrits</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-purple"><ion-icon name="receipt-outline"></ion-icon></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $totalCommandes ?></span>
                    <span class="stat-label">Commandes totales</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-orange"><ion-icon name="time-outline"></ion-icon></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $commandesEnAttente ?></span>
                    <span class="stat-label">Commandes en attente</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-blue"><ion-icon name="cash-outline"></ion-icon></div>
                <div class="stat-content">
                    <span class="stat-value"><?= number_format($chiffreAffaires, 0, ',', ' ') ?> DA</span>
                    <span class="stat-label">Chiffre d'affaires total</span>
                </div>
            </div>
        </div>

        <!-- RACCOURCIS DE GESTION -->
        <div class="admin-shortcuts">
            <a href="gerer_produits.php" class="shortcut-card">
                <ion-icon name="cube-outline"></ion-icon>
                <span>Gérer les produits</span>
            </a>
            <a href="gerer_commandes.php" class="shortcut-card">
                <ion-icon name="receipt-outline"></ion-icon>
                <span>Gérer les commandes</span>
            </a>
            <a href="gerer_utilisateurs.php" class="shortcut-card">
                <ion-icon name="people-outline"></ion-icon>
                <span>Gérer les utilisateurs</span>
            </a>
        </div>

        <!-- DERNIÈRES COMMANDES -->
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>Dernières commandes</h2>
                <a href="gerer_commandes.php" class="admin-link-small">Voir toutes les commandes</a>
            </div>

            <?php if (empty($commandes)): ?>
                <p class="admin-empty">Aucune commande pour le moment.</p>
            <?php else: ?>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Statut</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commandes as $commande): ?>
                                <tr>
                                    <td>#<?= $commande['id'] ?></td>
                                    <td><?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $commande['statut'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $commande['statut'])) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($commande['total'], 2, ',', ' ') ?> DA</td>
                                    <td><?= date('d/m/Y', strtotime($commande['date_commande'])) ?></td>
                                    <td><a href="gerer_commandes.php?id=<?= $commande['id'] ?>" class="admin-link-small">Voir détail</a></td>
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
