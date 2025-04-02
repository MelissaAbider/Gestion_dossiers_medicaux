<?php
// démarrage de la session 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// inclusion de la configuration de la BD
require_once __DIR__ . '/database.php';

// définition du chemin de base pour les URLs
define('BASE_URL', '/tp3_sec_mvc/public');

// définition des chemins de base de l'application
define('BASE_PATH', dirname(__DIR__) . '/');
define('APP_PATH', BASE_PATH . 'app/');
define('MODELS_PATH', APP_PATH . 'models/');
define('VIEWS_PATH', APP_PATH . 'views/');
define('CONTROLLERS_PATH', APP_PATH . 'controllers/');
define('PUBLIC_PATH', BASE_PATH . 'public/');

// fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn()
{
    // vérifie si une session est active et si l'utilisateur est connecté
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

// fonction pour rediriger les utilisateurs non authentifiés
function requireLogin()
{
    if (!isLoggedIn()) {
        // sauvegarde l'URL demandée pour rediriger après connexion
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: " . BASE_URL . "/login");
        exit();
    }
}

// fonction pour charger une vue
function loadView($view, $data = [])
{
    // extraction des données pour les rendre disponibles dans la vue
    extract($data);

    // inclusion de la vue
    require_once VIEWS_PATH . $view . '.php';
}
