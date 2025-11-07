<?php

class UserModel extends BaseModel
{
    // Le type de $db est maintenant PDO
    private $id = null;
    public $username = null;
    public $email = null;
    public $firstname = null;
    public $lastname = null;


    public function __construct(?array $data = null)
    {
        parent::__construct();

        if ($data) {
            $this->id = $data['id'] ?? null;
            $this->username = $data['username'] ?? null;
            $this->email = $data['email'] ?? null;
            $this->firstname = $data['firstname'] ?? null;
            $this->lastname = $data['lastname'] ?? null;
        }
    }


    /**
     * Enregistre un nouvel utilisateur en base de données.
     *
     * Valide les données, vérifie les doublons (email/username),
     * hashe le mot de passe et insère l'utilisateur.
     *
     * @param array $data Un tableau associatif contenant [username, email, password, firstname, lastname]
     * @return array Un tableau formaté pour la réponse JSON [status, message, data?]
     */
    public function register(array $data): array
    {
        // 1. Accès sécurisé aux données
        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;

        // 2. Validation
        if (empty($username) || empty($password) || empty($email) || empty($firstname) || empty($lastname)) {
            return [
                "success" => false,
                "message" => "Veuillez remplir tous les champs."
            ];
        }

        // 3. Vérification de doublon
        if ($this->findUser($username, $email)) {
            return [
                "success" => false,
                "message" => "Cet utilisateur (email ou pseudo) existe déjà."
            ];
        }

        try {
            // 4. Hash
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 5. Insertion SÉCURISÉE
            $query = "INSERT INTO users (username, password_hash, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
            $params = [$username, $hashedPassword, $email, $firstname, $lastname];

            $newId = $this->insert($query, $params);

            // 6. vérification de succès
            if ($newId) {
                // Mettre à jour l'objet actuel
                $this->id = $newId;
                $this->username = $username;
                $this->email = $email;
                $this->firstname = $firstname;
                $this->lastname = $lastname;

                return [
                    "success" => true,
                    "message" => "Utilisateur enregistré avec succès.",
                    "data" => $this->getAllInfos()
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "L'insertion a échoué pour une raison inconnue."
                ];
            }

        } catch (PDOException $e) {
            Logger::log('ERROR', "Erreur DB register(): " . $e->getMessage());
            return [
                "success" => false,
                "message" => "Une erreur interne est survenue lors de l'enregistrement."
            ];
        }
    }

    // public function connect(string $username, string $password)
    // {

    // }

    // public function disconnect()
    // {

    // }

    // public function delete()
    // {

    // }

    // public function update($username, $email, $firstname, $lastname)
    // {

    // }


    /**
     * Vérifie si un utilisateur existe déjà avec un email OU un nom d'utilisateur donné.
     *
     * @param string $username Le nom d'utilisateur à rechercher.
     * @param string|null $email L'email à rechercher (peut être null).
     * @return bool True si l'utilisateur existe, false sinon (ou en cas d'erreur).
     */
    public function findUser(string $username, ?string $email)
    {
        try {
            $query = "select username, email from users where email = ? or username = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$email, $username]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            Logger::log('ERROR', "Erreur BDD (findUser): " . $e->getMessage());
            return false;
        }
    }


    /**
     * Renvoie un tableau associatif des informations de l'uitilisateur actuel
     * @return array{email: mixed, firstname: mixed, id: mixed, lastname: mixed, username: mixed}
     */
    public function getAllInfos()
    {
        return [
            "id" => $this->id,
            "username" => $this->username,
            "email" => $this->email,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname
        ];
    }


    /**
     * Récupère tous les utilisateurs de la base de données.
     *
     * @return array Un tableau contenant tous les utilisateurs.
     */
    public function get_users()
    {
        $query = "select * from users";
        return $this->fetchAll($query);
    }
}