<?php
// PAGE BOUTIQUE (CATALOGUE + FILTRES)
require_once 'includes/config.php';
session_start();


// RÉCUPÉRATION DES VALEURS DES FILTRES
$categorie_id      = $_GET['categorie'] ?? '';
$sous_categorie_id = $_GET['sous_categorie'] ?? '';
$fabricant_id      = $_GET['fabricant'] ?? '';
$marque_id         = $_GET['marque'] ?? '';
$recherche         = trim($_GET['recherche'] ?? '');


// CONSTRUCTION DE LA REQUÊTE SQL AVEC FILTRES

$sql = "SELECT p.*, c.nom as categorie_nom, sc.nom as sous_categorie_nom, f.nom as fabricant_nom, mv.nom as marque_vehicule_nom
        FROM produits p
        JOIN sous_categories sc ON p.sous_categorie_id = sc.id
        JOIN categories c ON sc.categorie_id = c.id
        JOIN fabricants f ON p.fabricant_id = f.id
        JOIN marques_vehicules mv ON p.marque_vehicule_id = mv.id
        WHERE 1=1";
$params = [];

if (!empty($categorie_id)) {
    $sql .= " AND c.id = :categorie";
    $params[':categorie'] = $categorie_id;
}
if (!empty($sous_categorie_id)) {
    $sql .= " AND p.sous_categorie_id = :sous_cat";
    $params[':sous_cat'] = $sous_categorie_id;
}
if (!empty($fabricant_id)) {
    $sql .= " AND p.fabricant_id = :fabricant";
    $params[':fabricant'] = $fabricant_id;
}
if (!empty($marque_id)) {
    $sql .= " AND p.marque_vehicule_id = :marque";
    $params[':marque'] = $marque_id;
}
if (!empty($recherche)) {
    $sql .= " AND (p.nom LIKE :search1 OR p.reference LIKE :search2)";
    $params[':search1'] = "%$recherche%";
    $params[':search2'] = "%$recherche%";
}

$sql .= " ORDER BY p.id DESC";
$stmt = $connexion->prepare($sql);
$stmt->execute($params);
$produits = $stmt->fetchAll();


// RÉCUPÉRATION DES LISTES POUR LES MENUS DÉROULANTS

$categories = $connexion->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll();
$fabricants = $connexion->query("SELECT id, nom FROM fabricants ORDER BY nom")->fetchAll();
$marques    = $connexion->query("SELECT id, nom FROM marques_vehicules ORDER BY nom")->fetchAll();
$sous_categories = $connexion->query("SELECT id, nom, categorie_id FROM sous_categories ORDER BY nom")->fetchAll();

include 'includes/header.php';
?>

<div class="products-container">
    <div class="container">
        <h1>Nos produits</h1>

        <!-- FORMULAIRE DE FILTRES -->

        <form method="GET" class="filters-form">
            <div class="filters-grid">

                <div class="filter-group">
                    <label>Catégorie</label>
                    <select name="categorie" id="categorieSelect">
                        <option value="">Toutes</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $categorie_id == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Sous-catégorie</label>
                    <select name="sous_categorie" id="sousCategorieSelect">
                        <option value="">Toutes</option>
                        <?php foreach ($sous_categories as $sc): ?>
                            <option value="<?= $sc['id'] ?>" data-cat="<?= $sc['categorie_id'] ?>" <?= $sous_categorie_id == $sc['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sc['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Fabricant</label>
                    <select name="fabricant">
                        <option value="">Tous</option>
                        <?php foreach ($fabricants as $fab): ?>
                            <option value="<?= $fab['id'] ?>" <?= $fabricant_id == $fab['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($fab['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Marque véhicule</label>
                    <select name="marque">
                        <option value="">Toutes</option>
                        <?php foreach ($marques as $mv): ?>
                            <option value="<?= $mv['id'] ?>" <?= $marque_id == $mv['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mv['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Recherche</label>
                    <input type="text" name="recherche" placeholder="Nom ou référence" value="<?= htmlspecialchars($recherche) ?>">
                </div>

                <div class="filter-group buttons-group">
                    <button type="submit" class="btn-filter">Filtrer</button>
                </div>

            </div>
        </form>

        <!-- AFFICHAGE DES PRODUITS -->

        <?php if (empty($produits)): ?>
            <div class="empty-results empty-state">
                <ion-icon name="search-outline"></ion-icon>
                <p>Aucun produit trouvé.</p>
                <a href="produits.php" class="btn">Voir tous les produits</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($produits as $p): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($p['nom']) ?></h3>
                        <p class="product-desc-short"><?= htmlspecialchars($p['description']) ?></p>
                        <p class="category"><?= htmlspecialchars($p['categorie_nom']) ?> &gt; <?= htmlspecialchars($p['sous_categorie_nom']) ?></p>
                        <p class="fabricant">Fabricant : <?= htmlspecialchars($p['fabricant_nom']) ?></p>
                        <p class="marque">Marque : <?= htmlspecialchars($p['marque_vehicule_nom']) ?></p>

                        <p class="price"><?= number_format($p['prix'], 2, ',', ' ') ?> DA</p>
                        <?php if ($p['stock'] > 0): ?>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn-add add-to-cart" data-id="<?= $p['id'] ?>">Ajouter au panier</button>
                                <a href="acheter_maintenant.php?id=<?= $p['id'] ?>" class="btn-buy-now">Acheter</a>
                            <?php else: ?>
                                <a href="connexion.php" class="btn-outline-small">Se connecter pour acheter</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="stock-rupture">Rupture de stock</p>
                            <button class="btn-disabled" disabled>Indisponible</button>
                        <?php endif; ?>

                        <a href="produit_detail.php?id=<?= $p['id'] ?>" class="btn-detail">Voir détail</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>