<?php
session_start();
require_once 'config/connexion.php';

if (!isset($_SESSION['id'])) {
    header('Location: votrecompte.php');
    exit;
}

$id_produit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id_produit) {
    echo "Produit introuvable.";
    exit;
}

// Récupérer le produit et le vendeur associé
$stmt = $bdd->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id_produit]);
$produit = $stmt->fetch();

if (!$produit || $produit['type_vente'] !== 'negociation') {
    echo "Ce produit n'est pas disponible pour la négociation.";
    exit;
}

// On suppose que la colonne id_vendeur existe dans la table produits
$id_vendeur = isset($produit['id_vendeur']) ? intval($produit['id_vendeur']) : 0;
if (!$id_vendeur) {
    echo "Aucun vendeur associé à ce produit.";
    exit;
}

$message_info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prix_propose = isset($_POST['prix_propose']) ? floatval($_POST['prix_propose']) : 0;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $id_acheteur = intval($_SESSION['id']);

    if ($prix_propose > 0) {
        $stmt = $bdd->prepare("INSERT INTO propositions (id_produit, id_acheteur, id_vendeur, prix_propose, message, statut) VALUES (?, ?, ?, ?, ?, 'en_attente')");
        $stmt->execute([$id_produit, $id_acheteur, $id_vendeur, $prix_propose, $message]);

        // Notification vendeur (optionnel)
        $notif = $bdd->prepare("INSERT INTO notifications (utilisateur_id, message) VALUES (?, ?)");
        $notif->execute([$id_vendeur, "Nouvelle proposition reçue pour votre produit : $prix_propose €"]);

        $message_info = '<div class="alert alert-success">Votre offre a bien été envoyée au vendeur.</div>';
    } else {
        $message_info = '<div class="alert alert-danger">Veuillez saisir un prix valide.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Négocier – <?= htmlspecialchars($produit['titre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="card shadow p-4">
        <h3>Négocier pour : <?= htmlspecialchars($produit['titre']) ?></h3>
        <div class="mb-3">
            <span class="fw-bold">Prix actuel du produit :</span>
            <span class="text-success" style="font-size:1.2rem;"><?= htmlspecialchars($produit['prix']) ?> €</span>
        </div>
        <?= $message_info ?>
        <form method="post">
            <div class="mb-3">
                <label for="prix_propose" class="form-label">Votre prix proposé (€)</label>
                <input type="number" name="prix_propose" id="prix_propose" class="form-control" min="1" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message au vendeur</label>
                <textarea name="message" id="message" class="form-control" rows="4" placeholder="Ajoutez un commentaire facultatif..."></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Envoyer la proposition</button> <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
        </form>
    </div>
</div>
</body>
</html>