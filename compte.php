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
            background: linear-gradient(to right, #f4f9fc, #eaf4fb);
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .btn-custom {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
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
</body>
</html>
