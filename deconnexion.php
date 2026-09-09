<?php

// Démarrage de la session (pour pouvoir la détruire)
session_start();

// Destruction de toutes les données de session
session_destroy();

// Redirection vers la page d'accueil
header('Location: index.php');
exit;
?>