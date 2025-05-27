<?php
// Connexion à la base de données
$database = "agora";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);

$annonces = [];
if ($db_found) {
    // On récupère tous les SUV (type_vente ou une colonne pour filtrer, à adapter si besoin)
    $sql = "SELECT * FROM produits WHERE Catégorie = 'suv'";
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
    <script src="acceuil.js"></script>
    <link rel="stylesheet" href="styles/prime.css">
    <title>Agora Francia – Tout parcourir - SUV</title>
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
                <a class="btn btn-primary" href="index.html">Accueil</a>
                <a class="btn btn-primary" href="toutparcourir.html">Tout Parcourir</a>
                <a class="btn btn-primary" href="notifications.html">Notifications</a>
                <a class="btn btn-primary" href="panier.html">Panier</a>
                <a class="btn btn-primary" href="votrecompte.html">Votre compte</a>
            </div>
        </nav>

        <!-- Section principale -->
        <main class="text-center mb-4">
            <h2 class="text-center mb-3">SUV disponibles</h2>

            <div class="row justify-content-center">
                <div class="mb-3 text-start">
                    <a class="btn btn-primary">Trier</a>
                </div>
                <?php foreach ($annonces as $annonce): ?>
                <div class="col-12 col-md-4 mb-4">
                    <a href="annonce.php?id=<?php echo $annonce['id']; ?>">
                        <img src="<?php echo isset($annonce['image']) ? htmlspecialchars($annonce['image']) : 'images/default.jpg'; ?>" alt="<?php echo htmlspecialchars($annonce['titre']); ?>" class="img-fluid rounded mb-2" style="max-width:200px;">
                        <p><?php echo htmlspecialchars($annonce['titre']); ?></p>
                    </a>
                </div>
                <?php endforeach; ?>
                <?php if (empty($annonces)): ?>
                <div class="col-12">
                    <p>Aucun SUV disponible pour le moment.</p>
                </div>
                <?php endif; ?>
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
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5250.744877226254!2d2.2859626768664922!3d48.85110800121838!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1748293349769!5m2!1sfr!2sfr"
                        width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
