<?php
// Démarrage de la session utilisateur
session_start();
// Inclusion de la connexion PDO à la base de données
require_once 'config/connexion.php';

// Vérifie si le formulaire d'ajout de produit a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des champs du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];
    $type_vente = $_POST['type_vente'];
    $date_fin_enchere = !empty($_POST['date_fin_enchere']) ? $_POST['date_fin_enchere'] : null;

    // Gestion de l'upload de l'image
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetPath = "images/" . $imageName;
        // Déplace le fichier uploadé dans le dossier images
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    // Insertion du produit dans la base de données
    $stmt = $bdd->prepare("INSERT INTO produits (titre, description, prix, Catégorie, image, type_vente, date_fin_enchere) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titre, $description, $prix, $categorie, $imagePath, $type_vente, $date_fin_enchere]);

    // Message de confirmation
    $message = "Produit ajouté avec succès !";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5 p-4 bg-white shadow rounded">
    <nav class="mb-4">
        <a class="btn btn-primary me-2" href="index.php">Accueil</a>
        <a class="btn btn-secondary" href="votrecompte.php">Votre compte</a>
    </nav>

    <h2 class="mb-4">Ajouter un produit</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="titre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Prix de départ (€)</label>
            <input type="number" name="prix" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Catégorie</label>
            <select name="categorie" class="form-select" required>
                <option value="suv">SUV</option>
                <option value="berline">Berline</option>
                <option value="sportive">Sportive</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Type de vente</label>
            <select name="type_vente" class="form-select" required onchange="toggleEnchereDate(this.value)">
                <option value="achat_immediat">Achat immédiat</option>
                <option value="negociation">Négociation</option>
                <option value="enchere">Enchère</option>
            </select>
        </div>

        <div class="mb-3" id="date_enchere_field" style="display: none;">
            <label class="form-label">Date de fin d’enchère</label>
            <input type="datetime-local" name="date_fin_enchere" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Ajouter le produit</button>
    </form>
</div>

<script>
    function toggleEnchereDate(value) {
        document.getElementById('date_enchere_field').style.display = value === 'enchere' ? 'block' : 'none';
    }
</script>
</body>
</html>
