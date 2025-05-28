<?php
session_start();
require_once 'config/connexion.php';

if (!isset($_SESSION['id'])) {
    header('Location: votrecompte.html');
    exit;
}

$vendeur_id = $_SESSION['id'];

// Traitement acceptation/refus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['proposition_id'])) {
    $action = $_POST['action'];
    $prop_id = intval($_POST['proposition_id']);

    $nouveau_statut = ($action === 'accepter') ? 'accepte' : 'refuse';

    $stmt = $bdd->prepare("UPDATE propositions SET statut = ? WHERE id = ? AND id_vendeur = ?");
    $stmt->execute([$nouveau_statut, $prop_id, $vendeur_id]);

    // Envoi d'une notification à l'acheteur
    $stmtInfo = $bdd->prepare("SELECT id_acheteur, id_produit FROM propositions WHERE id = ?");
    $stmtInfo->execute([$prop_id]);
    $info = $stmtInfo->fetch();

    if ($info) {
        $messageNotif = ($nouveau_statut === 'accepte')
            ? "Votre offre pour le produit #" . $info['id_produit'] . " a été acceptée."
            : "Votre offre pour le produit #" . $info['id_produit'] . " a été refusée.";

        $stmtNotif = $bdd->prepare("INSERT INTO notifications (utilisateur_id, message, lue, date) VALUES (?, ?, 0, NOW())");
        $stmtNotif->execute([$info['id_acheteur'], $messageNotif]);
    }

    header("Location: proposition.php");
    exit;
}

// Récupérer toutes les propositions reçues
$stmt = $bdd->prepare("
    SELECT p.id, p.id_produit, p.prix_propose, p.date_proposition, p.statut, pr.titre, u.nom, u.prenom
    FROM propositions p
    JOIN produits pr ON p.id_produit = pr.id
    JOIN utilisateurs u ON p.id_acheteur = u.id
    WHERE p.id_vendeur = ?
    ORDER BY p.date_proposition DESC
");
$stmt->execute([$vendeur_id]);
$propositions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Propositions reçues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <h2 class="mb-4 text-center">Propositions reçues</h2>

    <?php if (empty($propositions)): ?>
        <div class="alert alert-info">Vous n'avez reçu aucune proposition pour le moment.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Produit</th>
                        <th>Acheteur</th>
                        <th>Prix proposé</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($propositions as $prop): ?>
                        <tr>
                            <td><?= htmlspecialchars($prop['titre']) ?> (#<?= $prop['id_produit'] ?>)</td>
                            <td><?= htmlspecialchars($prop['prenom'] . ' ' . $prop['nom']) ?></td>
                            <td><?= number_format($prop['prix_propose'], 2, ',', ' ') ?> €</td>
                            <td><?= $prop['date_proposition'] ?></td>
                            <td>
                                <?php if ($prop['statut'] === 'en_attente'): ?>
                                    <span class="badge bg-warning text-dark">En attente</span>
                                <?php elseif ($prop['statut'] === 'accepte'): ?>
                                    <span class="badge bg-success">Acceptée</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Refusée</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($prop['statut'] === 'en_attente'): ?>
                                    <form method="post" class="d-flex gap-2 justify-content-center">
                                        <input type="hidden" name="proposition_id" value="<?= $prop['id'] ?>">
                                        <button type="submit" name="action" value="accepter" class="btn btn-success btn-sm">Accepter</button>
                                        <button type="submit" name="action" value="refuser" class="btn btn-danger btn-sm">Refuser</button>
                                    </form>
                                <?php else: ?>
                                    <em>—</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="compte.php" class="btn btn-outline-secondary">Retour à votre compte</a>
    </div>
</div>
</body>
</html>
