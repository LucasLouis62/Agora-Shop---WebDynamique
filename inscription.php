<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('config/connexion.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mdp = $_POST['motdepasse'];
    $role = $_POST['role'];

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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h2>Formulaire d'inscription</h2>
    <?php if (!empty($message)) : ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Prénom :</label><br>
        <input type="text" name="prenom" required><br><br>

        <label>Nom :</label><br>
        <input type="text" name="nom" required><br><br>

        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="motdepasse" required><br><br>

        <label>Rôle :</label><br>
        <select name="role" required>
            <option value="acheteur">Acheteur</option>
            <option value="vendeur">Vendeur</option>
        </select><br><br>

        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
