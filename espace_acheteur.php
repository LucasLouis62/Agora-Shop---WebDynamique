<?php
session_start();

// Redirection si non connecté
if (!isset($_SESSION['id'])) {
    header('Location: votrecompte.php');
    exit();
}

// Connexion à la base de données
$database = "agora";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);
$id = intval($_SESSION['id']);

// Gestion formulaire bancaire
$b_message = '';
if (isset($_POST['save_bancaire'])) {
    $type_carte = mysqli_real_escape_string($db_handle, $_POST['type_carte']);
    $numero_carte = mysqli_real_escape_string($db_handle, $_POST['numero_carte']);
    $nom_sur_carte = mysqli_real_escape_string($db_handle, $_POST['nom_sur_carte']);
    $date_expiration = mysqli_real_escape_string($db_handle, $_POST['date_expiration']);
    $cvv = mysqli_real_escape_string($db_handle, $_POST['cvv']);
    // Vérifie si déjà existant
    $check = mysqli_query($db_handle, "SELECT * FROM data_b WHERE id = $id");
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE data_b SET type_carte='$type_carte', numero_carte='$numero_carte', nom_sur_carte='$nom_sur_carte', date_expiration='$date_expiration', cvv='$cvv' WHERE id=$id";
    } else {
        $sql = "INSERT INTO data_b (id, type_carte, numero_carte, nom_sur_carte, date_expiration, cvv) VALUES ($id, '$type_carte', '$numero_carte', '$nom_sur_carte', '$date_expiration', '$cvv')";
    }
    if (mysqli_query($db_handle, $sql)) {
        $b_message = '<div class="alert alert-success">Informations bancaires enregistrées !</div>';
    } else {
        $b_message = '<div class="alert alert-danger">Erreur lors de l\'enregistrement.</div>';
    }
}

// Gestion formulaire postal
$p_message = '';
if (isset($_POST['save_postal'])) {
    $nom = mysqli_real_escape_string($db_handle, $_POST['nom']);
    $prenom = mysqli_real_escape_string($db_handle, $_POST['prenom']);
    $adresse = mysqli_real_escape_string($db_handle, $_POST['adresse']);
    $ville = mysqli_real_escape_string($db_handle, $_POST['ville']);
    $code_postal = mysqli_real_escape_string($db_handle, $_POST['code_postal']);
    $pays = mysqli_real_escape_string($db_handle, $_POST['pays']);
    $num_tel = mysqli_real_escape_string($db_handle, $_POST['num_tel']);
    $check = mysqli_query($db_handle, "SELECT * FROM data_p WHERE id = $id");
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE data_p SET nom='$nom', prenom='$prenom', adresse='$adresse', ville='$ville', code_postal='$code_postal', pays='$pays', num_tel='$num_tel' WHERE id=$id";
    } else {
        $sql = "INSERT INTO data_p (id, nom, prenom, adresse, ville, code_postal, pays, num_tel) VALUES ($id, '$nom', '$prenom', '$adresse', '$ville', '$code_postal', '$pays', '$num_tel')";
    }
    if (mysqli_query($db_handle, $sql)) {
        $p_message = '<div class="alert alert-success">Informations postales enregistrées !</div>';
    } else {
        $p_message = '<div class="alert alert-danger">Erreur lors de l\'enregistrement.</div>';
    }
}

// Récupération des infos bancaires
$data_b = null;
if ($db_found) {
    $sql_b = "SELECT * FROM data_b WHERE id = $id";
    $result_b = mysqli_query($db_handle, $sql_b);
    if ($result_b && mysqli_num_rows($result_b) > 0) {
        $data_b = mysqli_fetch_assoc($result_b);
    }
}

// Récupération des infos postales
$data_p = null;
if ($db_found) {
    $sql_p = "SELECT * FROM data_p WHERE id = $id";
    $result_p = mysqli_query($db_handle, $sql_p);
    if ($result_p && mysqli_num_rows($result_p) > 0) {
        $data_p = mysqli_fetch_assoc($result_p);
    }
}

// Traitement des critères de recherche
if (isset($_POST['sauvegarder_criteres']) && isset($_SESSION['id'])) {
    $utilisateur_id = $_SESSION['id'];
    $mot_cle = isset($_POST['mot_cle']) ? trim($_POST['mot_cle']) : '';
    $prix_max = isset($_POST['prix_max']) ? floatval($_POST['prix_max']) : 0;
    $categorie = isset($_POST['categorie']) ? trim($_POST['categorie']) : '';

    $mot_cle_sql = mysqli_real_escape_string($db_handle, $mot_cle);
    $categorie_sql = mysqli_real_escape_string($db_handle, $categorie);
    $sql = "INSERT INTO criteres_recherche (utilisateur_id, mot_cle, prix_max, categorie)
            VALUES ($utilisateur_id, '$mot_cle_sql', $prix_max, '$categorie_sql')
            ON DUPLICATE KEY UPDATE mot_cle = VALUES(mot_cle), prix_max = VALUES(prix_max), categorie = VALUES(categorie)";
    if (mysqli_query($db_handle, $sql)) {
        $message_criteres = "<div class='alert alert-success text-center mt-3'>Critères enregistrés avec succès !</div>";
    } else {
        $message_criteres = "<div class='alert alert-danger text-center mt-3'>Erreur lors de l'enregistrement des critères.</div>";
    }
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
            margin-bottom: 32px;
        }
        .btn-lg {
            padding: 12px 24px;
        }
        .section-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 18px;
        }
    </style>
    <script>
    function toggleForm(id) {
        var f = document.getElementById(id);
        if (f.style.display === 'none' || f.style.display === '') f.style.display = 'block';
        else f.style.display = 'none';
    }
    </script>
</head>
<body>
    <!-- Logo -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation -->
    <?php include 'includes/navigation.php'; ?>

    <div class="container py-3">
        <!-- Encadré 1 : Infos personnelles -->
        <div class="card mx-auto p-4" style="max-width: 500px;">
            <div class="section-title">Informations personnelles</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($_SESSION['prenom']) ?></li>
                <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($_SESSION['nom']) ?></li>
                <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($_SESSION['email']) ?></li>
                <li class="list-group-item"><strong>Rôle :</strong> <?= htmlspecialchars($_SESSION['role']) ?></li>
            </ul>
        </div>

        <!-- Encadré 2 : Infos bancaires -->
        <div class="card mx-auto p-4" style="max-width: 500px;">
            <div class="section-title">Informations bancaires</div>
            <?= $b_message ?>
            <?php if ($data_b): ?>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item"><strong>Type de carte :</strong> <?= htmlspecialchars($data_b['type_carte']) ?></li>
                    <li class="list-group-item"><strong>Numéro de la carte :</strong> <?= htmlspecialchars($data_b['numero_carte']) ?></li>
                    <li class="list-group-item"><strong>Nom sur la carte :</strong> <?= htmlspecialchars($data_b['nom_sur_carte']) ?></li>
                    <li class="list-group-item"><strong>Date d'expiration :</strong> <?= htmlspecialchars($data_b['date_expiration']) ?></li>
                    <li class="list-group-item"><strong>Code de sécurité :</strong> <?= htmlspecialchars($data_b['cvv']) ?></li>
                </ul>
            <?php else: ?>
                <div class="text-muted mb-3">Aucune information bancaire enregistrée.</div>
            <?php endif; ?>
            <button class="btn btn-outline-primary w-100" onclick="toggleForm('form-bancaire')">Renseigner / Modifier</button>
            <form id="form-bancaire" method="post" style="display:none; margin-top:16px;">
                <div class="mb-2">
                    <label>Type de carte</label>
                    <select name="type_carte" class="form-control" required>
                        <option value="cb">CB</option>
                        <option value="paypal">PayPal</option>
                        <option value="applepay">Apple Pay</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Numéro de la carte</label>
                    <input type="text" name="numero_carte" class="form-control" maxlength="19" required>
                </div>
                <div class="mb-2">
                    <label>Nom sur la carte</label>
                    <input type="text" name="nom_sur_carte" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Date d'expiration</label>
                    <input type="text" name="date_expiration" class="form-control" placeholder="MM/AA" required>
                </div>
                <div class="mb-2">
                    <label>Code de sécurité</label>
                    <input type="text" name="cvv" class="form-control" maxlength="4" required>
                </div>
                <button type="submit" name="save_bancaire" class="btn btn-success w-100">Enregistrer</button>
            </form>
        </div>

        <!-- Encadré 3 : Infos postales -->
        <div class="card mx-auto p-4" style="max-width: 500px;">
            <div class="section-title">Informations postales</div>
            <?= $p_message ?>
            <?php if ($data_p): ?>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($data_p['nom']) ?></li>
                    <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($data_p['prenom']) ?></li>
                    <li class="list-group-item"><strong>Adresse :</strong> <?= htmlspecialchars($data_p['adresse']) ?></li>
                    <li class="list-group-item"><strong>Ville :</strong> <?= htmlspecialchars($data_p['ville']) ?></li>
                    <li class="list-group-item"><strong>Code postal :</strong> <?= htmlspecialchars($data_p['code_postal']) ?></li>
                    <li class="list-group-item"><strong>Pays :</strong> <?= htmlspecialchars($data_p['pays']) ?></li>
                    <li class="list-group-item"><strong>Numéro de téléphone :</strong> <?= htmlspecialchars($data_p['num_tel']) ?></li>
                </ul>
            <?php else: ?>
                <div class="text-muted mb-3">Aucune information postale enregistrée.</div>
            <?php endif; ?>
            <button class="btn btn-outline-primary w-100" onclick="toggleForm('form-postal')">Renseigner / Modifier</button>
            <form id="form-postal" method="post" style="display:none; margin-top:16px;">
                <div class="mb-2">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Prénom</label>
                    <input type="text" name="prenom" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Adresse</label>
                    <input type="text" name="adresse" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Ville</label>
                    <input type="text" name="ville" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Code postal</label>
                    <input type="text" name="code_postal" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Pays</label>
                    <input type="text" name="pays" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Numéro de téléphone</label>
                    <input type="text" name="num_tel" class="form-control" required>
                </div>
                <button type="submit" name="save_postal" class="btn btn-success w-100">Enregistrer</button>
            </form>
        </div>
        <?= $message_criteres ?>

        <!-- Encadré 4 : Critères de recherche -->
        <div class="card shadow mt-5 p-4">
            <h4 class="mb-3 text-center text-primary">🔎 Sauvegarder vos critères de recherche</h4>
            <form action="espace_acheteur.php" method="post">
                <div class="mb-3">
                    <label for="mot_cle" class="form-label">Mot-clé (ex : SUV, Tesla, électrique)</label>
                    <input type="text" name="mot_cle" id="mot_cle" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="prix_max" class="form-label">Prix maximum (€)</label>
                    <input type="number" name="prix_max" id="prix_max" class="form-control" min="1" step="1">
                </div>
                <div class="mb-3">
                    <label for="categorie" class="form-label">Catégorie</label>
                    <select name="categorie" id="categorie" class="form-control" required>
                        <option value="">-- Choisir --</option>
                        <option value="suv">SUV</option>
                        <option value="berline">Berline</option>
                        <option value="sportive">Sportive</option>
                    </select>
                </div>
                <button type="submit" name="sauvegarder_criteres" class="btn btn-outline-success w-100">
                    Enregistrer les critères
                </button>
            </form>
        </div>

        <!-- Encadré 5 : Déconnexio et retour menu -->
        <div class="card mx-auto p-3" style="max-width: 500px;">
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-outline-primary btn-custom">🏠 Retour à l'accueil</a>
                <a href="deconnexion.php" class="btn btn-danger btn-custom">🔓 Se déconnecter</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
</body>
</html>
