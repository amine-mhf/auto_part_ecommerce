<?php
// PAGE DE CONNEXION
require_once 'includes/config.php';
session_start();

// Si déjà connecté, redirection vers l'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$erreur = '';

// TRAITEMENT DU FORMULAIRE

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // Validation des champs
    if (empty($email) || empty($mot_de_passe)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        // Recherche de l'utilisateur par email
        $requete = $connexion->prepare('SELECT id, prenom, nom, email, mot_de_passe, role FROM utilisateurs WHERE email = :email');
        $requete->execute([':email' => $email]);
        $utilisateur = $requete->fetch();

        // Vérification du mot de passe (hash)
        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            // Connexion réussie : création de la session
            $_SESSION['user_id'] = $utilisateur['id'];
            $_SESSION['prenom'] = $utilisateur['prenom'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['email'] = $utilisateur['email'];
            $_SESSION['role'] = $utilisateur['role'];

            // Redirection selon le rôle
            if ($utilisateur['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}

include 'includes/header.php';
?>

<!--  PAGE AUTHENTIFICATION (CARTE CENTRÉE)-->
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon"><ion-icon name="lock-closed-outline"></ion-icon></div>
            <h1>Se connecter</h1>
            <p>Ravi de vous revoir</p>
        </div>

        <!-- Affichage du message d'erreur -->
        <?php if (!empty($erreur)) : ?>
            <div class="auth-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST" action="" class="auth-form">
            <div class="input-group">
                <div class="input-icon"><ion-icon name="mail-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-icon"><ion-icon name="lock-closed-outline"></ion-icon></div>
                <div class="input-field">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
            </div>

            <button type="submit" class="auth-btn">Se connecter</button>

            <div class="auth-footer">
                <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>