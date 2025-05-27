<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('config/connexion.php'); // Connexion BDD

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mdp = $_POST['motdepasse'];

    $stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mdp, $user['motdepasse'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php'); // Redirection vers page dynamique
        exit();
    } else {
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
</body>
</html>
