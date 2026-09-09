<?php
/**
 * ============================================================
 * ADMIN - GESTION DES UTILISATEURS
 * ============================================================
 * - Vérifie les droits administrateur
 * - Affiche la liste de tous les utilisateurs
 * - Permet de promouvoir un client en admin
 * - Permet de rétrograder un admin en client
 * - Empêche l'administrateur de se modifier lui-même
 * ============================================================
 */
require_once '../includes/config.php';
session_start();

// Vérification des droits administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

// TRAITEMENT DES ACTIONS (PROMOTION / RÉTROGRADATION)
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    // Empêcher l'administrateur de se modifier lui-même
    if ($id !== $_SESSION['user_id']) {
        if ($action === 'promote') {
            $sql = "UPDATE utilisateurs SET role = 'admin' WHERE id = :id";
            $stmt = $connexion->prepare($sql);
            $stmt->execute([':id' => $id]);
        } elseif ($action === 'demote') {
            $sql = "UPDATE utilisateurs SET role = 'client' WHERE id = :id";
            $stmt = $connexion->prepare($sql);
            $stmt->execute([':id' => $id]);
        }
    }
    header('Location: gerer_utilisateurs.php');
    exit;
}

include '../includes/admin_header.php';

$stmt = $connexion->query("SELECT id, prenom, nom, email, role, date_inscription FROM utilisateurs ORDER BY id DESC");
$utilisateurs = $stmt->fetchAll();
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">Administration</div>
        <nav class="admin-sidebar-nav">
            <a href="index.php"><ion-icon name="grid-outline"></ion-icon> Tableau de bord</a>
            <a href="gerer_produits.php"><ion-icon name="cube-outline"></ion-icon> Produits</a>
            <a href="gerer_commandes.php"><ion-icon name="receipt-outline"></ion-icon> Commandes</a>
            <a href="gerer_utilisateurs.php" class="active"><ion-icon name="people-outline"></ion-icon> Utilisateurs</a>
            <a href="../index.php"><ion-icon name="storefront-outline"></ion-icon> Retour au site</a>
        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1>Gestion des utilisateurs</h1>
        </div>

        <div class="admin-panel">
            <?php if (empty($utilisateurs)): ?>
                <p class="admin-empty">Aucun utilisateur trouvé.</p>
            <?php else: ?>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom complet</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Inscription</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $utilisateur): ?>
                            <tr>
                                <td>#<?= $utilisateur['id'] ?></td>
                                <td><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></td>
                                <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                                <td>
                                    <span class="badge-<?= $utilisateur['role'] === 'admin' ? 'admin' : 'client' ?>">
                                        <?= $utilisateur['role'] === 'admin' ? 'Administrateur' : 'Client' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($utilisateur['date_inscription'])) ?></td>
                                <td>
                                    <?php if ($utilisateur['id'] !== $_SESSION['user_id']): ?>
                                        <?php if ($utilisateur['role'] === 'admin'): ?>
                                            <a href="?id=<?= $utilisateur['id'] ?>&action=demote" class="btn-demote" onclick="return confirm('Rétrograder cet administrateur ?')">Rétrograder</a>
                                        <?php else: ?>
                                            <a href="?id=<?= $utilisateur['id'] ?>&action=promote" class="btn-promote" onclick="return confirm('Promouvoir ce client ?')">Promouvoir admin</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge-self">Vous-même</span>
                                    <?php endif; ?>
                                </td>
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
