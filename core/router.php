<?php

// On s'assure que ce fichier n'est pas appelé directement
// et que les constantes vitales sont définies.
if (!defined('ROOT_PATH') || !defined('CONTROLLER_PATH')) {
    exit('Accès direct non autorisé ou constantes non définies');
}

// --- LE ROUTEUR DYNAMIQUE ---

// 1. Récupérer et nettoyer l'URL
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Si le chemin commence par notre BASE_PATH, on le supprime
if (defined('BASE_PATH') && strpos($path, BASE_PATH) === 0) {
    $path = substr($path, strlen(BASE_PATH));
}

$path = trim($path, '/'); // Ex: "user/register" ou "room/view/123"

// 2. Découper l'URL en segments
$segments = $path ? explode('/', $path) : []; // Ex: ["user", "register"]

// 3. Identifier les parties de la route
$controllerSlug = $segments[0] ?? 'home';  // "user", "room", "booking", ou "home" par défaut
$methodName = $segments[1] ?? 'index';      // "register", "list", "index" par défaut
$param = $segments[2] ?? null;              // ID optionnel, ex: /room/view/123

// On récupère aussi la méthode HTTP (GET, POST, ...)
$method = $_SERVER['REQUEST_METHOD'];

Logger::log("[ROUTING]", "Contrôleur: $controllerSlug, Méthode: $methodName, Param: $param, HTTP: $method");

// 4. Gérer la route "par défaut" (racine)
if ($controllerSlug === 'home' && $methodName === 'index') {
    echo json_encode(["success" => true, 'message' => 'Bienvenue sur l\'API Booking Room']);
    exit(); // On s'arrête ici
}

// 5. Mapper le slug au nom de classe et au fichier
// Convention: URL "user" -> Fichier "user_controller.php" -> Classe "UserController"
$controllerClass = ucfirst($controllerSlug) . 'Controller';
$controllerFile = CONTROLLER_PATH . '/' . $controllerSlug . '_controller.php';

// 6. Vérifier si le fichier du contrôleur existe
if (!file_exists($controllerFile)) {
    http_response_code(404);
    Logger::log("WARN", "[404] Fichier contrôleur non trouvé: $controllerFile");
    echo json_encode(["success" => false, 'message' => "Route non trouvée (contrôleur '$controllerSlug' inexistant)."]);
    exit();
}

// 7. Charger le fichier, instancier le contrôleur et appeler la méthode

require_once $controllerFile; // Charger le fichier contrôleur spécifique

// Vérifier si la classe existe
if (!class_exists($controllerClass)) {
    throw new Exception("Classe $controllerClass non trouvée dans $controllerFile");
}

// Instancier le contrôleur
$controller = new $controllerClass(); // Ex: new UserController()

// Vérifier si la méthode (l'action) existe dans le contrôleur
if (!method_exists($controller, $methodName)) {
    http_response_code(404);
    Logger::log("WARN", "[404] Méthode non trouvée: $controllerClass->$methodName()");
    echo json_encode(["success" => false, 'message' => "Action non trouvée ('$methodName')."]);
    exit();
}

// 8. Appeler la méthode !
// On passe la méthode HTTP et le paramètre à la fonction
$controller->$methodName($method, $param); // Ex: $userController->register('POST', null)
