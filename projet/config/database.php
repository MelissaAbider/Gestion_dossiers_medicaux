<?php
// configuration de la connexion à la base de données
try {
    // définition des paramètres de connexion à la base de données
    $dsn = 'mysql:host=localhost;dbname=dossiers_medicaux;charset=utf8';
    $user = 'root';
    $password = '';
    
    // options pour configurer le comportement de PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // active le mode d'erreur pour obtenir des exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // définit le mode de récupération par défaut
    ];
    
    // création de l'instance PDO pour la connexion à la base de données
    $db = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    // en cas d'erreur, affiche un message et arrête le script
    die("erreur de connexion à la base de données : " . $e->getMessage());
}
?>