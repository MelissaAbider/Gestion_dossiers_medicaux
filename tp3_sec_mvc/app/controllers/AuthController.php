<?php
// contrôleur pour la gestion de l'authentification
require_once __DIR__ . '/../../config/config.php';
require_once MODELS_PATH . 'UserModel.php';

class AuthController
{
    private $userModel;

    // constructeur qui initialise le modele utilisateur
    public function __construct($db)
    {
        $this->userModel = new UserModel($db);
    }

    // méthode pour traiter la demande de connexion
    public function login()
    {
        $error = '';

        // si connecté redirection vers la page d'accueil
        if (isLoggedIn()) {
            header("Location: " . BASE_URL . "/");
            exit();
        }

        // formulaire de connexion
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            // validation
            if (empty($username) || empty($password)) {
                $error = "veuillez entrer votre nom d'utilisateur et votre mot de passe.";
            } else {
                try {
                    // tentative d'authentification
                    $user = $this->userModel->authenticate($username, $password);

                    if ($user) {
                        // authentification réussie stockage des informations
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];

                        // redirection vers la page demandée 
                        $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : BASE_URL . '/';
                        unset($_SESSION['redirect_url']);

                        header("Location: $redirect");
                        exit();
                    } else {
                        $error = "nom d'utilisateur ou mot de passe incorrect.";
                    }
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        // chargement de la vue de connexion 
        loadView('auth/login', ['error' => $error]);
    }

    // Déconnecter 
    public function logout()
    {
        // supprime toutes les variables de session
        $_SESSION = [];

        // si un cookie de session est utilisé le détruire
        if (ini_get("session.use_cookies")) {

            // les paramètres du cookie de session actuel (chemin, domaine, sécurisé, httponly)
            $params = session_get_cookie_params();

            setcookie(
                session_name(),          // nom du cookie de session (ex: PHPSESSID)
                '',                      // valeur vide
                time() - 42000,          // expiration dans le passé 
                $params["path"],         // même chemin que le cookie original
                $params["domain"],       // même domaine que le cookie original
                $params["secure"],       // sécurisé (HTTPS uniquement si activé)
                $params["httponly"]      // accès uniquement par HTTP (non accessible en JavaScript)
            );
        }


        // détruit la session
        session_destroy();

        // redirection vers la page d'accueil
        header("Location: " . BASE_URL . "/");
        exit();
    }
}
