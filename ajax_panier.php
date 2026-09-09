<?php


require_once 'includes/config.php';
session_start();

// INITIALISATION DE LA RÉPONSE JSON
$response = [
    'success' => false,
    'count'   => 0
];


// VÉRIFICATION CONNEXION + PRODUIT À AJOUTER
if (isset($_SESSION['user_id']) && isset($_GET['ajouter'])) {
    $user_id    = (int)$_SESSION['user_id'];
    $produit_id = (int)$_GET['ajouter'];
  
    // 1. VÉRIFICATION DU STOCK

    $stmt = $connexion->prepare("SELECT stock FROM produits WHERE id = ?");
    $stmt->execute([$produit_id]);
    $stock = (int)$stmt->fetchColumn();

    if ($stock <= 0) {
        // Stock insuffisant → échec
        $response['success'] = false;
    } else {
       
        // 2. VÉRIFICATION SI LE PRODUIT EST DÉJÀ DANS LE PANIER

        $stmt = $connexion->prepare("SELECT quantite FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
        $stmt->execute([$user_id, $produit_id]);
        $existe = $stmt->fetch();

        
        // 3. AJOUT OU MISE À JOUR DU PANIER
        if ($existe) {
            // Produit déjà présent → incrémenter la quantité
            $stmt = $connexion->prepare("UPDATE panier SET quantite = quantite + 1 WHERE utilisateur_id = ? AND produit_id = ?");
            $stmt->execute([$user_id, $produit_id]);
        } else {
            // Nouveau produit → insérer avec quantité 1
            $stmt = $connexion->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite) VALUES (?, ?, 1)");
            $stmt->execute([$user_id, $produit_id]);
        }

        // 4. RÉCUPÉRATION DU NOUVEAU COMPTEUR TOTAL

        $stmt = $connexion->prepare("SELECT SUM(quantite) as total FROM panier WHERE utilisateur_id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $response['count']   = (int)($result['total'] ?? 0);
        $response['success'] = true;
    }
} else {
    // Utilisateur non connecté ou pas d'ID produit
    $response['success'] = false;
}

// ENVOI DE LA RÉPONSE JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;