<?php
// Activation des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('config/connexion.php'); // Connexion à la BDD

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mdp = $_POST['motdepasse'];
    $role = $_POST['role']; // acheteur ou vendeur

    // Vérifie si l'email existe déjà
    $check = $bdd->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $message = "Un compte avec cet email existe déjà.";
    } else {
        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $insert = $bdd->prepare("INSERT INTO utilisateurs (prenom, nom, email, motdepasse, role) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$prenom, $nom, $email, $hash, $role]);

        $message = "Inscription réussie ! Vous pouvez vous connecter.";
    }
}
?>

<?php include('includes/header.php'); ?>

<h2 class="text-center mb-4">Créer un compte</h2>
<form method="POST" class="mx-auto" style="max-width:400px;">
    <?php if (!empty($message)) : ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <div class="mb-3">
        <label class="form-label">Prénom</label>
        <input type="text" name="prenom" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="nom" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input type="password" name="motdepasse" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Rôle</label>
        <select name="role" class="form-select" required>
            <option value="acheteur">Acheteur</option>
            <option value="vendeur">Vendeur</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success w-100">S'inscrire</button>
</form>

<?php include('includes/footer.php'); ?>
