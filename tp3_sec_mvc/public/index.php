<?php
// diriger les requêtes vers les controleurs

// inclusion du fichier de configuration général
require_once __DIR__ . '/../config/config.php';

// inclusion des contrôleurs
require_once __DIR__ . '/../app/controllers/DossierController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// obtention de l'URI demandée
$requestUri = $_SERVER['REQUEST_URI'];

// accès direct à index.php
if ($requestUri === '/tp3_sec_mvc/public/index.php' || $requestUri === '/public/index.php') {
    $uri = '/';
} else {
    // suppression du chemin de base de l'URI
    $uri = str_replace(BASE_URL, '', $requestUri);
    // suppression des paramètres de l'URL pour ne garder que le chemin
    $uri = parse_url($uri, PHP_URL_PATH);
    // on la considère comme la racine
    if (empty($uri)) {
        $uri = '/';
    }
}

// création des instances de contrôleurs
$dossierController = new DossierController($db);
$authController = new AuthController($db);

// routage basé sur l'URI
switch ($uri) {
    // page d'accueil
    case '/':
        $dossierController->index();
        break;

    // traitement de la connexion
    case '/login':
        $authController->login();
        break;

    // traitement de la déconnexion
    case '/logout':
        $authController->logout();
        break;

    // traitement de l'ajout d'un dossier
    case '/ajouter-dossier':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dossierController->ajouterDossier();
        } else {
            // redirection vers la page d'accueil si pas POST
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        break;

    // traitement de l'ajout d'un diagnostic
    case '/ajouter-diagnostic':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dossierController->ajouterDiagnostic();
        } else {
            // redirection vers la page d'accueil si la méthode n'est pas POST
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        break;

    default:
        // vérification si la route correspond à la suppression d'un dossier
        if (preg_match('/^\/supprimer-dossier\/(\d+)$/', $uri, $matches)) {
            $id = $matches[1];
            $dossierController->supprimerDossier($id);
        } else {
            // page non trouvée
            header('HTTP/1.0 404 Not Found');
            echo '404 - Page non trouvée';
            echo '<br>URI demandée: ' . htmlspecialchars($uri);
            echo '<br>REQUEST_URI: ' . htmlspecialchars($requestUri);
        }
        break;
}
