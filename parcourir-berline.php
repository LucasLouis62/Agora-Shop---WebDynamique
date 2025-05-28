<?php
// Connexion à la base de données
$database = "agora";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);

$annonces = [];
if ($db_found) {
    // Déterminer le tri en fonction des paramètres "tri", "valeur" et "prix"
    $tri = isset($_GET['tri']) ? $_GET['tri'] : '';
    $valeur = isset($_GET['valeur']) ? $_GET['valeur'] : '';
    $prix_min = isset($_GET['prix_min']) ? intval($_GET['prix_min']) : 0;
    $prix_max = isset($_GET['prix_max']) ? intval($_GET['prix_max']) : 0;

    // Construire la requête SQL en fonction du tri, du filtre et de la plage de prix
    $orderBy = '';
    $whereClause = "WHERE Catégorie = 'berline'";

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

    // Requête SQL pour récupérer les berlines avec le tri, le filtre et la plage de prix
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="styles/prime.css">
    <title>Agora Francia – Tout parcourir - Berlines</title>
</head>

<body>
    <div class="container my-4 p-4 border rounded shadow" style="background:#fff;">
        <!-- Logo -->
        <header class="text-center mb-4">
            <img src="images/logo_agora.png" alt="Logo Agora Francia" width="200" class="img-fluid">
        </header>

        <!-- Barre de navigation -->
        <nav class="navbar navbar-expand justify-content-center mb-4">
            <div class="navbar-nav gap-2">
                <a class="btn btn-primary" href="index.php">Accueil</a>
                <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
                <a class="btn btn-primary" href="notifications.php">Notifications</a>
                <a class="btn btn-primary" href="panier.php">Panier</a>
                <a class="btn btn-primary" href="votrecompte.php">Votre compte</a>
            </div>
        </nav>

        <!-- Section principale -->
        <main class="text-center mb-4">
            <h2 class="text-center mb-3">Berlines disponibles</h2>

            <div class="row justify-content-center">
                <div class="d-flex gap-2 mb-3 text-start">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Trier
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="?tri=prix_croissant&valeur=<?= htmlspecialchars($valeur); ?>&prix_min=<?= htmlspecialchars($prix_min); ?>&prix_max=<?= htmlspecialchars($prix_max); ?>">Prix croissant</a></li>
                            <li><a class="dropdown-item" href="?tri=prix_decroissant&valeur=<?= htmlspecialchars($valeur); ?>&prix_min=<?= htmlspecialchars($prix_min); ?>&prix_max=<?= htmlspecialchars($prix_max); ?>">Prix décroissant</a></li>
                            <li><a class="dropdown-item" href="?tri=date_publication&valeur=<?= htmlspecialchars($valeur); ?>&prix_min=<?= htmlspecialchars($prix_min); ?>&prix_max=<?= htmlspecialchars($prix_max); ?>">Date de publication</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonType" data-bs-toggle="dropdown" aria-expanded="false">
                            Type de vente
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonType">
                            <li><a class="dropdown-item" href="?valeur=&prix_min=<?= htmlspecialchars($prix_min); ?>&prix_max=<?= htmlspecialchars($prix_max); ?>">Tous</a></li>
                            <li><a class="dropdown-item" href="?valeur=achat_immediat&prix_min=<?= htmlspecialchars($prix_min); ?>&prix_max=<?= htmlspecialchars($prix_max); ?>">Achat immédiat</a></li>
                            <li><a class="dropdown-item" href="?valeur=enchere&prix_min=<?= htmlspecialchars($prix_min); ?>&prix_max=<?= htmlspecialchars($prix_max); ?>">Enchère</a></li>
                            <li><a class="dropdown-item" href="?valeur=negociation&prix_min=<?= htmlspecialchars($prix_min); ?>&prix_max=<?= htmlspecialchars($prix_max); ?>">Négociation</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonPrice" data-bs-toggle="dropdown" aria-expanded="false">
                            Plage de prix
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonPrice">
                            <li><a class="dropdown-item" href="?prix_min=1&prix_max=10000&valeur=<?= htmlspecialchars($valeur); ?>&tri=<?= htmlspecialchars($tri); ?>">0 - 10 000 €</a></li>
                            <li><a class="dropdown-item" href="?prix_min=10001&prix_max=30000&valeur=<?= htmlspecialchars($valeur); ?>&tri=<?= htmlspecialchars($tri); ?>">10 001 - 30 000 €</a></li>
                            <li><a class="dropdown-item" href="?prix_min=30001&prix_max=50000&valeur=<?= htmlspecialchars($valeur); ?>&tri=<?= htmlspecialchars($tri); ?>">30 001 - 50 000 €</a></li>
                            <li><a class="dropdown-item" href="?prix_min=50001&prix_max=100000&valeur=<?= htmlspecialchars($valeur); ?>&tri=<?= htmlspecialchars($tri); ?>">50 001 - 100 000 €</a></li>
                        </ul>
                    </div>
                </div>
                <?php foreach ($annonces as $annonce): ?>
                <div class="col-12 col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <a href="annonce.php?id=<?= $annonce['id']; ?>">
                            <img src="<?= isset($annonce['image']) ? htmlspecialchars($annonce['image']) : 'images/default.jpg'; ?>"
                                alt="<?= htmlspecialchars($annonce['titre']); ?>"
                                class="card-img-top img-fluid rounded mb-2"
                                style="max-width:100%; max-height:220px; object-fit:cover;">
                        </a>
                        <div class="card-body text-center">
                            <a href="annonce.php?id=<?= $annonce['id']; ?>" class="text-decoration-none text-dark">
                                <p class="card-title fw-bold"><?= htmlspecialchars($annonce['titre']); ?></p>
                            </a>
                            <?php if ($annonce['type_vente'] === 'achat_immediat'): ?>
                                <form method="POST" action="ajouter_au_panier.php">
                                    <input type="hidden" name="produit_id" value="<?= $annonce['id']; ?>">
                                    <button type="submit" class="btn btn-outline-success mt-2">Ajouter au panier</button>
                                </form>
                            <?php elseif ($annonce['type_vente'] === 'enchere'): ?>
                                <a href="annonce.php?id=<?= $annonce['id']; ?>#encherir" class="btn btn-success mt-2">Enchérir</a>
                            <?php elseif ($annonce['type_vente'] === 'negociation'): ?>
                                <a href="annonce.php?id=<?= $annonce['id']; ?>#acheter" class="btn btn-primary mt-2">Faire une offre</a>
                                <form method="POST" action="ajouter_au_panier.php">
                                    <input type="hidden" name="produit_id" value="<?= $annonce['id']; ?>">
                                    <button type="submit" class="btn btn-outline-success mt-2">Ajouter au panier</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>

        <!-- Pied de page -->
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
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.8878757609433!2d2.2847854156752096!3d48.850725779286154!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1685534176532!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>