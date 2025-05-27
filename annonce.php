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
                    <p class="prix-produit">Prix : <?php echo htmlspecialchars($produit['prix']); ?> €</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>