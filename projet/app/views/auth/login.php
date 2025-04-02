<?php
require_once __DIR__ . '/../../../config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Dossier Médical Électronique</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container login-container">
        <h1>Connexion</h1>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            <a href="<?= BASE_URL ?>/">Retour à l'accueil</a>
        </p>
    </div>
</body>

</html>