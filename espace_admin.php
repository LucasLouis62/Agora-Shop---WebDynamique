<?php
session_start();

// Redirection si non connecté
if (!isset($_SESSION['id'])) {
    header('Location: votrecompte.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte | Agora Francia</title>
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

    <div class="container py-5">
        <div class="card mx-auto p-4" style="max-width: 500px;">
            <h2 class="text-center mb-4 text-primary">Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?> 👋</h2>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($_SESSION['prenom']) ?></li>
                <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($_SESSION['nom']) ?></li>
                <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($_SESSION['email']) ?></li>
                <li class="list-group-item"><strong>Rôle :</strong> <?= htmlspecialchars($_SESSION['role']) ?></li>
            </ul>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php" class="btn btn-outline-primary btn-custom">🏠 Retour à l'accueil</a>
                <a href="deconnexion.php" class="btn btn-danger btn-custom">🔓 Se déconnecter</a>
            </div>
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
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.8878757609433!2d2.2847854156752096!3d48.850725779286154!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1685534176532!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </footer>
</div>
</body>
</html>
