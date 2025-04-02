<?php
// paramètres de connexion à la base de données MySQL 
$host = 'localhost';
$user = 'root';
$password = '';

try {
    // connexion au serveur MySQL
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // création de la BD 
    $pdo->exec("CREATE DATABASE IF NOT EXISTS dossiers_medicaux");

    // utilisation de la BD
    $pdo->exec("USE dossiers_medicaux");

    // création de la table dossiers 
    $sql = "CREATE TABLE IF NOT EXISTS dossiers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        numeroDossier INT NOT NULL,
        nomClient VARCHAR(255) NOT NULL,
        ageClient INT NOT NULL
    )";

    $pdo->exec($sql);

    // création de la table diagnostics 
    $sql = "CREATE TABLE IF NOT EXISTS diagnostics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dossier_id INT NOT NULL,
        date DATE NOT NULL,
        texte TEXT NOT NULL,
        FOREIGN KEY (dossier_id) REFERENCES dossiers(id) ON DELETE CASCADE
    )";

    $pdo->exec($sql);

    // création de la table users 
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);

    // vérification si l'admin existe 
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    $userExists = (int)$stmt->fetchColumn();

    // si admin n'existe pas on crée avec mdp 
    if ($userExists === 0) {
        // utilisation de password_hash pour stocker le mdp 
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES ('admin', :password)");
        $stmt->execute([':password' => $hashedPassword]);
        echo "admin créé avec le mot de passe 'admin123'<br>";
    }

    echo "base de données et tables créées avec succees";

    // redirection vers la page principale après 3 secondes
    header("refresh:3;url=../public/index.php");
} catch (PDOException $e) {
    die("erreur d'initialisation de la base de données: " . $e->getMessage());
}
