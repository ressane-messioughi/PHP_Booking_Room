<?php
// 1. Inclure le fichier de connexion
require 'config.php';

// 2. Configurer les Headers pour l'API
// Ceci est crucial pour permettre à React (qui s'exécute sur un autre port/domaine) d'accéder à ce script.
header('Access-Control-Allow-Origin: *'); // Permet l'accès depuis n'importe quel domaine (à sécuriser en production)
header('Content-Type: application/json'); // Indique que la réponse sera du JSON

// 3. Exécuter la requête SQL
try {
    // Exemple : Récupérer toutes les salles de réservation
    $stmt = $pdo->query("SELECT id, name, capacity FROM rooms ORDER BY name");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Renvoyer les données au format JSON
    echo json_encode($rooms);

} catch (Exception $e) {
    // Gérer les erreurs de requête
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des données: ' . $e->getMessage()]);
}

?>