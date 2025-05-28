<?php
session_start();
require_once 'config/connexion.php';

function rechercherProduits($bdd, $motCle) {
    $stmt = $bdd->prepare("SELECT * FROM produits WHERE titre LIKE ?");
    $stmt->execute(['%' . $motCle . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProduitsParCategorie($bdd, $categorie) {
    $stmt = $bdd->prepare("SELECT * FROM produits WHERE Catégorie = ?");
    $stmt->execute([$categorie]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$categories = ['suv', 'berline', 'sportive'];
$produitsParCategorie = [];
$recherche = $_GET['q'] ?? null;

if ($recherche) {
    $resultatsRecherche = rechercherProduits($bdd, $recherche);
} else {
    foreach ($categories as $categorie) {
        $produitsParCategorie[$categorie] = getProduitsParCategorie($bdd, $categorie);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agora Francia – Tout Parcourir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .carousel-container {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 1rem;
        }
        .card {
            width: 250px;
            display: inline-block;
            margin-right: 1rem;
            vertical-align: top;
        }
        .card-img-top {
            height: 180px;
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

    <form method="get" action="toutparcourir.php" class="mb-5">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Rechercher un véhicule..." value="<?= htmlspecialchars($recherche ?? '') ?>">
            <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
        </div>
    </form>

    <?php if ($recherche): ?>
        <h4 class="mb-4">Résultats pour « <?= htmlspecialchars($recherche) ?> »</h4>
        <?php if (empty($resultatsRecherche)): ?>
            <p>Aucun résultat trouvé.</p>
        <?php else: ?>
            <div class="carousel-container mb-4">
                <?php foreach ($resultatsRecherche as $produit): ?>
                    <div class="card shadow-sm">
                        <a href="annonce.php?id=<?= $produit['id'] ?>">
                            <img src="<?= htmlspecialchars($produit['image']) ?>" class="card-img-top" alt="<?= $produit['titre'] ?>">
                        </a>
                        <div class="card-body text-center">
                            <h6 class="card-title"><?= htmlspecialchars($produit['titre']) ?></h6>
                            <p class="small"><?= htmlspecialchars($produit['description']) ?></p>
                            <p class="text-muted"><?= number_format($produit['prix'], 0, ',', ' ') ?> €</p>
                            <div class="d-grid gap-1">
                                <a href="annonce.php?id=<?= $produit['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>
                                <?php if ($produit['type_vente'] !== 'enchere'): ?>
                                    <a href="ajouter_au_panier.php?id=<?= $produit['id'] ?>" class="btn btn-outline-success btn-sm">Ajouter</a>
                                <?php endif; ?>
                                <?php if ($produit['type_vente'] === 'negociation'): ?>
                                    <a href="negociation.php?id=<?= $produit['id'] ?>" class="btn btn-outline-warning btn-sm">Négocier</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <h2 class="text-center text-primary mb-5">Tout Parcourir</h2>
        <?php foreach ($produitsParCategorie as $categorie => $produits): ?>
            <h4 class="mb-3"><?= ucfirst($categorie) ?> 🚗 
                <a href="parcourir-<?= $categorie ?>.php" class="btn btn-sm btn-outline-secondary ms-2">Voir tous</a>
            </h4>
            <div class="carousel-container mb-5">
                <?php foreach ($produits as $produit): ?>
                    <div class="card shadow-sm">
                        <a href="annonce.php?id=<?= $produit['id'] ?>">
                            <img src="<?= htmlspecialchars($produit['image']) ?>" class="card-img-top" alt="<?= $produit['titre'] ?>">
                        </a>
                        <div class="card-body text-center">
                            <h6 class="card-title"><?= htmlspecialchars($produit['titre']) ?></h6>
                            <p class="small"><?= htmlspecialchars($produit['description']) ?></p>
                            <p class="text-muted"><?= number_format($produit['prix'], 0, ',', ' ') ?> €</p>
                            <div class="d-grid gap-1">
                                <a href="annonce.php?id=<?= $produit['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>
                                <?php if ($produit['type_vente'] !== 'enchere'): ?>
                                    <a href="ajouter_au_panier.php?id=<?= $produit['id'] ?>" class="btn btn-outline-success btn-sm">Ajouter</a>
                                <?php endif; ?>
                                <?php if ($produit['type_vente'] === 'negociation'): ?>
                                    <a href="negociation.php?id=<?= $produit['id'] ?>" class="btn btn-outline-warning btn-sm">Négocier</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <footer class="row text-center text-md-start align-items-center mt-5">
        <div class="col-md-4 mb-3 mb-md-0">
            <h5>Contact</h5>
            <p>Email : <a href="mailto:agora.francia@gmail.com">agora.francia@gmail.com</a></p>
            <p>Téléphone : 01 23 45 67 89</p>
            <p>Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <p>&copy; 2025 Agora Francia</p>
        </div>
        <div class="col-md-4">
            <h5>Nous trouver</h5>
            <iframe src="https://www.google.com/maps/embed?pb=..." width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </footer>
</div>
</body>
</html>
