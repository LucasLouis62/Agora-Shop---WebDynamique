<?php
session_start();

// Simulation de deux modes de paiement
$mode_paiement = $_POST['mode_paiement'] ?? '';

// Données préremplies si utilisateur connecté
$nom = $adresse = $carte = $expiration = $cvv = '';
$message_paiement = '';

if (isset($_SESSION['id'])) {
    $id = intval($_SESSION['id']);
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, 'agora');

    if ($db_found) {
        $sql_p = "SELECT * FROM data_p WHERE id = $id LIMIT 1";
        $res_p = mysqli_query($db_handle, $sql_p);
        if ($res_p && mysqli_num_rows($res_p) > 0) {
            $data_p = mysqli_fetch_assoc($res_p);
            $nom = $data_p['prenom'] . ' ' . $data_p['nom'];
            $adresse = $data_p['adresse'];
        }

        $sql_b = "SELECT * FROM data_b WHERE id = $id LIMIT 1";
        $res_b = mysqli_query($db_handle, $sql_b);
        if ($res_b && mysqli_num_rows($res_b) > 0) {
            $data_b = mysqli_fetch_assoc($res_b);
            $carte = $data_b['numero_carte'];
            $expiration = $data_b['date_expiration'];
            $cvv = $data_b['cvv'];
        }

        // Traitement du paiement
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Simulation email de livraison (bonus)
            $to = 'utilisateur@example.com';
            $sujet = "Confirmation de livraison - Agora Francia";
            $message = "Merci pour votre commande ! Livraison estimée sous 3 à 5 jours ouvrés.";
            @mail($to, $sujet, $message);

            // Suppression des articles du panier
            if (!empty($_SESSION['panier'])) {
                foreach ($_SESSION['panier'] as $article) {
                    $prod_id = intval($article['id'] ?? 0);
                    if ($prod_id > 0) {
                        mysqli_query($db_handle, "DELETE FROM produits WHERE id = $prod_id");
                    }
                }
            }
            unset($_SESSION['panier']);
            $message_paiement = '<div class="alert alert-success text-center">Merci pour votre commande ! Paiement par ' . htmlspecialchars($mode_paiement) . ' effectué.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Paiement – Agora Francia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f0f2f5; }
    .payment-form {
      max-width: 550px;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      margin: auto;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="text-center mb-4">
      <img src="images/logo_agora.png" alt="Logo Agora Francia" width="180">
    </div>

    <div class="payment-form">
      <h3 class="text-center mb-4 text-primary">Finaliser votre commande</h3>
      <?= $message_paiement ?>
      <form method="post" action="#">
        <div class="mb-3">
          <label class="form-label">Nom complet :</label>
          <input type="text" name="nom" class="form-control" placeholder="Votre nom" value="<?= htmlspecialchars($nom) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Adresse de livraison :</label>
          <input type="text" name="adresse" class="form-control" placeholder="12 rue des Fleurs, Paris" value="<?= htmlspecialchars($adresse) ?>" required>
        </div>

        <!-- Mode de paiement -->
        <div class="mb-3">
          <label class="form-label">Mode de paiement :</label>
          <select name="mode_paiement" class="form-select" required>
            <option value="carte" <?= $mode_paiement === 'carte' ? 'selected' : '' ?>>Carte de crédit</option>
            <option value="carte_cadeau" <?= $mode_paiement === 'carte_cadeau' ? 'selected' : '' ?>>Carte cadeau</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Numéro de carte :</label>
          <input type="text" name="carte" class="form-control" placeholder="XXXX-XXXX-XXXX-XXXX" value="<?= htmlspecialchars($carte) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Date d'expiration :</label>
          <input type="month" name="expiration" class="form-control" value="<?= htmlspecialchars($expiration) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Code de sécurité (CVV) :</label>
          <input type="text" name="cvv" class="form-control" placeholder="123" value="<?= htmlspecialchars($cvv) ?>" required>
        </div>

        <button type="submit" class="btn btn-success w-100 mb-2">Valider le paiement</button>
        <a href="panier.php" class="btn btn-outline-secondary w-100">Retour au panier</a>
      </form>
      <p class="text-center mt-3 text-muted">* Simulation - aucun paiement réel n'est effectué.</p>
    </div>
    <footer class="mt-5 text-center text-muted">
      <p>&copy; 2025 Agora Francia</p>
    </footer>
  </div>
</body>
</html>
