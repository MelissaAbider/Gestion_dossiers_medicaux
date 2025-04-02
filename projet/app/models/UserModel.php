<?php
// modèle pour la gestion des utilisateurs

class UserModel {
    private $db;
    
    // constructeur qui initialise la connexion à la base de données
    public function __construct($db) {
        $this->db = $db;
    }
    
    // fonction pour authentifier un utilisateur
    public function authenticate($username, $password) {
        try {
            // préparation de la requête pour vérifier les identifiants
            $stmt = $this->db->prepare("SELECT id, username, password FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            
            if ($user = $stmt->fetch()) {
                // vérification du mot de passe
                if (password_verify($password, $user['password'])) {
                    // authentification réussie
                    return $user;
                }
            }
            
            // échec de l'authentification
            return false;
        } catch (PDOException $e) {
            // gestion des erreurs
            throw new Exception("erreur lors de l'authentification : " . $e->getMessage());
        }
    }
}
?>