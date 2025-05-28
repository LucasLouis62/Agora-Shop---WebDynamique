<?php
session_start();
require_once('config/connexion.php');

// Simulation utilisateur connecté (à remplacer par $_SESSION['id'] si en prod)
$utilisateur_id = $_SESSION['id'] ?? 1; // ici 1 par défaut si session absente

// Marquer une notif comme lue
if (isset($_GET['marquer_lue'])) {
    $id = intval($_GET['marquer_lue']);
    $stmt = $bdd->prepare("UPDATE notifications SET lue = 1 WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$id, $utilisateur_id]);
    header("Location: notifications.php");
    exit();
}

// Supprimer une notif
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $stmt = $bdd->prepare("DELETE FROM notifications WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$id, $utilisateur_id]);
    header("Location: notifications.php");
    exit();
}

// Récupération des notifications
$stmt = $bdd->prepare("SELECT * FROM notifications WHERE utilisateur_id = ? ORDER BY date DESC");
$stmt->execute([$utilisateur_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agora Francia – Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/prime.css">
</head>
<body>
    <div class="container my-4 p-4 border rounded shadow" style="background:#fff;">
        <!-- Logo -->
        <header class="text-center mb-4">
            <img src="images/logo_agora.png" alt="Logo Agora Francia" width="200" class="img-fluid">
        </header>

        <!-- Navigation -->
        <nav class="navbar navbar-expand justify-content-center mb-4">
            <div class="navbar-nav gap-2">
                <a class="btn btn-primary" href="index.html">Accueil</a>
                <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
                <a class="btn btn-primary" href="notifications.php">Notifications</a>
                <a class="btn btn-primary" href="panier.html">Panier</a>
                <a class="btn btn-primary" href="votrecompte.html">Votre compte</a>
            </div>
        </nav>

        <!-- Section Notifications -->
        <main>
            <h2 class="text-center mb-4">Vos Notifications</h2>
            <?php if (empty($notifications)): ?>
                <div class="alert alert-info text-center">Aucune notification pour le moment.</div>
            <?php else: ?>
                <div class="list-group mb-4">
                    <?php foreach ($notifications as $notif): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap <?= $notif['lue'] ? 'text-muted' : '' ?>">
                            <span><?= htmlspecialchars($notif['message']) ?></span>
                            <div>
                                <?php if (!$notif['lue']): ?>
                                    <a href="?marquer_lue=<?= $notif['id'] ?>" class="btn btn-outline-success btn-sm me-2">Marquer comme lue</a>
                                <?php endif; ?>
                                <a href="?supprimer=<?= $notif['id'] ?>" class="btn btn-outline-danger btn-sm">Supprimer</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <!-- Footer -->
        <footer class="mt-4">
            <div class="row text-center text-md-start align-items-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>Contact</h5>
                    <p class="mb-1">Email : <a href="mailto:agora.francia@gmail.com">agora.francia@gmail.com</a></p>
                    <p class="mb-1">Téléphone : 01 23 45 67 89</p>
                    <p class="mb-0">Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <p class="mb-0">&copy; 2025 Agora Francia</p>
                </div>
                <div class="col-md-4">
                    <h5>Nous trouver</h5>
                    <iframe src="https://www.google.com/maps/embed?pb=..." width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
