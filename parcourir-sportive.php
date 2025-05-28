<?php
// Connexion à la base de données
$database = "agora";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);

$annonces = [];
if ($db_found) {
    // Tri, filtres, plages de prix
    $tri = $_GET['tri'] ?? '';
    $valeur = $_GET['valeur'] ?? '';
    $prix_min = isset($_GET['prix_min']) ? intval($_GET['prix_min']) : 0;
    $prix_max = isset($_GET['prix_max']) ? intval($_GET['prix_max']) : 0;

    $orderBy = '';
    $whereClause = "WHERE Catégorie = 'sportive'";

    if (!empty($valeur)) {
        $whereClause .= " AND type_vente = '" . mysqli_real_escape_string($db_handle, $valeur) . "'";
    }

    if ($prix_min > 0 && $prix_max > 0) {
        $whereClause .= " AND prix BETWEEN $prix_min AND $prix_max";
    }

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="styles/prime.css">
</head>
<body>
<div class="container my-4 p-4 border rounded shadow" style="background:#fff;">
    <header class="text-center mb-4">
        <img src="images/logo_agora.png" alt="Logo Agora Francia" width="200">
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

    <main class="text-center mb-4">
        <h2 class="mb-3">Sportives disponibles</h2>
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
                    <li><a class="dropdown-item" href="?prix_min=100000&prix_max=250000&valeur=<?= $valeur ?>&tri=<?= $tri ?>">100 000 - 250 000 €</a></li>
                    <li><a class="dropdown-item" href="?prix_min=250001&prix_max=500000&valeur=<?= $valeur ?>&tri=<?= $tri ?>">250 001 - 500 000 €</a></li>
                    <li><a class="dropdown-item" href="?prix_min=500001&prix_max=1000000&valeur=<?= $valeur ?>&tri=<?= $tri ?>">500 001 - 1 000 000 €</a></li>
                    <li><a class="dropdown-item" href="?prix_min=1000001&prix_max=2000000&valeur=<?= $valeur ?>&tri=<?= $tri ?>">1 000 001 - 2 000 000 €</a></li>
                </ul>
            </div>
        </div>

        <div class="row justify-content-center">
            <?php foreach ($annonces as $annonce): ?>
                <div class="col-12 col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <a href="annonce.php?id=<?= $annonce['id'] ?>">
                            <img src="<?= htmlspecialchars($annonce['image']) ?: 'images/default.jpg' ?>"
                                 class="card-img-top img-fluid rounded mb-2"
                                 style="max-width:100%; max-height:220px; object-fit:cover;">
                        </a>
                        <div class="card-body text-center">
                            <a href="annonce.php?id=<?= $annonce['id'] ?>" class="text-decoration-none text-dark">
                                <p class="card-title fw-bold"><?= htmlspecialchars($annonce['titre']) ?></p>
                            </a>
                            <?php if ($annonce['type_vente'] === 'achat_immediat'): ?>
                                <div class="d-grid gap-2 mt-2">
                                    <a href="annonce.php?id=<?= $annonce['id'] ?>#acheter" class="btn btn-success">Acheter</a>
                                    <a href="ajouter_au_panier.php?id=<?= $annonce['id'] ?>" class="btn btn-outline-primary btn-sm">Ajouter au panier</a>
                                </div>
                            <?php elseif ($annonce['type_vente'] === 'enchere'): ?>
                                <div class="d-grid gap-2 mt-2">
                                    <a href="annonce.php?id=<?= $annonce['id'] ?>#encherir" class="btn btn-success">Enchérir</a>
                                </div>
                            <?php elseif ($annonce['type_vente'] === 'negociation'): ?>
                                <div class="d-grid gap-2 mt-2">
                                    <a href="annonce.php?id=<?= $annonce['id'] ?>#acheter" class="btn btn-success">Faire une offre</a>
                                    <a href="ajouter_au_panier.php?id=<?= $annonce['id'] ?>" class="btn btn-outline-primary btn-sm">Ajouter au panier</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="mt-4">
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
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.8878757609433!2d2.2847854156752096!3d48.850725779286154!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1685534176532!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
