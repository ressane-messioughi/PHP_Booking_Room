<?php

abstract class BaseModel
{
    /**
     * L'instance de connexion PDO partagée
     * @var PDO
     */
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    /**
     * Exécute un SELECT et retourne TOUTES les lignes.
     *
     * @param string $sql La requête SQL à exécuter.
     * @param array $params Les paramètres pour la requête préparée.
     * @return array Un tableau de résultats (vide si erreur ou aucun résultat).
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::log('ERROR', "Erreur BDD (fetchAll): {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Exécute un SELECT et retourne UNE SEULE ligne.
     *
     * @param string $sql La requête SQL à exécuter.
     * @param array $params Les paramètres pour la requête préparée.
     * @return array|null Un tableau associatif, ou null si rien n'est trouvé.
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (PDOException $e) {
            Logger::log('ERROR', "Erreur BDD (fetch): {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Exécute une requête INSERT et retourne le nouvel ID.
     *
     * @param string $sql La requête SQL d'insertion.
     * @param array $params Les paramètres pour la requête préparée.
     * @return string|false Le dernier ID inséré, ou 'false' si l'insertion a échoué.
     */
    public function insert(string $sql, array $params = []): string|false
    {
        try {
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);

            if ($success && $stmt->rowCount() > 0) {
                return $this->db->lastInsertId();
            } else {
                Logger::log('WARN', "Requête INSERT exécutée mais 0 ligne affectée. SQL: $sql");
                return false;
            }
        } catch (PDOException $e) {
            Logger::log('ERROR', "Erreur BDD (insert): {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Exécute une requête (UPDATE, DELETE) et retourne si elle a réussi.
     *
     * @param string $sql La requête SQL.
     * @param array $params Les paramètres.
     * @return bool True si succès, false si échec.
     */
    public function execute(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params); 
        } catch (PDOException $e) {
            Logger::log('ERROR', "Erreur BDD (execute): {$e->getMessage()}");
            return false; 
        }
    }
}