<?php
// PAGE COMMANDE / PAIEMENT (SIMULATION)
require_once 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$erreur = '';
$success = false;
$commande_id = null;

$sql = "SELECT p.id, p.nom, p.prix, pa.quantite, p.stock as stock_disponible
        FROM panier pa
        JOIN produits p ON pa.produit_id = p.id
        WHERE pa.utilisateur_id = ?";
$stmt = $connexion->prepare($sql);
$stmt->execute([$user_id]);
$panier = $stmt->fetchAll();

if (empty($panier)) {
    header('Location: panier.php');
    exit;
}

$total = 0;
foreach ($panier as $item) {
    $total += $item['prix'] * $item['quantite'];
}

$wilayas = [
    'Adrar', 'Chlef', 'Laghouat', 'Oum El Bouaghi', 'Batna', 'Béjaïa',
    'Biskra', 'Béchar', 'Blida', 'Bouira', 'Tamanrasset', 'Tébessa',
    'Tlemcen', 'Tiaret', 'Tizi Ouzou', 'Alger', 'Djelfa', 'Jijel',
    'Sétif', 'Saïda', 'Skikda', 'Sidi Bel Abbès', 'Annaba', 'Guelma',
    'Constantine', 'Médéa', 'Mostaganem', 'M’Sila', 'Mascara', 'Ouargla',
    'Oran', 'El Bayadh', 'Illizi', 'Bordj Bou Arreridj', 'Boumerdès',
    'El Tarf', 'Tindouf', 'Tissemsilt', 'El Oued', 'Khenchela', 'Souk Ahras',
    'Tipaza', 'Mila', 'Aïn Defla', 'Naâma', 'Aïn Témouchent', 'Ghardaïa',
    'Relizane', 'Timimoun', 'Bordj Badji Mokhtar', 'Ouled Djellal',
    'Béni Abbès', 'In Salah', 'In Guezzam', 'Touggourt', 'Djanet',
    'El M’Ghair', 'El Meniaa'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? 'carte';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $wilaya = trim($_POST['wilaya'] ?? '');
    $zip_code = trim($_POST['zip_code'] ?? '');
    
    $errors = [];
    
    if (empty($full_name)) $errors[] = "Nom complet requis";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email valide requis";
    if (empty($address)) $errors[] = "Adresse requise";
    if (empty($city)) $errors[] = "Commune requise";
    if (empty($wilaya)) $errors[] = "Wilaya requise";
    if (empty($zip_code)) $errors[] = "Code postal requis";
    
    if ($payment_method === 'carte') {
        $card_name = trim($_POST['card_name'] ?? '');
        $card_number = preg_replace('/\s/', '', trim($_POST['card_number'] ?? ''));
        $exp_month = trim($_POST['exp_month'] ?? '');
        $exp_year = trim($_POST['exp_year'] ?? '');
        $cvv = trim($_POST['cvv'] ?? '');
        
        if (empty($card_name)) $errors[] = "Nom sur la carte requis";
        if (empty($card_number)) $errors[] = "Numéro de carte requis";
        if (empty($exp_month)) $errors[] = "Mois d'expiration requis";
        if (empty($exp_year)) $errors[] = "Année d'expiration requise";
        if (empty($cvv)) $errors[] = "CVV requis";
    }
    
    if (empty($errors)) {
        $stock_ok = true;
        foreach ($panier as $item) {
            if ($item['stock_disponible'] < $item['quantite']) {
                $errors[] = "Stock insuffisant pour : " . htmlspecialchars($item['nom']);
                $stock_ok = false;
                break;
            }
        }
        
        if ($stock_ok) {
            $statut = 'en_attente';
            
            $sql = "INSERT INTO commandes (utilisateur_id, total, adresse_livraison, wilaya, statut) VALUES (?, ?, ?, ?, ?)";
            $stmt = $connexion->prepare($sql);
            $stmt->execute([$user_id, $total, $address, $wilaya, $statut]);
            $commande_id = $connexion->lastInsertId();
            
            $sql_detail = "INSERT INTO details_commandes (commande_id, produit_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)";
            $stmt_detail = $connexion->prepare($sql_detail);
            $sql_update = "UPDATE produits SET stock = stock - ? WHERE id = ?";
            $stmt_update = $connexion->prepare($sql_update);
            
            foreach ($panier as $item) {
                $stmt_detail->execute([$commande_id, $item['id'], $item['quantite'], $item['prix']]);
                $stmt_update->execute([$item['quantite'], $item['id']]);
            }
            
            $sql_delete = "DELETE FROM panier WHERE utilisateur_id = ?";
            $stmt_delete = $connexion->prepare($sql_delete);
            $stmt_delete->execute([$user_id]);
            
            $success = true;
        } else {
            $erreur = implode('<br>', $errors);
        }
    } else {
        $erreur = implode('<br>', $errors);
    }
}

include 'includes/header.php';
?>

<div class="checkout-page">
    <div class="container">
        <div class="checkout-container">
            
            <?php if ($success && $commande_id): ?>
                <div class="payment-success-overlay">
                    <div class="payment-success-modal">
                        <div class="payment-success-icon">
                            <ion-icon name="checkmark-circle"></ion-icon>
                        </div>
                        <h2>Paiement effectué</h2>
                        <p class="payment-success-text">Votre commande a bien été enregistrée. Un récapitulatif vous a été assigné.</p>

                        <div class="payment-success-details">
                            <div class="payment-success-row">
                                <span>Commande</span>
                                <strong>#<?= $commande_id ?></strong>
                            </div>
                            <div class="payment-success-row">
                                <span>Montant réglé</span>
                                <strong><?= number_format($total, 2, ',', ' ') ?> DA</strong>
                            </div>
                            <div class="payment-success-row">
                                <span>Mode de paiement</span>
                                <strong><?= $payment_method === 'especes' ? 'Espèces à la livraison' : 'Carte bancaire' ?></strong>
                            </div>
                        </div>

                        <div class="payment-success-buttons">
                            <a href="mes_commandes.php" class="btn-primary">Voir mes commandes</a>
                            <a href="index.php" class="btn-outline">Retour à l'accueil</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                
                <div class="checkout-header">
                    <h1>Finaliser ma commande</h1>
                    <p>Complétez vos informations pour finaliser votre commande</p>
                </div>

                <?php if ($erreur): ?>
                    <div class="alert-error"><?= $erreur ?></div>
                <?php endif; ?>

                <div class="cart-summary">
                    <h2>Récapitulatif de votre commande</h2>
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Qté</th>
                                <th>Prix unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($panier as $item): 
                                $sous_total = $item['prix'] * $item['quantite'];
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['nom']) ?></td>
                                    <td><?= $item['quantite'] ?></td>
                                    <td><?= number_format($item['prix'], 2, ',', ' ') ?> DA</td>
                                    <td><?= number_format($sous_total, 2, ',', ' ') ?> DA</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total-row">
                                <td colspan="3"><strong>Total général</strong></td>
                                <td><strong><?= number_format($total, 2, ',', ' ') ?> DA</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="payment-options-container">
                    <h3>Mode de paiement</h3>
                    <div class="payment-methods-row">
                        <label class="payment-method-card">
                            <input type="radio" name="payment_method_radio" value="carte" checked>
                            <span class="method-icon"><ion-icon name="card-outline"></ion-icon></span>
                            <div class="method-info">
                                <strong>Carte Edahabia</strong>
                                <small>Paiement sécurisé</small>
                            </div>
                        </label>
                        <label class="payment-method-card">
                            <input type="radio" name="payment_method_radio" value="especes">
                            <span class="method-icon"><ion-icon name="cash-outline"></ion-icon></span>
                            <div class="method-info">
                                <strong>Espèces à la livraison</strong>
                                <small>Payez à la réception</small>
                            </div>
                        </label>
                    </div>
                </div>

                <form method="POST" class="checkout-form" id="checkoutForm">
                    <input type="hidden" name="payment_method" id="payment_method_input" value="carte">
                    
                    <div class="form-parallel">
                        <div class="form-col">
                            <h3 class="section-title">Adresse de livraison</h3>
                            <div class="input-box">
                                <label>Nom complet *</label>
                                <input type="text" name="full_name" required>
                            </div>
                            <div class="input-box">
                                <label>Email *</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="input-box">
                                <label>Adresse *</label>
                                <input type="text" name="address" required>
                            </div>
                            <div class="flex-row">
                                <div class="input-box half">
                                    <label>Commune *</label>
                                    <input type="text" name="city" required>
                                </div>
                                <div class="input-box half">
                                    <label>Wilaya *</label>
                                    <select name="wilaya" required>
                                        <option value="">-- Sélectionnez --</option>
                                        <?php foreach ($wilayas as $w): ?>
                                            <option value="<?= $w ?>"><?= $w ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="input-box">
                                <label>Code postal *</label>
                                <input type="text" name="zip_code" required>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <h3 class="section-title">Paiement</h3>
                            <div id="cardFields">
                                <div class="input-box">
                                    <label>Nom sur la carte *</label>
                                    <input type="text" name="card_name" id="card_name" placeholder="Nom comme sur la carte">
                                </div>
                                <div class="input-box">
                                    <label>Numéro de carte *</label>
                                    <input type="text" name="card_number" id="card_number" maxlength="19" placeholder="XXXX XXXX XXXX XXXX">
                                </div>
                                <div class="flex-row">
                                    <div class="input-box">
                                        <label>Mois exp.</label>
                                        <select name="exp_month" id="exp_month">
                                            <option value="">MM</option>
                                            <?php for($m=1;$m<=12;$m++): ?>
                                                <option value="<?= sprintf('%02d',$m) ?>"><?= sprintf('%02d',$m) ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="input-box">
                                        <label>Année exp.</label>
                                        <select name="exp_year" id="exp_year">
                                            <option value="">AA</option>
                                            <?php for($y=date('Y');$y<=date('Y')+10;$y++): ?>
                                                <option value="<?= $y ?>"><?= $y ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="input-box">
                                        <label>CVV</label>
                                        <input type="text" name="cvv" id="cvv" maxlength="3" placeholder="XXX">
                                    </div>
                                </div>
                            </div>
                            <div id="especesMessage" class="especes-message" style="display:none;">
                                <span class="method-icon"><ion-icon name="cash-outline"></ion-icon></span>
                                <div>
                                    <h4>Paiement à la livraison</h4>
                                    <p>Vous payez en espèces à la réception.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit" id="submitBtn">Payer maintenant</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>