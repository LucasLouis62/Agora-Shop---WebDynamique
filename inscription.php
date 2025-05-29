<?php
require_once 'config/connexion.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $bdd->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $message = "Cet email est déjà utilisé.";
    } else {
        $stmt = $bdd->prepare("INSERT INTO utilisateurs (nom, email, motdepasse, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $email, $motdepasse, $role]);
        $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription – Agora Francia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 500px;">
            <h2 class="text-center mb-4">Créer un compte</h2>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom complet</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="motdepasse" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Rôle</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="acheteur">Acheteur</option>
                        <option value="vendeur">Vendeur</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                <a href="index.php" class="btn btn-secondary w-100 mt-2">Retour au menu</a>
            </form>
        </div>
    </div>
</body>
</html>
