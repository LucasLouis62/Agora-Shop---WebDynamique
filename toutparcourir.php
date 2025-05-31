<?php
// Démarrage de la session utilisateur
session_start();

// Inclusion de la connexion PDO à la base de données
require_once 'config/connexion.php';

// Fonction Barre de recherche de produits par mot-clé (titre)
function rechercherProduits($bdd, $motCle) {
    $stmt = $bdd->prepare("SELECT * FROM produits WHERE titre LIKE ?");
    $stmt->execute(['%' . $motCle . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les produits d'une catégorie donnée
function getProduitsParCategorie($bdd, $categorie) {
    $stmt = $bdd->prepare("SELECT * FROM produits WHERE Catégorie = ?");
    $stmt->execute([$categorie]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Liste des catégories disponibles
$categories = ['suv', 'berline', 'sportive'];
$produitsParCategorie = [];

// Récupère le mot-clé de recherche si présent
$recherche = $_GET['q'] ?? null;

// Si une recherche est effectuée, on affiche les résultats, sinon on affiche par catégorie
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
    <!-- Styles pour affichage des annonces -->
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
        .text-truncate {
            max-width: 210px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <!-- Logo et header -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation principale -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Formulaire de recherche -->
    <form method="get" action="toutparcourir.php" class="mb-5">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Rechercher un véhicule..." value="<?= htmlspecialchars($recherche ?? '') ?>">
            <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
        </div>
    </form>

    <?php if ($recherche): ?>
        <!-- Si recherche -> Affichage des résultats de recherche -->
        <h4 class="mb-4">Résultats pour « <?= htmlspecialchars($recherche) ?> »</h4>
        <?php if (empty($resultatsRecherche)): ?>
            <!-- Si recherche vide -> Affichage aucun résultat -->
            <p>Aucun résultat trouvé.</p>

        <?php else: ?>
            <!-- Affichage contour et boutons pour annonce si recherche effectué -->
            <div class="carousel-container mb-4">
                <?php foreach ($resultatsRecherche as $produit): ?>
                    <div class="card shadow-sm">
                        <a href="annonce.php?id=<?= $produit['id'] ?>">
                            <img src="<?= htmlspecialchars($produit['image']) ?>" class="card-img-top" alt="<?= $produit['titre'] ?>">
                        </a>
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate" style="max-width: 210px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; margin: 0 auto;">
                                <?= htmlspecialchars($produit['titre']) ?>
                            </h6>
                            <p class="small text-wrap" style="word-break:break-word; white-space:normal; max-width:220px; margin:0 auto;">
                                <?= htmlspecialchars($produit['description']) ?>
                            </p>
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
        <!-- Affichage des produits par catégorie -->
        <h2 class="text-center text-primary mb-5">Tout Parcourir</h2>
        <?php foreach ($produitsParCategorie as $categorie => $produits): ?>
            <!-- Affichage des différentes catégorie -->
            <h4 class="mb-3"><?= ucfirst($categorie) ?> 🚗 
                <a href="parcourir-<?= $categorie ?>.php" class="btn btn-sm btn-outline-secondary ms-2">Voir tous</a>
            </h4>
            <div class="carousel-container mb-5">
                <!-- Affichage des données de l'annonce -->
                <?php foreach ($produits as $produit): ?>
                    <div class="card shadow-sm">
                        <a href="annonce.php?id=<?= $produit['id'] ?>">
                            <img src="<?= htmlspecialchars($produit['image']) ?>" class="card-img-top" alt="<?= $produit['titre'] ?>">
                        </a>
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate" style="max-width: 210px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; margin: 0 auto;">
                                <?= htmlspecialchars($produit['titre']) ?>
                            </h6>
                            <p class="small text-wrap" style="word-break:break-word; white-space:normal; max-width:220px; margin:0 auto;">
                                <?= htmlspecialchars($produit['description']) ?>
                            </p>
                            <p class="text-muted"><?= number_format($produit['prix'], 0, ',', ' ') ?> €</p>
                            <div class="d-grid gap-1">
                                <a href="annonce.php?id=<?= $produit['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>
                                <!-- Si pas une enchère -> afficher boutons Ajouter au panier -->
                                <?php if ($produit['type_vente'] !== 'enchere'): ?>
                                    <a href="ajouter_au_panier.php?id=<?= $produit['id'] ?>" class="btn btn-outline-success btn-sm">Ajouter au panier</a>
                                <?php endif; ?>
                                <!-- Si Client/vendeur -> afficher boutons Faire une offre -->
                                <?php if ($produit['type_vente'] === 'negociation'): ?>
                                    <a href="negociation.php?id=<?= $produit['id'] ?>" class="btn btn-outline-warning btn-sm">Faire une offre</a>
                                <?php endif; ?>
                                <!-- Si enchère -> afficher boutons Enchérir -->
                                <?php if ($produit['type_vente'] === 'enchere'): ?>
                                    <!-- Calcul du temps restant pour l'enchère -->
                                    <?php
                                        // Calcul du temps restant pour l'enchère
                                        $temps_restant = '';
                                        if (!empty($produit['date_ajout'])) {
                                            $date_ajout = new DateTime($produit['date_ajout']);
                                            $date_fin = clone $date_ajout;
                                            $date_fin->modify('+72 hours');
                                            $now = new DateTime();
                                            if ($now < $date_fin) {
                                                $interval = $now->diff($date_fin);
                                                $temps_restant = $interval->format('%a jours %h h %i min %s s');
                                            } else {
                                                $temps_restant = 'Enchère terminée';
                                            }
                                        }
                                    ?>
                                    <a href="enchere.php?id=<?= $produit['id'] ?>" class="btn btn-outline-danger btn-sm">Enchérir</a>
                                    <!-- Affichage temps restant -->
                                    <p class="mt-2 mb-0 small text-secondary" style="white-space:normal; word-break:break-word;">Fermeture de l'enchère dans : <strong><?= $temps_restant ?></strong></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>