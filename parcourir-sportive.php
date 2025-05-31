<?php
// Démarrage de la session utilisateur
session_start();

// Connexion à la base de données 'agora'
$database = "agora";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);

// Tableau pour stocker les annonces filtrées
$annonces = [];
if ($db_found) {
    // Tri, filtres, plages de prix
    $tri = $_GET['tri'] ?? '';
    $valeur = $_GET['valeur'] ?? '';
    $prix_min = isset($_GET['prix_min']) ? intval($_GET['prix_min']) : 0;
    $prix_max = isset($_GET['prix_max']) ? intval($_GET['prix_max']) : 0;

    $orderBy = '';
    $whereClause = "WHERE Catégorie = 'sportive'";

    // Filtres
    if (!empty($valeur)) {
        $whereClause .= " AND type_vente = '" . mysqli_real_escape_string($db_handle, $valeur) . "'";
    }

    if ($prix_min > 0 && $prix_max > 0) {
        $whereClause .= " AND prix BETWEEN $prix_min AND $prix_max";
    }

    // Gestion du tri
    if ($tri === 'prix_croissant') {
        $orderBy = 'ORDER BY prix ASC';
    } elseif ($tri === 'prix_decroissant') {
        $orderBy = 'ORDER BY prix DESC';
    } elseif ($tri === 'date_publication') {
        $orderBy = 'ORDER BY date_ajout DESC';
    }

    $sql = "SELECT * FROM produits $whereClause $orderBy";
    $result = mysqli_query($db_handle, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $annonces[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agora Francia – Tout parcourir - Sportives</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="styles/prime.css">
</head>

<body>
    <!-- Logo -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation -->
    <?php include 'includes/navigation.php'; ?>

    <main class="text-center mb-4">
        <h2 class="mb-3">Sportives disponibles</h2>

        <div class="row justify-content-center">
            <!-- Bouton de tris -->
            <div class="d-flex gap-2 mb-3 text-start">
                <!-- Tri -->
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">Trier</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?tri=prix_croissant&valeur=<?= $valeur ?>&prix_min=<?= $prix_min ?>&prix_max=<?= $prix_max ?>">Prix croissant</a></li>
                        <li><a class="dropdown-item" href="?tri=prix_decroissant&valeur=<?= $valeur ?>&prix_min=<?= $prix_min ?>&prix_max=<?= $prix_max ?>">Prix décroissant</a></li>
                        <li><a class="dropdown-item" href="?tri=date_publication&valeur=<?= $valeur ?>&prix_min=<?= $prix_min ?>&prix_max=<?= $prix_max ?>">Date de publication</a></li>
                    </ul>
                </div>

                <!-- Type de vente -->
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">Type de vente</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?valeur=&prix_min=<?= $prix_min ?>&prix_max=<?= $prix_max ?>">Tous</a></li>
                        <li><a class="dropdown-item" href="?valeur=achat_immediat&prix_min=<?= $prix_min ?>&prix_max=<?= $prix_max ?>">Achat immédiat</a></li>
                        <li><a class="dropdown-item" href="?valeur=enchere&prix_min=<?= $prix_min ?>&prix_max=<?= $prix_max ?>">Enchère</a></li>
                        <li><a class="dropdown-item" href="?valeur=negociation&prix_min=<?= $prix_min ?>&prix_max=<?= $prix_max ?>">Négociation</a></li>
                    </ul>
                </div>

                <!-- Plage de prix -->
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">Plage de prix</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?prix_min=1&prix_max=100000&valeur=<?= $valeur ?>&tri=<?= $tri ?>">0 - 100 000 €</a></li>
                        <li><a class="dropdown-item" href="?prix_min=100001&prix_max=250000&valeur=<?= $valeur ?>&tri=<?= $tri ?>">100 001 - 250 000 €</a></li>
                        <li><a class="dropdown-item" href="?prix_min=250001&prix_max=500000&valeur=<?= $valeur ?>&tri=<?= $tri ?>">250 001 - 500 000 €</a></li>
                        <li><a class="dropdown-item" href="?prix_min=500001&prix_max=1000000&valeur=<?= $valeur ?>&tri=<?= $tri ?>">500 001 - 1 000 000 €</a></li>
                        <li><a class="dropdown-item" href="?prix_min=1000001&prix_max=2000000&valeur=<?= $valeur ?>&tri=<?= $tri ?>">1 000 001 - 2 000 000 €</a></li>
                    </ul>
                </div>
            </div>

            <!-- Affichage des annonces -->
            <?php foreach ($annonces as $annonce): ?>
                <div class="col-12 col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <a href="annonce.php?id=<?= $annonce['id'] ?>">
                            <!-- Affichage Image et titre selon id de l'annonce -->
                            <img src="<?= htmlspecialchars($annonce['image']) ?: 'images/default.jpg' ?>"
                                class="card-img-top img-fluid rounded mb-2"
                                style="max-width:100%; max-height:220px; object-fit:cover;">
                        </a>
                        <div class="card-body text-center">
                            <a href="annonce.php?id=<?= $annonce['id'] ?>" class="text-decoration-none text-dark">
                                <p class="card-title fw-bold"><?= htmlspecialchars($annonce['titre']) ?></p>
                            </a>

                            <!-- Affichage des boutons selon type de vente -->
                            <div class="d-flex flex-column gap-2 align-items-center">
                                <?php if ($annonce['type_vente'] !== 'enchere'): ?>
                                    <a href="ajouter_au_panier.php?id=<?= $annonce['id'] ?>" class="btn btn-success w-100">Ajouter au panier</a>
                                <?php endif; ?>
                                <?php if ($annonce['type_vente'] === 'negociation'): ?>
                                    <a href="negociation.php?id=<?= $annonce['id'] ?>" class="btn btn-warning w-100">Faire une offre</a>
                                <?php endif; ?>
                                <?php if ($annonce['type_vente'] === 'enchere'): ?>
                                    <?php include 'includes/temps_restant.php'; ?>
                                    <a href="enchere.php?id=<?= $annonce['id'] ?>" class="btn btn-danger w-100">Enchérir</a>
                                    <p class="mt-2 mb-0 small text-secondary" style="white-space:normal; word-break:break-word;">Fermeture de l'enchère dans : <strong><?= $temps_restant ?></strong></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

     <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
</body>
</html>
