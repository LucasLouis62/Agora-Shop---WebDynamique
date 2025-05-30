<?php
session_start();
require_once('config/connexion.php');

// Redirection si non connecté
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'vendeur') {
    header('Location: votrecompte.php');
    exit();
}

$message = '';

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poster_annonce'])) {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'] ?? '';
    $type_vente = $_POST['type_vente'];
    $image = $_POST['image'];
    $date_ajout = date('Y-m-d H:i:s');
    $vendeur_id = intval($_SESSION['id']);

    $stmt = $bdd->prepare("INSERT INTO produits (titre, description, prix, Catégorie, type_vente, image, date_ajout, id_vendeur) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$titre, $description, $prix, $categorie, $type_vente, $image, $date_ajout, $vendeur_id])) {
        $message = "✅ Annonce ajoutée avec succès !";
    } else {
        $message = "❌ Une erreur s'est produite lors de l'ajout de l'annonce.";
    }
}

// Récupération des annonces du vendeur
$annonces = [];
$stmt = $bdd->prepare("SELECT * FROM produits WHERE id_vendeur = ?");
$stmt->execute([$_SESSION['id']]);
$annonces = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Vendeur | Agora Francia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 1rem; }
        .btn-lg { padding: 12px 24px; }
    </style>
</head>
<body>
<div class="container my-4 p-4 bg-white shadow rounded">
    <header class="text-center mb-4">
        <img src="images/logo_agora.png" alt="Logo Agora Francia" width="200" class="img-fluid">
    </header>

    <nav class="navbar navbar-expand justify-content-center mb-4">
        <div class="navbar-nav gap-2">
            <a class="btn btn-primary" href="index.php">Accueil</a>
            <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
            <a class="btn btn-primary" href="notifications.php">Notifications</a>
            <a class="btn btn-primary" href="panier.php">Panier</a>
            <a class="btn btn-primary" href="compte.php">Votre compte</a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="text-center mb-4">
            <button class="btn btn-primary btn-lg" onclick="document.getElementById('formPosterAnnonce').style.display='block'">📢 Poster une annonce</button>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Formulaire d'ajout -->
        <div id="formPosterAnnonce" class="mt-4" style="display: none;">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" class="form-control" id="titre" name="titre" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="prix" class="form-label">Prix (€)</label>
                    <input type="number" class="form-control" id="prix" name="prix" required>
                </div>
                <div class="mb-3">
                    <label for="categorie" class="form-label">Catégorie</label>
                    <select class="form-select" id="categorie" name="categorie" required>
                        <option value="suv">SUV</option>
                        <option value="berline">Berline</option>
                        <option value="sportive">Sportive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="type_vente" class="form-label">Type de vente</label>
                    <select class="form-select" id="type_vente" name="type_vente" required>
                        <option value="achat_immediat">Achat immédiat</option>
                        <option value="enchere">Enchère</option>
                        <option value="negociation">Négociation</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">URL de l'image</label>
                    <input type="text" class="form-control" id="image" name="image" required>
                </div>
                <button type="submit" name="poster_annonce" class="btn btn-success">Ajouter l'annonce</button>
            </form>
        </div>

        <!-- Liste des annonces -->
        <h4 class="mt-5">📦 Mes annonces</h4>
        <?php if (count($annonces) === 0): ?>
            <p class="text-muted">Vous n'avez encore posté aucune annonce.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($annonces as $a): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?= htmlspecialchars($a['image']) ?>" class="card-img-top" alt="Image" style="height: 180px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($a['titre']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($a['description']) ?></p>
                                <p><strong><?= number_format($a['prix'], 0, ',', ' ') ?> €</strong></p>
                                <p class="text-muted"><?= ucfirst($a['Catégorie']) ?> • <?= ucfirst($a['type_vente']) ?></p>
                                <div class="d-grid gap-1">
                                    <a href="modifier_produit.php?id=<?= $a['id'] ?>" class="btn btn-outline-primary btn-sm">✏️ Modifier</a>
                                    <a href="supprimer_produit.php?id=<?= $a['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette annonce ?')">🗑️ Supprimer</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between mt-4">
            <a href="index.php" class="btn btn-outline-secondary">🏠 Retour à l'accueil</a>
            <a href="deconnexion.php" class="btn btn-danger">🔓 Se déconnecter</a>
        </div>
    </div>

    <footer class="row text-center text-md-start align-items-center mt-5">
        <div class="col-md-4">
            <h5>Contact</h5>
            <p>Email : <a href="mailto:agora.francia@gmail.com">agora.francia@gmail.com</a></p>
            <p>Téléphone : 01 23 45 67 89</p>
            <p>Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
        </div>
        <div class="col-md-4">
            <p>&copy; 2025 Agora Francia</p>
        </div>
        <div class="col-md-4">
            <h5>Nous trouver</h5>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18..." width="220" height="120" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </footer>
</div>
</body>
</html>
