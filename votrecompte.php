<?php
// Démarrage de la session utilisateur
session_start();

// Inclusion de la connexion PDO à la base de données
require_once 'config/connexion.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agora Francia - Votre compte</title>
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
    <!-- Logo et header -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation principale -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Formulaire de connexion -->
    <div class="container my-5">
        <div class="card shadow mx-auto p-5 text-center" style="max-width: 500px;">
            <h2 class="mb-4">Bienvenue sur Agora Francia</h2>
            <p>Accédez à votre compte pour suivre vos commandes, vos ventes ou modifier vos informations.</p>
            <div class="d-flex flex-column gap-3 mt-4">
                <!-- Se connecte au compte via connexion.php -->
                <a href="connexion.php" class="btn btn-outline-primary btn-lg">🔐 Se connecter</a>
                <!-- Ouvre le formulaire de création de compte -->
                <a href="inscription.php" class="btn btn-success btn-lg">📝 Créer un compte</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
