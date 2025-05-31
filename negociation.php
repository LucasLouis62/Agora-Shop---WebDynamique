<?php
// Démarrage de la session utilisateur
session_start();
require_once('config/connexion.php');

// Redirige si l'utilisateur n'est pas connecté
if (!isset($_SESSION['id'])) {
    header('Location: votrecompte.php');
    exit;
}

// Récupération de l'ID du produit depuis l'URL
$id_produit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id_produit) {
    echo "Produit introuvable.";
    exit;
}

// Récupérer les infos du produit
$stmt = $bdd->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id_produit]);
$produit = $stmt->fetch();

// Vérifie que le produit existe et qu'il est bien en négociation
if (!$produit || $produit['type_vente'] !== 'negociation') {
    echo "Ce produit n'est pas disponible à la négociation.";
    exit;
}

// Préparation des variables utiles
$id_acheteur = intval($_SESSION['id']);
$id_vendeur = $produit['id_vendeur'] ?? null;
$titre = $produit['titre'] ?? 'Produit';
$image = $produit['image'] ?? 'images/default.jpg';
$role_utilisateur = $_SESSION['role'];

$message_info = '';

// Calcul du nombre d'échanges entre ce vendeur et cet acheteur (max 5)
$stmt = $bdd->prepare("SELECT COUNT(*) FROM propositions WHERE id_produit = ? AND ((id_acheteur = ? AND id_vendeur = ?) OR (id_acheteur = ? AND id_vendeur = ?))");
$stmt->execute([$id_produit, $id_acheteur, $id_vendeur, $id_vendeur, $id_acheteur]);
$nb_echanges = $stmt->fetchColumn();

// Traitement de la soumission d'une proposition de prix
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $nb_echanges < 5) {
    $prix_propose = isset($_POST['prix_propose']) ? floatval($_POST['prix_propose']) : 0;
    $message = trim($_POST['message'] ?? '');

    if ($prix_propose > 0) {
        // Détermine qui est l'auteur et le destinataire de la proposition
        if ($role_utilisateur === 'vendeur') {
            $auteur_id = $id_vendeur;
            $destinataire_id = $id_acheteur;
        } else {
            $auteur_id = $id_acheteur;
            $destinataire_id = $id_vendeur;
        }

        // Insertion de la proposition en base
        $stmt = $bdd->prepare("INSERT INTO propositions (id_produit, id_acheteur, id_vendeur, prix_propose, message, statut) VALUES (?, ?, ?, ?, ?, 'en_attente')");
        $stmt->execute([$id_produit, $id_acheteur, $id_vendeur, $prix_propose, $message]);

        // Création d'une notification pour le destinataire
        $notif = $bdd->prepare("INSERT INTO notifications (utilisateur_id, message, produit_id, image_url) VALUES (?, ?, ?, ?)");
        $notif->execute([
            $destinataire_id,
            "Nouvelle proposition de prix : $prix_propose € pour <strong>$titre</strong>.",
            $id_produit,
            $image
        ]);

        $message_info = '<div class="alert alert-success">🎉 Votre proposition a été envoyée.</div>';
    } else {
        $message_info = '<div class="alert alert-danger">Veuillez saisir un prix valide.</div>';
    }
} elseif ($nb_echanges >= 5) {
    // Si le nombre maximal d'échanges est atteint
    $message_info = '<div class="alert alert-warning">Le nombre maximal d’échanges (5) a été atteint.</div>';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Négociation – <?= htmlspecialchars($produit['titre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="card shadow p-4">
        <h3 class="mb-3">Négociation pour : <?= htmlspecialchars($produit['titre']) ?></h3>
        <p><strong>Prix affiché actuellement :</strong> <?= htmlspecialchars($produit['prix']) ?> €</p>

        <?= $message_info ?>

        <?php if ($nb_echanges < 5): ?>
            <form method="post">
                <div class="mb-3">
                    <label for="prix_propose" class="form-label">Proposer un prix (€)</label>
                    <input type="number" name="prix_propose" id="prix_propose" class="form-control" min="1" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message (optionnel)</label>
                    <textarea name="message" id="message" class="form-control" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-warning">Envoyer la proposition</button>
                <a href="toutparcourir.php" class="btn btn-secondary ms-2">Retour</a>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
