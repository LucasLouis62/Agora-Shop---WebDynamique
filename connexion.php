<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// Inclusion du fichier de connexion à la base de données (PDO)
require_once 'config/connexion.php';

// Initialisation du message de retour pour l'utilisateur
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $email = $_POST['email'];
    $mdp = $_POST['motdepasse'];

    // Préparation et exécution de la requête pour trouver l'utilisateur par email
    $stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe et de l'existence de l'utilisateur
    if ($user && password_verify($mdp, $user['motdepasse'])) {
        // Stockage des informations utilisateur en session
        $_SESSION['id'] = $user['id'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Redirection selon le rôle de l'utilisateur
        if ($user['role'] === 'acheteur') {
            header('Location: espace_acheteur.php'); // Redirection pour acheteur
        } elseif ($user['role'] === 'admin') {
            header('Location: espace_admin.php'); // Redirection pour administrateur
        } elseif ($user['role'] === 'vendeur') {
            header('Location: espace_vendeur.php'); // Redirection pour vendeur
        }
        exit();
    } else {
        // Message d'erreur si l'authentification échoue
        $message = "❌ Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion – Agora Francia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Logo et header -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation principale -->
    <?php include 'includes/navigation.php'; ?>

    <div class="container my-5">
        <div class="card shadow mx-auto p-4" style="max-width: 450px;">
            <h2 class="text-center mb-4">Se connecter</h2>
            <?php if (!empty($message)) : ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="motdepasse" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>
            <div class="text-center mt-3">
                <a href="votrecompte.php" class="text-decoration-none">⬅️ Retour</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
