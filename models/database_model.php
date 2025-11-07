<?php

class DataBase {
    // 1. Le type de la propriété devient ?PDO
    private static ?PDO $connexion = null;

    /**
     * Retourne l'instance unique de la connexion PDO.
     *
     * @return PDO L'objet de connexion PDO.
     */
    // 2. Le type de retour devient PDO
    public static function getConnexion(): PDO {
        
        if (self::$connexion === null) {
            
            // 3. Le DSN (Data Source Name) est construit avec les constantes
            //    Le charset est défini ici, remplaçant set_charset()
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

            // 4. Options pour PDO
            $options = [
                // Remplace mysqli_report() pour la gestion des erreurs
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Récupère les résultats en tableaux associatifs par défaut
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
                // Assure une meilleure sécurité (désactive l'émulation)
                PDO::ATTR_EMULATE_PREPARES   => false, 
            ];

            try {
                // 5. Création de l'instance PDO
                self::$connexion = new PDO($dsn, DB_USER, DB_PASS, $options);

            } catch (PDOException $e) { // 6. L'exception devient PDOException
                // 7. Gérer l'échec de la connexion
                die("Erreur de connexion à la base de données (PDO) : " . $e->getMessage());
            }
        }

        return self::$connexion;
    }
}