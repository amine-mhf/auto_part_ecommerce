<?php
/**
 * ============================================================
 * ADMIN - GESTION DES PRODUITS (CRUD)
 * ============================================================
 */
require_once '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];

    $stmt = $connexion->prepare("DELETE FROM details_commandes WHERE produit_id = ?");
    $stmt->execute([$id]);

    $stmt = $connexion->prepare("DELETE FROM panier WHERE produit_id = ?");
    $stmt->execute([$id]);

    $stmt = $connexion->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: gerer_produits.php');
    exit;
}

if (isset($_POST['ajouter'])) {
    $image = trim($_POST['image'] ?? '');
    if ($image === '') {
        $image = 'ressources/images/placeholder.png';
    }

    $sql = "INSERT INTO produits (reference, nom, description, prix, stock, image, sous_categorie_id, fabricant_id, marque_vehicule_id)
            VALUES (:ref, :nom, :descr, :prix, :stock, :image, :scat, :fab, :marq)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':ref'   => $_POST['reference'],
        ':nom'   => $_POST['nom'],
        ':descr' => $_POST['description'],
        ':prix'  => $_POST['prix'],
        ':stock' => $_POST['stock'],
        ':image' => $image,
        ':scat'  => $_POST['sous_categorie_id'],
        ':fab'   => $_POST['fabricant_id'],
        ':marq'  => $_POST['marque_vehicule_id']
    ]);
    header('Location: gerer_produits.php');
    exit;
}

if (isset($_POST['modifier'])) {
    $sql = "UPDATE produits SET
                reference = :ref, nom = :nom, description = :descr,
                prix = :prix, stock = :stock, image = :image,
                sous_categorie_id = :scat, fabricant_id = :fab, marque_vehicule_id = :marq
            WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':ref'   => $_POST['reference'],
        ':nom'   => $_POST['nom'],
        ':descr' => $_POST['description'],
        ':prix'  => $_POST['prix'],
        ':stock' => $_POST['stock'],
        ':image' => $_POST['image'],
        ':scat'  => $_POST['sous_categorie_id'],
        ':fab'   => $_POST['fabricant_id'],
        ':marq'  => $_POST['marque_vehicule_id'],
        ':id'    => $_POST['id']
    ]);
    header('Location: gerer_produits.php');
    exit;
}

include '../includes/admin_header.php';

$sous_categories = $connexion->query("SELECT id, nom FROM sous_categories ORDER BY nom")->fetchAll();
$fabricants      = $connexion->query("SELECT id, nom FROM fabricants ORDER BY nom")->fetchAll();
$marques         = $connexion->query("SELECT id, nom FROM marques_vehicules ORDER BY nom")->fetchAll();

$produits = $connexion->query("
    SELECT p.*,
           sc.nom AS sous_categorie_nom,
           f.nom AS fabricant_nom,
           mv.nom AS marque_vehicule_nom
    FROM produits p
    JOIN sous_categories sc ON p.sous_categorie_id = sc.id
    JOIN fabricants f ON p.fabricant_id = f.id
    JOIN marques_vehicules mv ON p.marque_vehicule_id = mv.id
    ORDER BY p.id DESC
")->fetchAll();

$editMode = isset($_GET['modifier_id']);
$editProduit = null;
if ($editMode) {
    $stmt = $connexion->prepare("SELECT * FROM produits WHERE id = :id");
    $stmt->execute([':id' => $_GET['modifier_id']]);
    $editProduit = $stmt->fetch();
    $editMode = (bool) $editProduit;
}
?>

<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">Administration</div>
        <nav class="admin-sidebar-nav">
            <a href="index.php"><ion-icon name="grid-outline"></ion-icon> Tableau de bord</a>
            <a href="gerer_produits.php" class="active"><ion-icon name="cube-outline"></ion-icon> Produits</a>
            <a href="gerer_commandes.php"><ion-icon name="receipt-outline"></ion-icon> Commandes</a>
            <a href="gerer_utilisateurs.php"><ion-icon name="people-outline"></ion-icon> Utilisateurs</a>
            <a href="../index.php"><ion-icon name="storefront-outline"></ion-icon> Retour au site</a>
        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1>Gestion des produits</h1>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2><?= $editMode ? 'Modifier le produit' : 'Ajouter un produit' ?></h2>
            </div>

            <form method="POST" class="admin-form">
                <?php if ($editMode): ?>
                    <input type="hidden" name="id" value="<?= (int) $editProduit['id'] ?>">
                <?php endif; ?>

                <div class="admin-form-grid">
                    <div class="admin-form-field">
                        <label>Référence</label>
                        <input type="text" name="reference" value="<?= $editMode ? htmlspecialchars($editProduit['reference']) : '' ?>" required>
                    </div>

                    <div class="admin-form-field">
                        <label>Nom</label>
                        <input type="text" name="nom" value="<?= $editMode ? htmlspecialchars($editProduit['nom']) : '' ?>" required>
                    </div>

                    <div class="admin-form-field admin-form-field-full">
                        <label>Description</label>
                        <textarea name="description" rows="3"><?= $editMode ? htmlspecialchars($editProduit['description']) : '' ?></textarea>
                    </div>

                    <div class="admin-form-field">
                        <label>Prix (DA)</label>
                        <input type="number" step="0.01" name="prix" value="<?= $editMode ? $editProduit['prix'] : '' ?>" required>
                    </div>

                    <div class="admin-form-field">
                        <label>Stock</label>
                        <input type="number" name="stock" value="<?= $editMode ? $editProduit['stock'] : '' ?>" required>
                    </div>

                    <div class="admin-form-field admin-form-field-full">
                        <label>Chemin de l'image (dossier ressources/images/...)</label>
                        <input type="text" name="image" placeholder="ressources/images/FILTRE/exemple.png"
                               value="<?= $editMode ? htmlspecialchars($editProduit['image']) : '' ?>">
                        <?php if ($editMode && !empty($editProduit['image'])): ?>
                            <img src="/pieces_auto/<?= htmlspecialchars($editProduit['image']) ?>"
                                 alt="Aperçu" class="admin-image-preview">
                        <?php endif; ?>
                    </div>

                    <div class="admin-form-field">
                        <label>Sous-catégorie</label>
                        <select name="sous_categorie_id" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($sous_categories as $sc): ?>
                                <option value="<?= $sc['id'] ?>" <?= $editMode && $editProduit['sous_categorie_id'] == $sc['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sc['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="admin-form-field">
                        <label>Fabricant</label>
                        <select name="fabricant_id" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($fabricants as $fab): ?>
                                <option value="<?= $fab['id'] ?>" <?= $editMode && $editProduit['fabricant_id'] == $fab['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($fab['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="admin-form-field">
                        <label>Marque véhicule</label>
                        <select name="marque_vehicule_id" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($marques as $mv): ?>
                                <option value="<?= $mv['id'] ?>" <?= $editMode && $editProduit['marque_vehicule_id'] == $mv['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($mv['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <?php if ($editMode): ?>
                        <button type="submit" name="modifier" class="btn-admin-primary">Mettre à jour</button>
                        <a href="gerer_produits.php" class="btn-admin-outline">Annuler</a>
                    <?php else: ?>
                        <button type="submit" name="ajouter" class="btn-admin-primary">Ajouter le produit</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>Liste des produits (<?= count($produits) ?>)</h2>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>ID</th>
                            <th>Référence</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Sous-catégorie</th>
                            <th>Fabricant</th>
                            <th>Marque</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $p): ?>
                        <tr>
                            <td>
                                <img src="/pieces_auto/<?= htmlspecialchars($p['image']) ?>"
                                     alt="<?= htmlspecialchars($p['nom']) ?>" class="admin-product-thumb">
                            </td>
                            <td>#<?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['reference']) ?></td>
                            <td><?= htmlspecialchars($p['nom']) ?></td>
                            <td><?= number_format($p['prix'], 2, ',', ' ') ?> DA</td>
                            <td><?= $p['stock'] > 0 ? $p['stock'] : '<span class="status-badge status-en_attente">Rupture</span>' ?></td>
                            <td><?= htmlspecialchars($p['sous_categorie_nom']) ?></td>
                            <td><?= htmlspecialchars($p['fabricant_nom']) ?></td>
                            <td><?= htmlspecialchars($p['marque_vehicule_nom']) ?></td>
                            <td class="admin-actions-cell">
                                <a href="?modifier_id=<?= $p['id'] ?>" class="admin-link-small">Modifier</a>
                                <a href="?supprimer=<?= $p['id'] ?>" class="admin-link-danger" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
