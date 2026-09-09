<?php

// CONFIGURATION DE LA CONNEXION À LA BASE DE DONNÉES

$adresse_serveur = 'localhost:3306';
$nom_base        = 'pieces_auto';
$nom_utilisateur = 'root';
$mot_de_passe    = '';

// ÉTABLISSEMENT DE LA CONNEXION PDO

try {
    $connexion = new PDO(
        "mysql:host=$adresse_serveur;dbname=$nom_base;charset=utf8mb4",
        $nom_utilisateur,
        $mot_de_passe
    );

    // En cas d'erreur SQL, on lance une exception
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // On désactive l'émulation des requêtes préparées pour plus de sécurité
    $connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // Mode de récupération par défaut : tableau associatif
    $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si la connexion échoue, on arrête tout et on affiche l'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
