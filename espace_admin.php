<?php
// Démarrage de la session utilisateur
session_start();
// Inclusion de la connexion PDO à la base de données
require_once 'config/connexion.php';

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['id'])) {
    header('Location: votrecompte.php');
    exit();
}

// Récupération de la liste des annonces existantes pour affichage dans le tableau de suppression
$stmt = $bdd->query("SELECT id, titre, prix FROM produits ORDER BY id DESC");
$annonces_existantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire d'ajout de vendeur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_vendeur'])) {
    require_once('config/connexion.php');
    $prenom = $_POST['prenom_vendeur'];
    $nom = $_POST['nom_vendeur'];
    $email = $_POST['email_vendeur'];
    $role = 'vendeur';
    $motdepasse = password_hash($_POST['mdp_vendeur'], PASSWORD_DEFAULT);
    // Insertion du nouveau vendeur dans la table utilisateurs
    $stmt = $bdd->prepare("INSERT INTO utilisateurs (prenom, nom, email, motdepasse, role) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$prenom, $nom, $email, $motdepasse, $role])) {
        echo '<div class="alert alert-success mt-3">Vendeur ajouté avec succès.</div>';
    } else {
        echo '<div class="alert alert-danger mt-3">Erreur lors de l\'ajout du vendeur.</div>';
    }
}
// Traitement du formulaire de suppression d'annonce
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_annonce'])) {
    require_once('config/connexion.php');
    $id_annonce = intval($_POST['id_annonce_sup']);
    // Suppression de l'annonce par son ID
    $stmt = $bdd->prepare("DELETE FROM produits WHERE id = ?");
    if ($stmt->execute([$id_annonce])) {
        if ($stmt->rowCount() > 0) {
            echo '<div class="alert alert-success mt-3">Annonce supprimée avec succès.</div>';
        } else {
            echo '<div class="alert alert-warning mt-3">Aucune annonce trouvée avec cet ID.</div>';
        }
    } else {
        echo '<div class="alert alert-danger mt-3">Erreur lors de la suppression de l\'annonce.</div>';
    }
}
// Traitement du formulaire de suppression de vendeur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_vendeur'])) {
    require_once('config/connexion.php');
    $prenom = $_POST['prenom_vendeur_sup'];
    $nom = $_POST['nom_vendeur_sup'];
    $role = 'vendeur';
    // Suppression du vendeur par prénom, nom et rôle
    $stmt = $bdd->prepare("DELETE FROM utilisateurs WHERE prenom = ? AND nom = ? AND role = ?");
    if ($stmt->execute([$prenom, $nom, $role])) {
        if ($stmt->rowCount() > 0) {
            echo '<div class="alert alert-success mt-3">Vendeur supprimé avec succès.</div>';
        } else {
            echo '<div class="alert alert-warning mt-3">Aucun vendeur trouvé avec ce nom et prénom.</div>';
        }
    } else {
        echo '<div class="alert alert-danger mt-3">Erreur lors de la suppression du vendeur.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte | Agora Francia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
        }
        .btn-lg {
            padding: 12px 24px;
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation -->
    <?php include 'includes/navigation.php'; ?>

    <div class="container py-5">
        <!-- Affichage données personnelles et boutons actions-->
        <div class="card mx-auto p-4" style="max-width: 500px;">
            <h2 class="text-center mb-4 text-primary">Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?> 👋</h2>
            <ul class="list-group list-group-flush">
                <?= isset($_SESSION['prenom']) ? htmlspecialchars($_SESSION['prenom']) : 'Utilisateur' ?>
                <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($_SESSION['nom']) ?></li>
                <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($_SESSION['email']) ?></li>
                <li class="list-group-item"><strong>Rôle :</strong> <?= htmlspecialchars($_SESSION['role']) ?></li>
            </ul>
            <div class="d-flex flex-column gap-3 mt-4">
                <button class="btn btn-outline-primary btn-lg" onclick="document.getElementById('rechercheAnnonce').style.display='block'">Rechercher une annonce</button>
                <button class="btn btn-outline-success btn-lg" onclick="document.getElementById('ajoutAnnonce').style.display='block'">Ajouter une annonce</button>
                <button class="btn btn-outline-secondary btn-lg" onclick="document.getElementById('ajoutVendeur').style.display='block'">Ajouter un vendeur</button>
                <button class="btn btn-outline-danger btn-lg" onclick="document.getElementById('supprimerVendeur').style.display='block'">Supprimer un vendeur</button>
                <button class="btn btn-outline-danger btn-lg" onclick="document.getElementById('supprimerAnnonce').style.display='block'">Supprimer une annonce</button>

            </div>
        </div>

        <!-- Bloc recherche annonce -->
        <div id="rechercheAnnonce" style="display:none;max-width:500px;margin:32px auto;">
            <form method="get" action="toutparcourir.php">
                <div class="input-group mb-3">
                    <input type="text" name="q" class="form-control" placeholder="Rechercher une annonce...">
                    <button class="btn btn-primary" type="submit">Rechercher</button>
                </div>
            </form>
        </div>

        <!-- Bloc ajout annonce -->
        <div id="ajoutAnnonce" style="display:none;max-width:500px;margin:32px auto;">
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
        <div id="supprimerAnnonce" style="display:none;max-width:500px;margin:32px auto;">
        <h5 class="text-center mt-3">Annonces existantes</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Prix (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($annonces_existantes as $annonce): ?>
                    <tr>
                        <td><?= $annonce['id'] ?></td>
                        <td><?= htmlspecialchars($annonce['titre']) ?></td>
                        <td><?= number_format($annonce['prix'], 2, ',', ' ') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="POST" action="">
                <div class="mb-3">
                    <label for="id_annonce_sup" class="form-label">ID de l'annonce à supprimer</label>
                    <input type="number" class="form-control" id="id_annonce_sup" name="id_annonce_sup" required>
                </div>
                <button type="submit" name="supprimer_annonce" class="btn btn-danger">Supprimer l'annonce</button>
            </form>
        </div>
                <!-- Bloc ajout vendeur -->
                <div id="ajoutVendeur" style="display:none;max-width:500px;margin:32px auto;">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="prenom_vendeur" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom_vendeur" name="prenom_vendeur" required>
                        </div>
                        <div class="mb-3">
                            <label for="nom_vendeur" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom_vendeur" name="nom_vendeur" required>
                        </div>
                        <div class="mb-3">
                            <label for="email_vendeur" class="form-label">Adresse mail</label>
                            <input type="email" class="form-control" id="email_vendeur" name="email_vendeur" required>
                        </div>
                        <div class="mb-3">
                            <label for="mdp_vendeur" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="mdp_vendeur" name="mdp_vendeur" required>
                        </div>
                        <button type="submit" name="ajouter_vendeur" class="btn btn-success">Ajouter le vendeur</button>
                    </form>
                </div>
                <!-- Bloc suppression vendeur -->
                <div id="supprimerVendeur" style="display:none;max-width:500px;margin:32px auto;">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="prenom_vendeur_sup" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom_vendeur_sup" name="prenom_vendeur_sup" required>
                        </div>
                        <div class="mb-3">
                            <label for="nom_vendeur_sup" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom_vendeur_sup" name="nom_vendeur_sup" required>
                        </div>
                        <button type="submit" name="supprimer_vendeur" class="btn btn-danger">Supprimer le vendeur</button>
                    </form>
                </div>
                <!-- Fin nouveaux blocs -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-outline-primary btn-custom">🏠 Retour à l'accueil</a>
                    <a href="deconnexion.php" class="btn btn-danger btn-custom">🔓 Se déconnecter</a>
                </div>
            </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>