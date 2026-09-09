<?php
/**
 * ============================================================
 * ADMIN_HEADER.PHP - EN-TÊTE DÉDIÉ À L'ADMINISTRATION
 * ============================================================
 * Totalement indépendant du header/footer du site client :
 * l'administrateur ne voit jamais la navigation client et le
 * dashboard en même temps. Pour parcourir la boutique, il utilise
 * le bouton "Visiter le site" ci-dessous, qui le fait sortir de
 * l'espace admin.
 * ============================================================
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - APIS RAPIDO</title>
    <link rel="stylesheet" href="/pieces_auto/ressources/css/admin.css?v=8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300..700&display=swap" rel="stylesheet">
</head>

<body class="admin-body">

    <header class="admin-topheader">
        <div class="admin-topheader-left">
            <img src="/pieces_auto/ressources/images/logo/logo.png" alt="APIS RAPIDO" class="admin-logo">
            <span class="admin-topheader-title">Espace administration</span>
        </div>

        <div class="admin-topheader-right">
            <a href="/pieces_auto/index.php" class="btn-visit-site">
                <ion-icon name="storefront-outline"></ion-icon> <span>Visiter le site</span>
            </a>
            <div class="admin-user-chip">
                <ion-icon name="person-circle-outline"></ion-icon>
                <span><?= htmlspecialchars($_SESSION['prenom'] ?? 'Admin') ?></span>
            </div>
            <a href="/pieces_auto/deconnexion.php" class="admin-logout-link" title="Déconnexion">
                <ion-icon name="log-out-outline"></ion-icon>
            </a>
        </div>
    </header>
