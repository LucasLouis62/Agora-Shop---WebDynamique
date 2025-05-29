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
if (isset($_POST['save_data_p'])) {
    $type_carte = mysqli_real_escape_string($db_handle, $_POST['type_carte']);
    $numero_carte = mysqli_real_escape_string($db_handle, $_POST['numero_carte']);
    $nom_sur_carte = mysqli_real_escape_string($db_handle, $_POST['nom_sur_carte']);
    $date_expiration = mysqli_real_escape_string($db_handle, $_POST['date_expiration']);
    $cvv = mysqli_real_escape_string($db_handle, $_POST['cvv']);
    // Vérifie si déjà existant
    $check = mysqli_query($db_handle, "SELECT * FROM data_p WHERE utilisateurs = $id");
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE data_p SET type_carte='$type_carte', numero_carte='$numero_carte', nom_sur_carte='$nom_sur_carte', date_expiration='$date_expiration', cvv='$cvv' WHERE id_utilisateur=$id";
    } else {
        $sql = "INSERT INTO data_p (id_utilisateur, type_carte, numero_carte, nom_sur_carte, date_expiration, cvv) VALUES ($id, '$type_carte', '$numero_carte', '$nom_sur_carte', '$date_expiration', '$cvv')";
    }
    if (mysqli_query($db_handle, $sql)) {
        $b_message = '<div class="alert alert-success">Informations bancaires enregistrées !</div>';
    } else {
        $b_message = '<div class="alert alert-danger">Erreur lors de l\'enregistrement.</div>';
    }
}

// Gestion formulaire postal
$p_message = '';
if (isset($_POST['save_data_b'])) {
    $nom = mysqli_real_escape_string($db_handle, $_POST['nom']);
    $prenom = mysqli_real_escape_string($db_handle, $_POST['prenom']);
    $adresse = mysqli_real_escape_string($db_handle, $_POST['adresse']);
    $ville = mysqli_real_escape_string($db_handle, $_POST['ville']);
    $code_postal = mysqli_real_escape_string($db_handle, $_POST['code_postal']);
    $pays = mysqli_real_escape_string($db_handle, $_POST['pays']);
    $num_tel = mysqli_real_escape_string($db_handle, $_POST['num_tel']);
    $check = mysqli_query($db_handle, "SELECT * FROM data_b WHERE utilisateurs = $id");
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE data_b SET nom='$nom', prenom='$prenom', adresse='$adresse', ville='$ville', code_postal='$code_postal', pays='$pays', num_tel='$num_tel' WHERE id_utilisateur=$id";
    } else {
        $sql = "INSERT INTO data_b (id_utilisateur, nom, prenom, adresse, ville, code_postal, pays, num_tel) VALUES ($id, '$nom', '$prenom', '$adresse', '$ville', '$code_postal', '$pays', '$num_tel')";
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
    $sql_b = "SELECT * FROM data_p";
    $result_b = mysqli_query($db_handle, $sql_b);
    if ($result_b && mysqli_num_rows($result_b) > 0) {
        $data_b = mysqli_fetch_assoc($result_b);
    }
}

// Récupération des infos postales
$data_p = null;
if ($db_found) {
    $sql_p = "SELECT * FROM data_b";
    $result_p = mysqli_query($db_handle, $sql_p);
    if ($result_p && mysqli_num_rows($result_p) > 0) {
        $data_p = mysqli_fetch_assoc($result_p);
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
<div class="container my-4 p-4 bg-white shadow rounded">
    <header class="text-center mb-4">
        <img src="images/logo_agora.png" alt="Logo Agora Francia" width="200" class="img-fluid">
    </header>

    <nav class="navbar navbar-expand justify-content-center mb-4">
        <div class="navbar-nav gap-2">
            <a class="btn btn-primary" href="index.php">Accueil</a>
            <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
            <a class="btn btn-primary" href="notifications.php">Notifications</a>
            <a class="btn btn-primary" href="panier.php">Panier</a>
            <a class="btn btn-primary" href="<?= isset($_SESSION['id']) ? 'compte.php' : 'votrecompte.php' ?>">Votre compte</a>
        </div>
    </nav>

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
        <div class="card mx-auto p-3" style="max-width: 500px;">
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-outline-primary btn-custom">🏠 Retour à l'accueil</a>
                <a href="deconnexion.php" class="btn btn-danger btn-custom">🔓 Se déconnecter</a>
            </div>
        </div>
    </div>

    <footer class="row text-center text-md-start align-items-center mt-5">
        <div class="col-md-4 mb-3 mb-md-0">
            <h5>Contact</h5>
            <p>Email : <a href="mailto:agora.francia@gmail.com">agora.francia@gmail.com</a></p>
            <p>Téléphone : 01 23 45 67 89</p>
            <p>Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <p>&copy; 2025 Agora Francia</p>
        </div>
        <div class="col-md-4">
            <h5>Nous trouver</h5>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.8878757609433!2d2.2847854156752096!3d48.850725779286154!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1685534176532!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="220" height="120" style="border:0; border-radius:8px;"></iframe>
        </div>
    </footer>
</div>
</body>
</html>
