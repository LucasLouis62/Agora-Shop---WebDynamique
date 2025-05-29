<?php
session_start();
require_once 'config/connexion.php';

if (!isset($_GET['id'])) {
    echo "Produit introuvable.";
    exit;
}

$produit_id = $_GET['id'];

// Récupérer les infos du produit
$stmt = $bdd->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$produit_id]);
$produit = $stmt->fetch();

if (!$produit || $produit['type_vente'] !== 'enchere') {
    echo "Ce produit n'est pas en vente aux enchères.";
    exit;
}

// Gérer la soumission d'une enchère
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id'])) {
    $enchere = (float) $_POST['montant'];
    $utilisateur_id = $_SESSION['id'];

    // Vérifier la dernière enchère
    $stmt = $bdd->prepare("SELECT MAX(montant) FROM encheres WHERE produit_id = ?");
    $stmt->execute([$produit_id]);
    $max_enchere = $stmt->fetchColumn();

    $montant_min = max($produit['prix'], $max_enchere ?? 0) + 1;

    if ($enchere >= $montant_min) {
        $stmt = $bdd->prepare("INSERT INTO encheres (produit_id, utilisateur_id, montant, date_enchere) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$produit_id, $utilisateur_id, $enchere]);
        $message = "Votre enchère a été enregistrée.";
    } else {
        $message = "Votre enchère doit être supérieure ou égale à $montant_min €.";
    }
}

// Historique
$stmt = $bdd->prepare("SELECT e.montant, u.nom, e.date_enchere FROM encheres e JOIN utilisateurs u ON e.utilisateur_id = u.id WHERE e.produit_id = ? ORDER BY e.date_enchere DESC");
$stmt->execute([$produit_id]);
$historique = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enchère – <?= htmlspecialchars($produit['titre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="card p-4 shadow">
        <h2><?= htmlspecialchars($produit['titre']) ?></h2>
        <a href="toutparcourir.php" class="btn btn-outline-secondary btn-sm mb-3">← Retour au menu</a>

        <p><?= htmlspecialchars($produit['description']) ?></p>
        <p>Prix de départ : <strong><?= $produit['prix'] ?> €</strong></p>
        <p>Date de fin d’enchère : <strong><?= $produit['date_fin_enchere'] ?? 'non spécifiée' ?></strong></p>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['id'])): ?>
            <form method="post" class="mt-3">
                <div class="mb-3">
                    <label for="montant" class="form-label">Votre enchère (€)</label>
                    <input type="number" name="montant" id="montant" class="form-control" required min="<?= $produit['prix'] + 1 ?>">
                </div>
                <button type="submit" class="btn btn-danger">Placer une enchère</button>
            </form>
        <?php else: ?>
            <p class="text-warning">Connectez-vous pour participer à l'enchère.</p>
        <?php endif; ?>

        <h4 class="mt-4">Historique des enchères</h4>
        <ul class="list-group">
            <?php foreach ($historique as $e): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($e['nom']) ?></strong> : <?= $e['montant'] ?> € – <?= $e['date_enchere'] ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
</body>
</html>
