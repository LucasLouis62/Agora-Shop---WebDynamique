<?php
session_start();

// Initialiser le panier s’il n’existe pas encore
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Supprimer un article du panier
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $_SESSION['panier'] = array_filter($_SESSION['panier'], fn($item) => ($item['id'] ?? 0) !== $id);
    header('Location: panier.php');
    exit();
}

$panier = $_SESSION['panier'];
$total = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agora Francia – Panier</title>
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
            <a class="btn btn-primary" href="panier.php">Panier</a>
            <a class="btn btn-primary" href="<?= isset($_SESSION['id']) ? 'compte.php' : 'votrecompte.html' ?>">Votre compte</a>
        </div>
    </nav>

    <main>
        <h2 class="text-center mb-4">Votre Panier</h2>

        <!-- Message de confirmation -->
        <?php if (isset($_SESSION['message_panier'])): ?>
            <div class="alert alert-success text-center">
                <?= htmlspecialchars($_SESSION['message_panier']) ?>
            </div>
            <?php unset($_SESSION['message_panier']); ?>
        <?php endif; ?>

        <?php if (empty($panier)): ?>
            <div class="alert alert-info text-center">Votre panier est vide.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Photo</th>
                            <th>Article</th>
                            <th>Description</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($panier as $article): 
                            $id = $article['id'] ?? 0;
                            $nom = htmlspecialchars($article['nom'] ?? 'Nom inconnu');
                            $description = htmlspecialchars($article['description'] ?? 'Pas de description');
                            $image = htmlspecialchars($article['image'] ?? 'images/default.jpg');
                            $quantite = $article['quantite'] ?? 1;
                            $prix = $article['prix'] ?? 0;
                            $sous_total = $prix * $quantite;
                            $total += $sous_total;
                        ?>
                        <tr>
                            <td><img src="<?= $image ?>" alt="Photo" width="100" class="img-thumbnail"></td>
                            <td><?= $nom ?></td>
                            <td><?= $description ?></td>
                            <td><?= $quantite ?></td>
                            <td><?= $prix ?> €</td>
                            <td><?= $sous_total ?> €</td>
                            <td>
                                <a href="?supprimer=<?= $id ?>" class="btn btn-outline-danger btn-sm">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
                <p class="mb-2 mb-md-0"><strong>Total :</strong> <?= $total ?> €</p>
                <a href="paiement.php" class="btn btn-success">Passer à la commande</a>
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
