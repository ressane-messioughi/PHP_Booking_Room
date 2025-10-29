<?php

// --- 1. Configuration initiale et Sécurité ---

// A. Définir les Headers CORS (TRÈS IMPORTANT pour React)
// Cela autorise votre frontend React à communiquer avec ce backend API.
header("Access-Control-Allow-Origin: *"); // Permet à tout domaine (votre React) d'accéder
header("Content-Type: application/json; charset=UTF-8"); // La réponse sera du JSON
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Autorise les méthodes HTTP
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

date_default_timezone_set('Europe/Paris');
// Démarrer la session
session_start();
// Charger la configuration
require_once '../config/database.php';
require_once '../config/path.php';

// Charger les fichiers utilitaires
require_once SERVICE_PATH . '/helpers.php';

// Charger les modèles
require_once MODEL_PATH . '/user_model.php';
require_once MODEL_PATH . '/booking_model.php';
require_once MODEL_PATH . '/room_model.php';

// Activer l'affichage des erreurs en développement
// À désactiver en production
error_reporting(E_ALL);
ini_set('display_errors', 1);

