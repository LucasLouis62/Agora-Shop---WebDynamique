<?php
session_start();
require_once('config/connexion.php');

// Traitement des critères de recherche
if (isset($_POST['sauvegarder_criteres']) && isset($_SESSION['id'])) {
    $utilisateur_id = $_SESSION['id'];
    $motcle = trim($_POST['motcle']);
    $prixmax = floatval($_POST['prixmax']);

    $stmt = $bdd->prepare("INSERT INTO criteres_recherche (utilisateur_id, motcle, prixmax)
                           VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE motcle = VALUES(motcle), prixmax = VALUES(prixmax)");
    $stmt->execute([$utilisateur_id, $motcle, $prixmax]);
    $message_criteres = "<div class='alert alert-success text-center mt-3'>Critères enregistrés avec succès !</div>";
} else {
    $message_criteres = '';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre compte – Agora Francia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
        }
        .btn-lg {
            padding: 12px 24px;
        }
    </style>
</head>
<body>
<div class="container my-4 p-4 bg-white shadow rounded">
    <header class="text-center mb-4">
        <img src="images/logo_agora.png" alt="Logo Agora Francia" width="200" class="img-fluid">
    </header>

    <nav class="navbar navbar-expand justify-content-center mb-4">
        <div class="navbar-nav gap-2">
            <a class="btn btn-primary" href="index.php">Accueil</a>
            <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
            <a class="btn btn-primary" href="notifications.php">Notifications</a>
            <a class="btn btn-primary" href="panier.php">Panier</a>
            <a class="btn btn-primary" href="<?= isset($_SESSION['id']) ? 'compte.php' : 'votrecompte.php' ?>">Votre compte</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="card shadow mx-auto p-5 text-center" style="max-width: 500px;">
            <h2 class="mb-4">Bienvenue sur Agora Francia</h2>
            <p>Accédez à votre compte pour suivre vos commandes, vos ventes ou modifier vos informations.</p>
            <div class="d-flex flex-column gap-3 mt-4">
                <a href="connexion.php" class="btn btn-outline-primary btn-lg">🔐 Se connecter</a>
                <a href="inscription.php" class="btn btn-success btn-lg">📝 Créer un compte</a>
            </div>
        </div>

        <?= $message_criteres ?>

        <!-- Bloc Critères de recherche -->
        <div class="card shadow mt-5 p-4">
            <h4 class="mb-3 text-center text-primary">🔎 Sauvegarder vos critères de recherche</h4>
            <form action="votrecompte.php" method="post">
                <div class="mb-3">
                    <label for="motcle" class="form-label">Mot-clé (ex : SUV, Tesla, électrique)</label>
                    <input type="text" name="motcle" id="motcle" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="prixmax" class="form-label">Prix maximum (€)</label>
                    <input type="number" name="prixmax" id="prixmax" class="form-control" min="1" step="1">
                </div>
                <button type="submit" name="sauvegarder_criteres" class="btn btn-outline-success w-100">
                    Enregistrer les critères
                </button>
            </form>
        </div>
    </div>

    <footer class="row text-center text-md-start align-items-center mt-5">
        <div class="col-md-4 mb-3 mb-md-0">
            <h5>Contact</h5>
            <p>Email : <a href="mailto:agora.francia@gmail.com">agora.francia@gmail.com</a></p>
            <p>Téléphone : 01 23 45 67 89</p>
            <p>Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <p>&copy; 2025 Agora Francia</p>
        </div>
        <div class="col-md-4">
            <h5>Nous trouver</h5>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.8878757609433!2d2.2847854156752096!3d48.850725779286154!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1685534176532!5m2!1sfr!2sfr" width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen loading="lazy"></iframe>
        </div>
    </footer>
</div>
</body>
</html>
