<?php
// Démarrage de la session utilisateur
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
    <!-- Logo -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation -->
    <?php include 'includes/navigation.php'; ?>

    <main>
    <!-- Titre principal du panier -->
    <h2 class="text-center mb-4">Votre Panier</h2>

    <!-- Affichage d'un message de succès si une action sur le panier a eu lieu (ex : ajout ou suppression) -->
    <?php if (isset($_SESSION['message_panier'])): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($_SESSION['message_panier']) ?>
        </div>
        <?php unset($_SESSION['message_panier']); ?>
    <?php endif; ?>

    <!-- Si le panier est vide, afficher un message d'information -->
    <?php if (empty($panier)): ?>
        <div class="alert alert-info text-center">Votre panier est vide.</div>
    <?php else: ?>
        <!-- Sinon, afficher le contenu du panier sous forme de tableau -->
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
                        <th></th> <!-- Colonne pour le bouton de suppression -->
                    </tr>
                </thead>
                <tbody>
                    <!-- Parcours de chaque article du panier pour affichage -->
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
                        <!-- Affichage de la photo de l'article -->
                        <td><img src="<?= $image ?>" alt="Photo" width="100" class="img-thumbnail"></td>
                        <!-- Nom de l'article -->
                        <td><?= $nom ?></td>
                        <!-- Description de l'article -->
                        <td><?= $description ?></td>
                        <!-- Quantité sélectionnée -->
                        <td><?= $quantite ?></td>
                        <!-- Prix unitaire -->
                        <td><?= $prix ?> €</td>
                        <!-- Total pour cet article (prix * quantité) -->
                        <td><?= $sous_total ?> €</td>
                        <!-- Bouton pour supprimer l'article du panier -->
                        <td>
                            <a href="?supprimer=<?= $id ?>" class="btn btn-outline-danger btn-sm">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Affichage du total général et du bouton de paiement -->
        <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
            <p class="mb-2 mb-md-0"><strong>Total :</strong> <?= $total ?> €</p>
            <a href="paiement.php" class="btn btn-success">Procéder au paiement</a>
        </div>
    <?php endif; ?>
</main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
