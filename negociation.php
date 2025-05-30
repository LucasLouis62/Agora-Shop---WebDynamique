<?php
session_start();
require_once('config/connexion.php');

// Redirige si l'utilisateur n'est pas connecté
if (!isset($_SESSION['id'])) {
    header('Location: votrecompte.php');
    exit;
}

$id_produit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id_produit) {
    echo "Produit introuvable.";
    exit;
}

// Récupération des infos produit
$stmt = $bdd->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id_produit]);
$produit = $stmt->fetch();

if (!$produit || $produit['type_vente'] !== 'negociation') {
    echo "Ce produit n'est pas disponible à la négociation.";
    exit;
}

$id_acheteur = intval($_SESSION['id']);
$id_vendeur = $produit['vendeur_id'] ?? null;
$titre = $produit['titre'] ?? 'Produit';
$image = $produit['image'] ?? 'images/default.jpg';

$message_info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prix_propose = isset($_POST['prix_propose']) ? floatval($_POST['prix_propose']) : 0;
    $message = trim($_POST['message'] ?? '');

    if ($prix_propose > 0) {
        // Insertion dans la table propositions
        $stmt = $bdd->prepare("INSERT INTO propositions (id_produit, id_acheteur, id_vendeur, prix_propose, message, statut)
                               VALUES (?, ?, ?, ?, ?, 'en_attente')");
        $stmt->execute([$id_produit, $id_acheteur, $id_vendeur, $prix_propose, $message]);

        // Notification
        if ($id_vendeur) {
            $notif = $bdd->prepare("INSERT INTO notifications (utilisateur_id, message, produit_id, image_url) VALUES (?, ?, ?, ?)");
            $notif->execute([
                $id_vendeur,
                "Nouvelle offre de négociation reçue : $prix_propose € pour <strong>$titre</strong>",
                $id_produit,
                $image
            ]);
        } else {
            $notif = $bdd->prepare("INSERT INTO notifications (utilisateur_id, message, produit_id, image_url) VALUES (?, ?, ?, ?)");
            $notif->execute([
                $id_acheteur,
                "Votre offre pour le produit <strong>$titre</strong> est en attente (aucun vendeur défini pour ce produit).",
                $id_produit,
                $image
            ]);
        }

        $message_info = '<div class="alert alert-success">🎉 Votre proposition a bien été envoyée.</div>';
    } else {
        $message_info = '<div class="alert alert-danger">Veuillez proposer un prix valide.</div>';
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
        <h3 class="mb-3">Négocier pour : <?= htmlspecialchars($produit['titre']) ?></h3>
        <p><strong>Prix affiché :</strong> <?= htmlspecialchars($produit['prix']) ?> €</p>

        <?= $message_info ?>

        <form method="post">
            <div class="mb-3">
                <label for="prix_propose" class="form-label">Votre prix proposé (€)</label>
                <input type="number" name="prix_propose" id="prix_propose" class="form-control" min="1" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message au vendeur (facultatif)</label>
                <textarea name="message" id="message" class="form-control" rows="4" placeholder="Ajoutez un commentaire..."></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Envoyer la proposition</button>
            <a href="toutparcourir.php" class="btn btn-outline-secondary ms-2">← Retour</a>
        </form>
    </div>
</div>
</body>
</html>
