<?php


// === DÉBUT DE LA CONFIGURATION DES LOGS ===
// Définir le chemin racine avant tout

// Configuration de la gestion des erreurs (LA BONNE FAÇON POUR UNE API)
error_reporting(E_ALL);         // Rapporter toutes les erreurs...
ini_set('display_errors', 0);   // ...mais ne JAMAIS les afficher au client.
ini_set('log_errors', 1);       // Activer l'écriture des erreurs dans un log.

// Définir le fichier de log
define('LOG_FILE', ROOT_PATH . '/logs/app.log');
ini_set('error_log', LOG_FILE); // Spécifier où PHP doit écrire.

// Logguer le début de la requête
error_log("--- [INFO] " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI'] . " ---");