<?php
// PAGE PROFIL UTILISATEUR
require_once 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$erreur = '';


// RÉCUPÉRATION DES INFORMATIONS ACTUELLES
$requete = $connexion->prepare('SELECT prenom, nom, email, telephone, wilaya, adresse FROM utilisateurs WHERE id = :id');
$requete->execute([':id' => $user_id]);
$utilisateur = $requete->fetch();

if (!$utilisateur) {
    session_destroy();
    header('Location: connexion.php');
    exit;
}

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

// TRAITEMENT DU FORMULAIRE DE MODIFICATION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $wilaya = trim($_POST['wilaya'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $nouveau_mdp = $_POST['nouveau_mot_de_passe'] ?? '';
    $confirmer_mdp = $_POST['confirmer_mot_de_passe'] ?? '';

    // Validation des champs obligatoires

    if (empty($prenom) || empty($nom)) {
        $erreur = 'Le prénom et le nom sont obligatoires.';
    } elseif (!empty($telephone) && !preg_match('/^[0-9]{10}$/', $telephone)) {
        $erreur = 'Le téléphone doit contenir exactement 10 chiffres.';
    } elseif (empty($wilaya)) {
        $erreur = 'Veuillez sélectionner une wilaya.';
    } elseif (empty($adresse)) {
        $erreur = 'L’adresse est obligatoire.';
    }

    // Validation du mot de passe (si renseigné)

    if (empty($erreur) && !empty($nouveau_mdp)) {
        if (strlen($nouveau_mdp) < 6) {
            $erreur = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
        } elseif ($nouveau_mdp !== $confirmer_mdp) {
            $erreur = 'Les mots de passe ne correspondent pas.';
        }
    }

    // Mise à jour en base de données

    if (empty($erreur)) {
        $sql = 'UPDATE utilisateurs SET prenom = :prenom, nom = :nom, telephone = :telephone, wilaya = :wilaya, adresse = :adresse';
        $params = [
            ':prenom'    => $prenom,
            ':nom'       => $nom,
            ':telephone' => $telephone ?: null,
            ':wilaya'    => $wilaya,
            ':adresse'   => $adresse,
            ':id'        => $user_id
        ];

        // Ajout de la modification du mot de passe si nécessaire

        if (!empty($nouveau_mdp)) {
            $mdp_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $sql .= ', mot_de_passe = :mot_de_passe';
            $params[':mot_de_passe'] = $mdp_hash;
        }

        $sql .= ' WHERE id = :id';
        $requete = $connexion->prepare($sql);
        $requete->execute($params);

        // Mise à jour de la session

        $_SESSION['prenom'] = $prenom;
        $_SESSION['nom'] = $nom;

        $message = 'Votre profil a été mis à jour avec succès.';

        // Rechargement des données affichées

        $utilisateur = [
            'prenom'    => $prenom,
            'nom'       => $nom,
            'email'     => $utilisateur['email'],
            'telephone' => $telephone,
            'wilaya'    => $wilaya,
            'adresse'   => $adresse
        ];
    }
}

include 'includes/header.php';
?>

   <!--  PAGE PROFIL  -->

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon"><ion-icon name="person-circle-outline"></ion-icon></div>
            <h1>Mon profil</h1>
            <p>Bonjour <?= htmlspecialchars($_SESSION['prenom']) ?> <?= htmlspecialchars($_SESSION['nom']) ?></p>
        </div>

        <!-- Message de succès -->
        <?php if (!empty($message)): ?>
            <div class="alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Message d'erreur -->
        <?php if (!empty($erreur)): ?>
            <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <!-- Formulaire de modification du profil -->
        <form method="POST" action="" class="auth-form">

            <!-- Prénom -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="person-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" required>
                </div>
            </div>

            <!-- Nom -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="person-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
                </div>
            </div>

            <!-- Email (non modifiable) -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="mail-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="<?= htmlspecialchars($utilisateur['email']) ?>" disabled>
                </div>
            </div>

            <!-- Téléphone (optionnel) -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="call-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="telephone">Téléphone (10 chiffres)</label>
                    <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($utilisateur['telephone'] ?? '') ?>" maxlength="10">
                </div>
            </div>

            <!-- Wilaya -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="map-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="wilaya">Wilaya *</label>
                    <select id="wilaya" name="wilaya" required>
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach ($wilayas as $w): ?>
                            <option value="<?= htmlspecialchars($w) ?>" <?= ($utilisateur['wilaya'] === $w) ? 'selected' : '' ?>>
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
                    <textarea id="adresse" name="adresse" rows="2" required><?= htmlspecialchars($utilisateur['adresse']) ?></textarea>
                </div>
            </div>

            <!-- Nouveau mot de passe (optionnel) -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="lock-closed-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="nouveau_mot_de_passe">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" minlength="6">
                </div>
            </div>

            <!-- Confirmation nouveau mot de passe -->
            <div class="input-group">
                <div class="input-icon"><ion-icon name="checkmark-done-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="confirmer_mot_de_passe">Confirmer</label>
                    <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe">
                </div>
            </div>

            <button type="submit" class="auth-btn">Mettre à jour</button>

            <div class="auth-footer">
                <a href="deconnexion.php">Se déconnecter</a>
            </div>

        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>