<?php 
// vue pour l'affichage des dossiers médicaux
require_once __DIR__ . '/../../../config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossier Médical Électronique</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css.css">
    <script>
        function filtrerDossiers() {
            var filtreNom = document.getElementById("filtre-nom").value.toLowerCase();
            var table = document.getElementById("table-dossiers");
            var rows = table.getElementsByTagName("tr");
            var totalAge = 0;
            var count = 0;

            for (var i = 1; i < rows.length; i++) {
                var nomCell = rows[i].getElementsByTagName("td")[1];
                var ageCell = rows[i].getElementsByTagName("td")[2];

                if (nomCell) {
                    var nom = nomCell.innerText;
                    if (nom.toLowerCase().indexOf(filtreNom) > -1) {
                        rows[i].style.display = "";
                        totalAge += parseInt(ageCell.innerText);
                        count++;
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }

            document.getElementById("total-dossiers").textContent = count;
            document.getElementById("age-moyen").textContent = (count > 0 ? (totalAge / count).toFixed(2) : 0);
        }
    </script>
    <style>
        .message {
            padding: 10px;
            margin-bottom: 20px;
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            border-radius: 4px;
        }
        .user-menu {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-menu a {
            text-decoration: none;
            padding: 5px 10px;
            color: #007bff;
        }
        .user-menu a:hover {
            text-decoration: underline;
        }
        .auth-required-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Dossiers Médicaux</h1>
        
        <!-- Menu utilisateur avec connexion/déconnexion -->
        <div class="user-menu">
            <?php if (isLoggedIn()): ?>
                <span>Connecté en tant que <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="<?= BASE_URL ?>/logout">Déconnexion</a>
            <?php else: ?>
                <span>Visiteur</span>
                <a href="<?= BASE_URL ?>/login">Connexion</a>
            <?php endif; ?>
        </div>
        
        <!-- Message de notification -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <?php unset($_SESSION['message']); // supprimer le message après affichage ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout de dossier -->
        <?php if (isLoggedIn()): ?>
            <form method="POST" action="<?= BASE_URL ?>/ajouter-dossier">
                <label for="numero-dossier">Numéro du dossier</label>
                <input type="number" name="numero-dossier" required>

                <label for="nom-client">Nom du client</label>
                <input type="text" name="nom-client" required>

                <label for="age-client">Age du client</label>
                <input type="number" name="age-client" required>

                <label for="date-diagnostic">Date du diagnostic</label>
                <input type="date" name="date-diagnostic" required>

                <label for="diagnostic">Diagnostic</label>
                <input type="text" name="diagnostic" required>

                <button type="submit" name="ajouter-dossier">Ajouter le dossier</button>
            </form>
        <?php else: ?>
            <div class="auth-required-message">
                Veuillez vous <a href="<?= BASE_URL ?>/login">connecter</a> pour ajouter ou modifier des dossiers.
            </div>
        <?php endif; ?>

        <!-- Filtrer par nom -->
        <input type="text" id="filtre-nom" placeholder="Filtrer par nom" oninput="filtrerDossiers()" value="<?= htmlspecialchars($filtreNom) ?>">

        <h2>Liste des Dossiers</h2>
        <table id="table-dossiers">
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Nom</th>
                    <th>Age</th>
                    <th>Diagnostic</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dossiers as $dossier): ?>
                    <tr>
                        <td><?= $dossier['numeroDossier'] ?></td>
                        <td><?= htmlspecialchars($dossier['nomClient']) ?></td>
                        <td><?= $dossier['ageClient'] ?></td>
                        <td>
                            <?php foreach ($dossier['diagnostics'] as $diagnostic): ?>
                                <?= $diagnostic['date'] ?>: <?= htmlspecialchars($diagnostic['texte']) ?><br>
                            <?php endforeach; ?>

                            <?php if (isLoggedIn()): ?>
                                <form method="POST" action="<?= BASE_URL ?>/ajouter-diagnostic" style="margin-top: 10px;">
                                    <label for="date">Date : </label>
                                    <input type="date" name="date-diagnostic" required class="ajout_date"><br>
                                    <label for="diagnostic">Diagnostic : </label>
                                    <input type="text" name="diagnostic" required class="ajout_diagnostic"><br>
                                    <input type="hidden" name="dossier_id" value="<?= $dossier['id'] ?>">
                                    <button type="submit" name="ajouter-diagnostic" id="ajouter">Ajouter un diagnostic</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isLoggedIn()): ?>
                                <a href="<?= BASE_URL ?>/supprimer-dossier/<?= $dossier['id'] ?>" onclick="return confirm('Supprimer ce dossier ?')">Supprimer</a>
                            <?php else: ?>
                                <span style="color: #6c757d;">Connectez-vous pour supprimer</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Statistiques</h3>
        <p>Total de dossiers : <span id="total-dossiers"><?= count($dossiers) ?></span></p>
        <p>
            Âge moyen des patients : <span id="age-moyen"><?= $ageMoyen ?></span>
        </p>
    </div>
</body>
</html>