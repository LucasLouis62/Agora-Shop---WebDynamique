<nav class="navbar navbar-expand justify-content-center mb-4">
    <div class="navbar-nav gap-2">
        <a class="btn btn-primary" href="index.php">Accueil</a>
        <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
        <a class="btn btn-primary" href="notifications.php">Notifications</a>
        <a class="btn btn-primary" href="panier.php">Panier</a>
        <?php if (isset($_SESSION['id'])) : ?>
            <a class="btn btn-success" href="compte.php">👤 Mon compte</a>
        <?php else : ?>
            <a class="btn btn-primary" href="votrecompte.php">Votre compte</a>
        <?php endif; ?>
    </div>
</nav>