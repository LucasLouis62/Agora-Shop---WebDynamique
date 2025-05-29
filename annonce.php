<?php
// Connexion à la base de données (modifie les variables selon ta config)
$database = "agora";

$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);

// Récupérer l'ID du produit depuis l'URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$produit = null;
if ($db_found && $id > 0) {
    $sql = "SELECT * FROM produits WHERE id = $id";
    $result = mysqli_query($db_handle, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $produit = mysqli_fetch_assoc($result);
    }
}
// Variables par défaut si produit non trouvé
if (!$produit) {
    $produit = [
        'titre' => 'Produit inconnu',
        'description' => 'Aucune description disponible.',
        'date_ajout' => '',
        'prix' => 'N/A',
        'type_vente' => '',
        'image' => 'images/default.jpg'
    ];
}

$enchere = null;
if ($produit['type_vente'] === 'enchere') {
    $sql_enchere = "SELECT MAX(montant) AS montant FROM encheres WHERE produit_id = " . intval($produit['id']);
    $result_enchere = mysqli_query($db_handle, $sql_enchere);
    if ($result_enchere && mysqli_num_rows($result_enchere) > 0) {
        $enchere = mysqli_fetch_assoc($result_enchere);
    }
}

// Calcul du temps restant pour une enchère (72h après date_ajout)
$temps_restant = '';
if ($produit['type_vente'] === 'enchere' && !empty($produit['date_ajout'])) {
    $date_ajout = new DateTime($produit['date_ajout']);
    $date_fin = clone $date_ajout;
    $date_fin->modify('+72 hours');
    $now = new DateTime();
    if ($now < $date_fin) {
        $interval = $now->diff($date_fin);
        $temps_restant = $interval->format('%a jours %h h %i min %s s');
    } else {
        $temps_restant = 'Enchère terminée';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($produit['titre']); ?> – Agora Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .titre-produit {
            text-align: center;
            font-weight: bold;
            color: #000;
            margin-bottom: 32px;
            font-size: 2.2rem;
        }
        .encadre-bleu {
            border: 3px solid #089cfc;
            border-radius: 12px;
            background: #f0f8ff;
            padding: 24px 12px;
            margin-bottom: 16px;
        }
        .encadre-blanc {
            border: 2px solid #089cfc;
            border-radius: 12px;
            background: #fff;
            padding: 24px 24px 16px 24px;
            box-shadow: 0 2px 8px rgba(13,110,253,0.07);
        }
        .prix-produit {
            font-size: 1.3rem;
            font-weight: bold;
            color: #089cfc;
        }
    </style>
</head>
<body>
    <div class="container my-4 p-4 border rounded shadow" style="background:#fff;">
        <!-- Logo -->
        <header class="text-center mb-4">
            <img src="images/logo_agora.png" alt="Logo Agora Francia" width="200" class="img-fluid">
        </header>

        <div class="container my-5">
            <div class="titre-produit">
                <?php echo htmlspecialchars($produit['titre']); ?>
            </div>
            <div class="row justify-content-center align-items-center">
                <div class="col-md-5 mb-4 mb-md-0">
                    <div class="encadre-bleu text-center">
                        <img src="<?php echo isset($produit['image']) ? htmlspecialchars($produit['image']) : 'images/default.jpg'; ?>" alt="Produit" class="img-fluid rounded" style="max-width:300px;">
                    </div>
                </div>
            <div class="col-md-7">
                <div class="encadre-blanc">
                    <p><strong>Description :</strong> <?php echo htmlspecialchars($produit['description']); ?></p>
                    <p><strong>Date d'ajout :</strong> <?php echo htmlspecialchars($produit['date_ajout']); ?></p>
                    <p><strong>Type de vente :</strong> <?php echo htmlspecialchars($produit['type_vente']); ?></p>

                    <?php if ($produit['type_vente'] === 'achat_immediat'): ?>
                        <p class="prix-produit">Prix : <?php echo htmlspecialchars($produit['prix']); ?> €</p>
                        <a href="ajouter_au_panier.php?id=<?= $produit['id'] ?>" class="btn btn-success">Ajouter au panier</a>
                    <?php elseif ($produit['type_vente'] === 'negociation'): ?>
                        <p class="prix-produit">Prix : <?php echo htmlspecialchars($produit['prix']); ?> €</p>
                        <a href="ajouter_au_panier.php?id=<?= $produit['id'] ?>" class="btn btn-success">Ajouter au panier</a>
                        <a href="negociation.php?id=<?= $produit['id'] ?>" class="btn btn-warning">Faire une offre</a>
                        <br><br>
                        <p><strong>Proposition d'offre restante : </strong></p>
                    <?php elseif ($produit['type_vente'] === 'enchere'): ?>
                        <p class="prix-produit">Prix Minimum : <?php echo htmlspecialchars($produit['prix']); ?> €</p>
                        <p class="prix-produit">Enchère gagnante : <?php echo ($enchere && $enchere['montant'] !== null) ? htmlspecialchars($enchere['montant']) : htmlspecialchars($produit['prix']); ?> €</p>
                        <a href="enchere.php?id=<?= $produit['id'] ?>" class="btn btn-danger">Enchérir</a>
                        <br><br>
                        <p><strong>Fermeture de l'enchère dans :</strong> <?php echo $temps_restant; ?></p>
                    <?php endif; ?>
                </div>
                <br>
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-primary">Retourner à l'accueil</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pied de page -->
    <footer class="mt-4">
        <div class="row text-center text-md-start align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <h5>Contact</h5>
                <p class="mb-1">Email : <a href="mailto:agora.francia@gmail.com">agora.francia@gmail.com</a></p>
                <p class="mb-1">Téléphone : 01 23 45 67 89</p>
                <p class="mb-0">Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <p class="mb-0">&copy; 2025 Agora Francia</p>
            </div>
            <div class="col-md-4">
                <h5>Nous trouver</h5>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.8878757609433!2d2.2847854156752096!3d48.850725779286154!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1685534176532!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="220" height="120" style="border:0; border-radius:8px;"></iframe>
            </div>
        </footer>
    </div>
</body>
</html>