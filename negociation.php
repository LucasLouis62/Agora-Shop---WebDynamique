<?php
session_start();
require_once 'config/connexion.php';

if (!isset($_SESSION['id'])) {
    header('Location: votrecompte.html');
    exit;
}

$produit_id = $_GET['id'] ?? null;
$message_confirmation = '';

if (!$produit_id) {
    echo "Produit introuvable.";
    exit;
}

// Récupérer le produit
$stmt = $bdd->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$produit_id]);
$produit = $stmt->fetch();

if (!$produit || $produit['type_vente'] !== 'negociation') {
    echo "Ce produit n'est pas disponible pour la négociation.";
    exit;
}

$vendeur_id = $produit['vendeur_id'] ?? null;
if (!$vendeur_id) {
    echo "Erreur : aucun vendeur n'est associé à ce produit.";
    exit;
}

// Formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prix_propose = $_POST['prix_propose'];
    $message = $_POST['message'];
    $acheteur_id = $_SESSION['id'];

    $stmt = $bdd->prepare("INSERT INTO propositions (id_produit, id_acheteur, id_vendeur, prix_propose, message, statut)
                           VALUES (?, ?, ?, ?, ?, 'en_attente')");
    $stmt->execute([$produit_id, $acheteur_id, $vendeur_id, $prix_propose, $message]);

    $message_confirmation = "🎉 Votre proposition a bien été envoyée. Elle est en attente de réponse du vendeur.";
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

        <?php if (!empty($message_confirmation)): ?>
            <div class="alert alert-success text-center"><?= $message_confirmation ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="prix_propose" class="form-label">Votre prix proposé (€)</label>
                <input type="number" name="prix_propose" id="prix_propose" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message au vendeur</label>
                <textarea name="message" id="message" class="form-control" rows="4" placeholder="Ajoutez un commentaire facultatif..."></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Envoyer la proposition</button>
        </form>
    </div>
</div>
</body>
</html>
