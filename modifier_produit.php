<?php
session_start();
require_once 'config/connexion.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'vendeur') {
    header("Location: connexion.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Produit introuvable.";
    exit;
}

$produit_id = intval($_GET['id']);
$id_vendeur = $_SESSION['id'];

// Récupérer les infos du produit à modifier
$stmt = $bdd->prepare("SELECT * FROM produits WHERE id = ? AND id_vendeur = ?");
$stmt->execute([$produit_id, $id_vendeur]);
$produit = $stmt->fetch();

if (!$produit) {
    echo "Produit non trouvé ou non autorisé.";
    exit;
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = floatval($_POST['prix']);
    $type_vente = $_POST['type_vente'];

    $stmt = $bdd->prepare("UPDATE produits SET titre = ?, description = ?, prix = ?, type_vente = ? WHERE id = ? AND id_vendeur = ?");
    $stmt->execute([$titre, $description, $prix, $type_vente, $produit_id, $id_vendeur]);

    header("Location: espace_vendeur.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le produit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container my-5">
    <h2>Modifier l’annonce</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($produit['titre']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($produit['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Prix (€)</label>
            <input type="number" step="0.01" name="prix" class="form-control" value="<?= $produit['prix'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Type de vente</label>
            <select name="type_vente" class="form-select" required>
                <option value="achat_immediat" <?= $produit['type_vente'] === 'achat_immediat' ? 'selected' : '' ?>>Achat immédiat</option>
                <option value="enchere" <?= $produit['type_vente'] === 'enchere' ? 'selected' : '' ?>>Enchère</option>
                <option value="negociation" <?= $produit['type_vente'] === 'negociation' ? 'selected' : '' ?>>Négociation</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="espace_vendeur.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
