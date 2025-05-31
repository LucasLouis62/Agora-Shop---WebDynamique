<?php
// Démarrage de la session utilisateur
session_start();

// Inclusion de la connexion PDO à la base de données
require_once('config/connexion.php');

// Vérifier si l'utilisateur est connecté, sinon utiliser un ID par défaut
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

// Récupérer toutes les notifications
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
    <style>
        .notif-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 15px;
        }
        .notif-card {
            border-radius: 0.75rem;
            padding: 15px;
            background-color: #fdfdfd;
            transition: background 0.2s ease;
        }
        .notif-card:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <!-- Logo -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation -->
    <?php include 'includes/navigation.php'; ?>


    <!-- Notifications -->
    <main>
        <h2 class="text-center mb-4">🔔 Vos Notifications</h2>
        <!-- Si pas de notification -->
        <?php if (empty($notifications)): ?>
            <div class="alert alert-info text-center">Aucune notification pour le moment.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($notifications as $notif): ?>
                    <div class="list-group-item notif-card d-flex justify-content-between align-items-center flex-wrap <?= $notif['lue'] ? 'text-muted' : '' ?>">
                        <?php
                        // Vérifier si le message contient un produit
                        preg_match('/produit_id=(\d+)/', $notif['message'], $matches);
                        $produit_id = $matches[1] ?? null;

                        if ($produit_id) {
                            // Requête pour récupérer image et titre du produit
                            $stmtProduit = $bdd->prepare("SELECT titre, image FROM produits WHERE id = ?");
                            $stmtProduit->execute([$produit_id]);
                            $produit = $stmtProduit->fetch(PDO::FETCH_ASSOC);
                        }
                        ?>
                        
                        <!-- Affichage du contenu de la notification -->
                        <div class="d-flex align-items-center flex-grow-1">
                            <?php if (!empty($produit)): ?>
                                <!-- Si la notification concerne un produit, affiche un lien avec image et titre du produit -->
                                <a href="annonce.php?id=<?= $produit_id ?>" class="d-flex align-items-center text-decoration-none text-dark">
                                    <img src="<?= htmlspecialchars($produit['image']) ?>" alt="Produit" class="notif-img">
                                    <div>
                                        <strong><?= htmlspecialchars($produit['titre']) ?></strong><br>
                                        <?= htmlspecialchars(strip_tags($notif['message'])) ?>
                                    </div>
                                </a>
                            <?php else: ?>
                                <!-- Sinon, affiche simplement le message de la notification -->
                                <div>
                                    <?= htmlspecialchars(strip_tags($notif['message'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Affichage des boutons d'action (marquer comme lue, supprimer) -->
                        <div class="mt-2 mt-md-0">
                            <?php if (!$notif['lue']): ?>
                                <a href="?marquer_lue=<?= $notif['id'] ?>" class="btn btn-outline-success btn-sm me-1">✔</a>
                            <?php endif; ?>
                            <a href="?supprimer=<?= $notif['id'] ?>" class="btn btn-outline-danger btn-sm">🗑</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
