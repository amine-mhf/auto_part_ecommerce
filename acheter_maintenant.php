<?php
/**
 * ============================================================
 * ACHETER_MAINTENANT.PHP - ACHAT DIRECT
 * ============================================================
 * Ajoute le produit au panier (comme "Ajouter au panier") puis
 * redirige immédiatement vers la page de commande, pour permettre
 * un achat rapide sans repasser par le panier.
 * ============================================================
 */

require_once 'includes/config.php';
session_start();

// Connexion obligatoire pour acheter
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id    = (int) $_SESSION['user_id'];
$produit_id = (int) ($_GET['id'] ?? 0);

if ($produit_id > 0) {
    $stmt = $connexion->prepare("SELECT stock FROM produits WHERE id = ?");
    $stmt->execute([$produit_id]);
    $stock = (int) $stmt->fetchColumn();

    if ($stock > 0) {
        $stmt = $connexion->prepare("SELECT quantite FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
        $stmt->execute([$user_id, $produit_id]);
        $existe = $stmt->fetch();

        if ($existe) {
            $stmt = $connexion->prepare("UPDATE panier SET quantite = quantite + 1 WHERE utilisateur_id = ? AND produit_id = ?");
            $stmt->execute([$user_id, $produit_id]);
        } else {
            $stmt = $connexion->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite) VALUES (?, ?, 1)");
            $stmt->execute([$user_id, $produit_id]);
        }
    }
}

// Redirection directe vers la commande
header('Location: commande.php');
exit;
