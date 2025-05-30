<?php 
session_start();
require_once 'config/connexion.php';

if (!isset($_GET['id'])) {
    echo "Produit introuvable.";
    exit;
}

$produit_id = intval($_GET['id']);

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
    $enchere = (float) ($_POST['montant'] ?? 0);
    $utilisateur_id = $_SESSION['id'];

    // Récupérer la meilleure enchère précédente
    $stmt = $bdd->prepare("SELECT utilisateur_id, MAX(montant) as montant FROM encheres WHERE produit_id = ? GROUP BY utilisateur_id ORDER BY montant DESC LIMIT 1");
    $stmt->execute([$produit_id]);
    $ancienne_meilleure = $stmt->fetch();

    $montant_min = max($produit['prix'], $ancienne_meilleure['montant'] ?? 0) + 1;

    if ($enchere >= $montant_min) {
        $stmt = $bdd->prepare("INSERT INTO encheres (produit_id, utilisateur_id, montant, date_enchere) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$produit_id, $utilisateur_id, $enchere]);

        $stmt = $bdd->prepare("INSERT INTO notifications (utilisateur_id, message, produit_id, image_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $utilisateur_id,
            "🎉 Vous êtes en tête sur le produit « {$produit['titre']} » avec une enchère de {$enchere} €.",
            $produit_id,
            $produit['image'] ?? 'images/default.jpg'
        ]);

        if (!empty($ancienne_meilleure) && $ancienne_meilleure['utilisateur_id'] != $utilisateur_id) {
            $stmt = $bdd->prepare("INSERT INTO notifications (utilisateur_id, message, produit_id, image_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $ancienne_meilleure['utilisateur_id'],
                "⚠️ Vous avez été dépassé sur le produit « {$produit['titre']} ». Nouvelle enchère : {$enchere} €.",
                $produit_id,
                $produit['image'] ?? 'images/default.jpg'
            ]);
        }

        $message = "Votre enchère a été enregistrée.";
    } else {
        $message = "Votre enchère doit être supérieure ou égale à $montant_min €.";
    }
}

// Enregistrement enchère automatique
if (isset($_POST['auto_enchere']) && isset($_POST['max_auto']) && isset($_SESSION['id'])) {
    $max_auto = (float) $_POST['max_auto'];
    $utilisateur_id = $_SESSION['id'];
    $stmt = $bdd->prepare("REPLACE INTO enchere_auto (utilisateur_id, produit_id, montant_max) VALUES (?, ?, ?)");
    $stmt->execute([$utilisateur_id, $produit_id, $max_auto]);
    $message = "✅ Votre enchère automatique jusqu'à {$max_auto} € a été enregistrée.";
}

// Logique d'enchère automatique
function verifierEncheresAutomatiques(PDO $bdd, $produit_id, $produit) {
    $stmt = $bdd->prepare("SELECT utilisateur_id, montant FROM encheres WHERE produit_id = ? ORDER BY montant DESC LIMIT 1");
    $stmt->execute([$produit_id]);
    $last = $stmt->fetch();
    $last_montant = $last['montant'] ?? 0;
    $last_user = $last['utilisateur_id'] ?? 0;

    $stmt = $bdd->prepare("SELECT * FROM enchere_auto WHERE produit_id = ? ORDER BY montant_max DESC");
    $stmt->execute([$produit_id]);
    $autos = $stmt->fetchAll();

    foreach ($autos as $auto) {
        if ($auto['utilisateur_id'] !== $last_user && $auto['montant_max'] > $last_montant) {
            $new_bid = $last_montant + 1;
            $stmt = $bdd->prepare("INSERT INTO encheres (produit_id, utilisateur_id, montant, date_enchere) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$produit_id, $auto['utilisateur_id'], $new_bid]);

            $stmt = $bdd->prepare("INSERT INTO notifications (utilisateur_id, message, produit_id, image_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $auto['utilisateur_id'],
                "🎉 Vous êtes maintenant en tête avec une auto-enchère de {$new_bid} € sur « {$produit['titre']} ».",
                $produit_id,
                $produit['image'] ?? 'images/default.jpg'
            ]);
            break;
        }
    }
}

verifierEncheresAutomatiques($bdd, $produit_id, $produit);

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

            <form method="post" class="mt-4">
                <h5>Enchère automatique</h5>
                <div class="mb-3">
                    <label for="max_auto" class="form-label">Montant max souhaité (€)</label>
                    <input type="number" name="max_auto" id="max_auto" class="form-control" required min="<?= $produit['prix'] + 1 ?>">
                </div>
                <button type="submit" name="auto_enchere" class="btn btn-outline-primary">Activer enchère automatique</button>
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
