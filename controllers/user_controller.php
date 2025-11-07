<?php

// On s'assure que ce fichier n'est pas appelé directement
if (!defined('ROOT_PATH')) {
    exit('Accès direct non autorisé');
}

class UserController
{
    /**
     * Gère la requête GET /user
     *
     * @param string $method La méthode HTTP (ex: 'GET')
     * @param array|null $param Le paramètre d'URL (ex: un ID)
     */
    public function index($method, $param) // <-- LA CORRECTION
    {
        // Optionnel : Vous pouvez vérifier que c'est bien un GET
        if ($method !== 'GET') {
            http_response_code(405);
            Logger::log('WARN', "[405] Tentative de $method sur /user (GET attendu)");
            echo json_encode(["success" => false, 'message' => 'Méthode non autorisée.']);
            return;
        }

        $userModel = new UserModel();
        $users = $userModel->get_users();

        http_response_code(200);
        // On formate la réponse ici, dans le contrôleur
        echo json_encode([
            "success" => true,
            "data" => $users
        ]);
    }

    public function register($method, $param)
    {
        // 1. Vérifier la méthode HTTP
        if ($method !== 'POST') {
            http_response_code(405); // Method Not Allowed
            error_log("[405] Tentative de $method sur /user/register");
            echo json_encode(["success" => false, 'message' => 'Méthode non autorisée. Utilisez POST.']);
            return;
        }

        // 2. Récupérer et décoder les données JSON
        $data = json_decode(file_get_contents('php://input'), true);

        // 3. Logger les données reçues (pour le débogage)
        error_log("[USER_REGISTER] Données JSON reçues: " . print_r($data, true));

        // 4. Valider les données
        $errors = isEmptyFields($data);

        if (count($errors) > 0) {
            http_response_code(400); // 400 Bad Request
            echo json_encode([
                "success" => false,
                'message' => 'Données manquantes : ' . join(', ', $errors),
                'data' => $data
            ]);

            return;
        }

        // 5. Appeler le modèle pour créer l'utilisateur
        $user_model = new UserModel();
        $result = $user_model->register($data);

        // 6. Renvoyer une réponse de succès
        http_response_code(201); // 201 Created
        echo json_encode([...$result, "data" => $data]);
    }
}