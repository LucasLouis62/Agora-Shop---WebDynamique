<?php
session_start();
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
      max-width: 500px;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      margin: auto;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .form-control::placeholder {
      color: #bbb;
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

      <form method="post" action="#">
        <div class="mb-3">
          <label class="form-label">Nom complet :</label>
          <input type="text" name="nom" class="form-control" placeholder="Votre nom">
        </div>

        <div class="mb-3">
          <label class="form-label">Adresse de livraison :</label>
          <input type="text" name="adresse" class="form-control" placeholder="12 rue des Fleurs, Paris">
        </div>

        <div class="mb-3">
          <label class="form-label">Numéro de carte :</label>
          <input type="text" name="carte" class="form-control" placeholder="XXXX-XXXX-XXXX-XXXX">
        </div>

        <div class="mb-3">
          <label class="form-label">Date d'expiration :</label>
          <input type="month" name="expiration" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Code de sécurité (CVV) :</label>
          <input type="text" name="cvv" class="form-control" placeholder="123">
        </div>

        <button type="submit" class="btn btn-success w-100 mb-2">Valider le paiement</button>
        <a href="toutparcourir.php" class="btn btn-outline-secondary w-100">Retour au menu</a>
      </form>

      <p class="text-center mt-3 text-muted">* Ceci est une simulation, aucun paiement réel ne sera effectué.</p>
    </div>

    <footer class="mt-5 text-center text-muted">
      <p>&copy; 2025 Agora Francia</p>
    </footer>
  </div>
</body>
</html>
