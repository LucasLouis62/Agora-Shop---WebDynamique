<?php
// Inclusion du fichier de connexion à la base de données (PDO)
require_once 'config/connexion.php';

// Initialisation du message de retour pour l'utilisateur
$message = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et sécurisation des champs du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    // Hachage du mot de passe pour la sécurité
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT);
    // Le rôle est forcé à "acheteur" (pas de choix possible à l'inscription)
    $role = 'acheteur';

    // Vérification si l'email existe déjà dans la base
    $stmt = $bdd->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        // Si l'email existe déjà, message d'erreur
        $message = "Cet email est déjà utilisé.";
    } else {
        // Sinon, insertion du nouvel utilisateur
        $stmt = $bdd->prepare("INSERT INTO utilisateurs (nom, prenom, email, motdepasse, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $motdepasse, $role]);
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
    <!-- Logo et header -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation principale -->
    <?php include 'includes/navigation.php'; ?>

    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 500px;">
            <h2 class="text-center mb-4">Créer un compte</h2>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="motdepasse" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                <a href="index.php" class="btn btn-secondary w-100 mt-2">Retour au menu</a>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
