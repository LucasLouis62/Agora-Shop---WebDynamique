<?php
session_start();

// Redirection si non connecté
if (!isset($_SESSION['id'])) {
    header('Location: votrecompte.php');
    exit();
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
            <a class="btn btn-primary" href="<?= isset($_SESSION['id']) ? 'compte.php' : 'votrecompte.php' ?>">Votre compte</a>
        </div>
    </nav>

    <div class="container py-5">
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
    <?php
// Connexion et récupération des annonces existantes
require_once('config/connexion.php');
$stmt = $bdd->query("SELECT id, titre, prix FROM produits ORDER BY id DESC");
$annonces_existantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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

    <footer class="row text-center text-md-start align-items-center mt-5">
        <div class="col-md-4 mb-3 mb-md-0">
            <h5>Contact</h5>
            <p>Email : <a href="mailto:agora.francia@gmail.com">agora.francia@gmail.com</a></p>
            <p>Téléphone : 01 23 45 67 89</p>
            <p>Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <p>&copy; 2025 Agora Francia</p>
        </div>
        <div class="col-md-4">
            <h5>Nous trouver</h5>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.8878757609433!2d2.2847854156752096!3d48.850725779286154!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1685534176532!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="220" height="120" style="border:0; border-radius:8px;"></iframe>
        </div>
    </footer>
</div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_vendeur'])) {
    require_once('config/connexion.php');
    $prenom = $_POST['prenom_vendeur'];
    $nom = $_POST['nom_vendeur'];
    $email = $_POST['email_vendeur'];
    $role = 'vendeur';
    $motdepasse = password_hash($_POST['mdp_vendeur'], PASSWORD_DEFAULT);
    $stmt = $bdd->prepare("INSERT INTO utilisateurs (prenom, nom, email, motdepasse, role) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$prenom, $nom, $email, $motdepasse, $role])) {
        echo '<div class="alert alert-success mt-3">Vendeur ajouté avec succès.</div>';
    } else {
        echo '<div class="alert alert-danger mt-3">Erreur lors de l\'ajout du vendeur.</div>';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_annonce'])) {
    require_once('config/connexion.php');
    $id_annonce = intval($_POST['id_annonce_sup']);

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_vendeur'])) {
    require_once('config/connexion.php');
    $prenom = $_POST['prenom_vendeur_sup'];
    $nom = $_POST['nom_vendeur_sup'];
    $role = 'vendeur';
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
