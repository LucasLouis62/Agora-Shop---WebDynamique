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
            <a class="btn btn-primary" href="index.html">Accueil</a>
            <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
            <a class="btn btn-primary" href="notifications.php">Notifications</a>
            <a class="btn btn-primary" href="panier.php">Panier</a>
            <a class="btn btn-primary" href="<?= isset($_SESSION['id']) ? 'compte.php' : 'votrecompte.html' ?>">Votre compte</a>
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
            <iframe src="https://www.google.com/maps/embed?pb=..." width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </footer>
</div>
</body>
</html>
