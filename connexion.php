<?php
session_start();
require_once('config/connexion.php'); // ta connexion BDD

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
        $_SESSION['role'] = $user['role'];
        header('Location: compte.php');
        exit();
    } else {
        $message = "Email ou mot de passe incorrect.";
    }
}
?>

<?php include('includes/header.php'); ?>

<h2 class="text-center mb-4">Connexion</h2>
<form method="POST" class="mx-auto" style="max-width:400px;">
    <?php if (!empty($message)) : ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
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

<?php include('includes/footer.php'); ?>
