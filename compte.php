<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: connexion.php');
    exit();
}
?>

<?php include('includes/header.php'); ?>

<h2 class="text-center mb-4">Votre compte</h2>
<div class="card mx-auto mb-4" style="max-width: 400px;">
    <div class="card-body">
        <p><strong>Prénom :</strong> <?= htmlspecialchars($_SESSION['prenom']) ?></p>
        <p><strong>Rôle :</strong> <?= htmlspecialchars($_SESSION['role']) ?></p>
        <a href="deconnexion.php" class="btn btn-outline-danger w-100 mt-3">Se déconnecter</a>
    </div>
</div>

<?php include('includes/footer.php'); ?>
