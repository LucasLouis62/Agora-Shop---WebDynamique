<?php
session_start();
require_once('config/connexion.php');

$utilisateur_id = $_SESSION['id'] ?? 1;

// Marquer comme lue
if (isset($_GET['marquer_lue'])) {
    $id = intval($_GET['marquer_lue']);
    $stmt = $bdd->prepare("UPDATE notifications SET lue = 1 WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$id, $utilisateur_id]);
    header("Location: notifications.php");
    exit();
}

// Supprimer
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $stmt = $bdd->prepare("DELETE FROM notifications WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$id, $utilisateur_id]);
    header("Location: notifications.php");
    exit();
}

// Récupérer les notifications enrichies
$stmt = $bdd->prepare("
    SELECT n.*, p.titre, p.image 
    FROM notifications n 
    LEFT JOIN produits p ON n.produit_id = p.id 
    WHERE n.utilisateur_id = ? 
    ORDER BY n.date DESC
");
$stmt->execute([$utilisateur_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agora Francia – Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4 p-4 bg-white border rounded shadow">
    <header class="text-center mb-4">
        <img src="images/logo_agora.png" alt="Logo Agora Francia" width="200" class="img-fluid">
    </header>

    <nav class="navbar navbar-expand justify-content-center mb-4">
        <div class="navbar-nav gap-2">
            <a class="btn btn-primary" href="index.php">Accueil</a>
            <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
            <a class="btn btn-primary" href="notifications.php">Notifications</a>
            <a class="btn btn-primary" href="panier.php">Panier</a>
            <a class="btn btn-primary" href="votrecompte.php">Votre compte</a>
        </div>
    </nav>

    <main>
        <h2 class="text-center mb-4">Vos Notifications</h2>

        <?php if (empty($notifications)): ?>
            <div class="alert alert-info text-center">Aucune notification pour le moment.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($notifications as $notif): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap <?= $notif['lue'] ? 'text-muted' : '' ?>">
                        <div class="d-flex align-items-center">
                            <?php if (!empty($notif['produit_id']) && !empty($notif['image'])): ?>
                                <a href="annonce.php?id=<?= $notif['produit_id'] ?>" class="me-3">
                                    <img src="<?= htmlspecialchars($notif['image']) ?>" alt="Image produit" width="60" height="40" class="rounded">
                                </a>
                            <?php endif; ?>
                            <div>
                                <?php if (!empty($notif['produit_id']) && !empty($notif['titre'])): ?>
                                    <a href="annonce.php?id=<?= $notif['produit_id'] ?>" class="text-decoration-none">
                                        <strong><?= htmlspecialchars($notif['titre']) ?></strong>
                                    </a><br>
                                <?php endif; ?>
                                <?= $notif['message'] ?>
                            </div>
                        </div>
                        <div class="mt-2 mt-md-0">
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

    <footer class="mt-5">
        <div class="row text-center text-md-start align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <h5>Contact</h5>
                <p>Email : <a href="mailto:agora.francia@gmail.com">agora.francia@gmail.com</a></p>
                <p>Téléphone : 01 23 45 67 89</p>
                <p>Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <p class="mb-0">&copy; 2025 Agora Francia</p>
            </div>
            <div class="col-md-4">
                <h5>Nous trouver</h5>
                <iframe src="https://www.google.com/maps/embed?pb=..." width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen loading="lazy"></iframe>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
