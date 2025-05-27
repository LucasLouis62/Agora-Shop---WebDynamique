<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('config/connexion.php');

// Vérifie que l'utilisateur est connecté et est vendeur
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'vendeur') {
    header('Location: connexion.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $type_vente = $_POST['type_vente'];
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    // Dossier images
    $dossier = "images/";
    $chemin = $dossier . basename($image);
    move_uploaded_file($tmp, $chemin);

    $stmt = $bdd->prepare("INSERT INTO produits (titre, description, prix, type_vente, image, vendeur_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titre, $description, $prix, $type_vente, $chemin, $_SESSION['id']]);

    $message = "Produit ajouté avec succès ✅";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit – Agora Francia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
            <h2 class="text-center mb-4">Ajouter une voiture</h2>
            <?php if (!empty($message)) : ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Prix (€)</label>
                    <input type="number" step="0.01" name="prix" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type de vente</label>
                    <select name="type_vente" class="form-select" required>
                        <option value="achat_immediat">Achat immédiat</option>
                        <option value="enchere">Enchère</option>
                        <option value="negociation">Négociation</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ajouter la voiture</button>
            </form>
        </div>
    </div>
</body>
</html>
