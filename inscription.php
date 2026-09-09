<?php
// PAGE D'INSCRIPTION
require_once 'includes/config.php';
session_start();

// Si déjà connecté, redirection vers l'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// TABLEAUX DES ERREURS ET VALEURS SAISIES

$erreurs = [];
$valeurs = [
    'nom'       => '',
    'prenom'    => '',
    'email'     => '',
    'telephone' => '',
    'wilaya'    => '',
    'adresse'   => ''
];


// LISTE COMPLÈTE DES 69 WILAYAS
$wilayas = [
    'Adrar', 'Chlef', 'Laghouat', 'Oum El Bouaghi', 'Batna', 'Béjaïa',
    'Biskra', 'Béchar', 'Blida', 'Bouira', 'Tamanrasset', 'Tébessa',
    'Tlemcen', 'Tiaret', 'Tizi Ouzou', 'Alger', 'Djelfa', 'Jijel',
    'Sétif', 'Saïda', 'Skikda', 'Sidi Bel Abbès', 'Annaba', 'Guelma',
    'Constantine', 'Médéa', 'Mostaganem', 'M’Sila', 'Mascara', 'Ouargla',
    'Oran', 'El Bayadh', 'Illizi', 'Bordj Bou Arreridj', 'Boumerdès',
    'El Tarf', 'Tindouf', 'Tissemsilt', 'El Oued', 'Khenchela', 'Souk Ahras',
    'Tipaza', 'Mila', 'Aïn Defla', 'Naâma', 'Aïn Témouchent', 'Ghardaïa',
    'Relizane', 'Timimoun', 'Bordj Badji Mokhtar', 'Ouled Djellal', 'Béni Abbès',
    'In Salah', 'In Guezzam', 'Touggourt', 'Djanet', 'El M’Ghair', 'El Meniaa',
    'Aflou', 'Barika', 'El Kantara', 'Bir El Ater', 'El Aricha',
    'Ksar Chellala', 'Aïn Oussera', 'Messaad', 'Ksar El Boukhari', 'Bou Saâda', 'El Abiodh Sidi Cheikh'
];

// TRAITEMENT DU FORMULAIRE D'INSCRIPTION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom'] ?? '');
    $prenom    = trim($_POST['prenom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $wilaya    = trim($_POST['wilaya'] ?? '');
    $adresse   = trim($_POST['adresse'] ?? '');
    $mdp       = $_POST['mot_de_passe'] ?? '';
    $mdp_conf  = $_POST['confirmation_mdp'] ?? '';

    // Sauvegarde des valeurs pour réaffichage
    $valeurs = compact('nom', 'prenom', 'email', 'telephone', 'wilaya', 'adresse');

    // VALIDATION DES CHAMPS

    if (empty($nom))    $erreurs[] = 'Le nom est obligatoire.';
    if (empty($prenom)) $erreurs[] = 'Le prénom est obligatoire.';
    
    if (empty($email)) {
        $erreurs[] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse email n'est pas valide.";
    }
    
    if (!empty($telephone) && !preg_match('/^[0-9]{10}$/', $telephone)) {
        $erreurs[] = 'Le téléphone doit contenir exactement 10 chiffres.';
    }
    
    if (empty($wilaya))    $erreurs[] = 'Veuillez sélectionner une wilaya.';
    if (empty($adresse))   $erreurs[] = 'L’adresse est obligatoire.';
    
    if (empty($mdp)) {
        $erreurs[] = 'Le mot de passe est obligatoire.';
    } elseif (strlen($mdp) < 6) {
        $erreurs[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    
    if ($mdp !== $mdp_conf) {
        $erreurs[] = 'Les mots de passe ne correspondent pas.';
    }

    
    // VÉRIFICATION SI L'EMAIL EXISTE DÉJÀ

    if (empty($erreurs)) {
        $requete = $connexion->prepare('SELECT id FROM utilisateurs WHERE email = :email LIMIT 1');
        $requete->execute([':email' => $email]);
        if ($requete->fetch()) {
            $erreurs[] = 'Cet email est déjà utilisé.';
        }
    }

    
    // INSERTION EN BASE + CONNEXION AUTOMATIQUE
    if (empty($erreurs)) {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, telephone, wilaya, adresse, role, date_inscription)
                VALUES (:prenom, :nom, :email, :mot_de_passe, :telephone, :wilaya, :adresse, "client", NOW())';
        $requete = $connexion->prepare($sql);
        $requete->execute([
            ':prenom'      => $prenom,
            ':nom'         => $nom,
            ':email'       => $email,
            ':mot_de_passe' => $mdp_hash,
            ':telephone'   => $telephone ?: null,
            ':wilaya'      => $wilaya,
            ':adresse'     => $adresse
        ]);
        
        $nouvelId = $connexion->lastInsertId();
        
        // Connexion automatique
        $_SESSION['user_id'] = $nouvelId;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['nom'] = $nom;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'client';
        
        header('Location: index.php');
        exit;
    }
}

include 'includes/header.php';
?>

<!-- PAGE D'INSCRIPTION (CARTE CENTRÉE) -->
 
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon"><ion-icon name="person-add-outline"></ion-icon></div>
            <h1>Créer un compte</h1>
            <p>Rejoignez APIS RAPIDO</p>
        </div>

        <!-- Affichage des erreurs -->
        <?php if (!empty($erreurs)) : ?>
            <div class="auth-error">
                <strong>Erreurs :</strong>
                <ul>
                    <?php foreach ($erreurs as $err) : ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'inscription -->
        <form method="POST" action="" class="auth-form">

            <!-- Prénom -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="person-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($valeurs['prenom']) ?>" required>
                </div>
            </div>

            <!-- Nom -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="person-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($valeurs['nom']) ?>" required>
                </div>
            </div>

            <!-- Email -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="mail-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($valeurs['email']) ?>" required>
                </div>
            </div>

            <!-- Téléphone (optionnel) -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="call-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="telephone">Téléphone (10 chiffres)</label>
                    <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($valeurs['telephone']) ?>" maxlength="10" pattern="[0-9]{10}">
                </div>
            </div>

            <!-- Wilaya -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="map-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="wilaya">Wilaya *</label>
                    <select id="wilaya" name="wilaya" required>
                        <option value="">-- Sélectionnez votre wilaya --</option>
                        <?php foreach ($wilayas as $w): ?>
                            <option value="<?= htmlspecialchars($w) ?>" <?= $valeurs['wilaya'] === $w ? 'selected' : '' ?>>
                                <?= htmlspecialchars($w) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Adresse -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="home-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="adresse">Adresse *</label>
                    <textarea id="adresse" name="adresse" rows="2" required><?= htmlspecialchars($valeurs['adresse']) ?></textarea>
                </div>
            </div>

            <!-- Mot de passe -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="lock-closed-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="mot_de_passe">Mot de passe * (min. 6 caractères)</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="6">
                </div>
            </div>

            <!-- Confirmation mot de passe -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="checkmark-done-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="confirmation_mdp">Confirmer le mot de passe *</label>
                    <input type="password" id="confirmation_mdp" name="confirmation_mdp" required>
                </div>
            </div>

            <button type="submit" class="auth-btn">S'inscrire</button>

            <div class="auth-footer">
                <p>Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
            </div>

        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>