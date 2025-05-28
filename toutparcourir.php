<?php
session_start();
require_once 'config/connexion.php';

function getProduitsParCategorie($bdd, $categorie) {
    $stmt = $bdd->prepare("SELECT * FROM produits WHERE Catégorie = ?");
    $stmt->execute([$categorie]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$categories = ['suv', 'berline', 'sportive'];
$produitsParCategorie = [];
foreach ($categories as $categorie) {
    $produitsParCategorie[$categorie] = getProduitsParCategorie($bdd, $categorie);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agora Francia – Tout Parcourir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .carousel-container {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 1rem;
        }
        .card {
            width: 250px;
            display: inline-block;
            margin-right: 1rem;
        }
        .card-img-top {
            height: 180px;
            width: 100%;
            object-fit: cover;
        }
        footer {
            border-top: 1px solid #ddd;
            padding-top: 1rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
<div class="container my-4 p-4 bg-white shadow rounded">
    <!-- Logo -->
    <header class="text-center mb-4">
        <img src="images/logo_agora.png" alt="Logo Agora Francia" width="200" class="img-fluid">
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand justify-content-center mb-4">
        <div class="navbar-nav gap-2">
            <a class="btn btn-primary" href="index.html">Accueil</a>
            <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
            <a class="btn btn-primary" href="notifications.html">Notifications</a>
            <a class="btn btn-primary" href="panier.html">Panier</a>
            <a class="btn btn-primary" href="<?= isset($_SESSION['id']) ? 'compte.php' : 'votrecompte.html' ?>">Votre compte</a>
        </div>
    </nav>

    <h2 class="text-center text-primary mb-5">Tout Parcourir</h2>

    <?php foreach ($produitsParCategorie as $categorie => $produits) : ?>
        <h4 class="mb-3"><?= ucfirst($categorie) ?> 🚗 
            <a href="parcourir-<?= $categorie ?>.php" class="btn btn-sm btn-outline-secondary ms-2">Voir tous</a>
        </h4>
        <div class="carousel-container mb-5">
            <?php foreach ($produits as $produit) : ?>
                <div class="card shadow-sm">
                    <a href="annonce.php?id=<?= $produit['id'] ?>">
                        <img src="<?= htmlspecialchars($produit['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($produit['titre']) ?>">
                    </a>
                    <div class="card-body text-center">
                        <h6 class="card-title"><?= htmlspecialchars($produit['titre']) ?></h6>
                        <p class="card-text small"><?= htmlspecialchars($produit['description']) ?></p>
                        <p class="text-muted mb-2"><?= number_format($produit['prix'], 0, ',', ' ') ?> €</p>
                        <a href="annonce.php?id=<?= $produit['id'] ?>" class="btn btn-outline-primary btn-sm">Voir l'annonce</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <!-- Footer -->
    <footer class="row text-center text-md-start align-items-center">
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
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5250.744877226254!2d2.2859626768664922!3d48.85110800121838!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1748293349769!5m2!1sfr!2sfr"
                    width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </footer>
</div>
</body>
</html>
