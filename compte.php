<?php
session_start(); // Démarre la session

// Vérifie si l'utilisateur est déjà connecté
if (isset($_SESSION['id'])) {
    // L'utilisateur est connecté, vérifie son rôle
    if (isset($_SESSION['role'])) {
        // Redirige l'utilisateur vers la page appropriée en fonction de son rôle
        if ($_SESSION['role'] === 'acheteur') {
            header('Location: espace_acheteur.php');
            exit();
        } elseif ($_SESSION['role'] === 'vendeur') {
            header('Location: espace_vendeur.php');
            exit();
        } elseif ($_SESSION['role'] === 'admin') {
            header('Location: espace_admin.php');
            exit();
        }
    }
} else {
    // L'utilisateur n'est pas connecté, redirige vers la page de compte
    header('Location: votrecompte.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'accueil</title>
    <!-- Ajoutez ici vos liens CSS et autres métadonnées -->
</head>
<body>
    <!-- Contenu de votre page d'accueil -->
    <h1>Bienvenue sur notre site</h1>
    <p>Ceci est la page d'accueil. Vous serez redirigé automatiquement en fonction de votre rôle.</p>
</body>
</html>