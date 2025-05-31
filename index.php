<?php
// Démarrage de la session utilisateur
session_start();

// Connexion à la base de données 'agora'
$database = "agora";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);
$carrousel_annonces = [];

if ($db_found) {
    // Suppression automatique des annonces dont l'enchère est terminée (plus de 72h)
    $now = date('Y-m-d H:i:s');
    $sql_delete = "DELETE FROM produits WHERE type_vente = 'enchere' AND DATE_ADD(date_ajout, INTERVAL 72 HOUR) < '$now'";
    mysqli_query($db_handle, $sql_delete);

    // Sélectionner 5 annonces au hasard pour le carrousel
    $sql = "SELECT id, titre, image FROM produits ORDER BY RAND() LIMIT 5";
    $result = mysqli_query($db_handle, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $carrousel_annonces[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agora Francia – Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Styles pour carrousel -->
    <style>
        body {
            background: #f8fbfd;
            font-family: 'Segoe UI', sans-serif;
        }
        .carrousel img {
            display: none;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .carrousel img.active {
            display: block;
        }
        .welcome {
            font-size: 1.2rem;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 20px;
        }
        .btn-carrousel {
            border-radius: 50%;
            width: 48px;
            height: 48px;
            font-size: 1.4rem;
            background-color: #007bff;
            color: white;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .btn-carrousel:hover {
            transform: scale(1.1);
        }
        .map-container iframe {
            width: 100%;
            height: 150px;
            border: none;
            border-radius: 8px;
        }
    </style>
    <!-- Scripte js pour carrousel -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const images = document.querySelectorAll(".carrousel img");
            let index = 0;
            if(images.length > 0) images[index].classList.add("active");
            document.querySelector(".prev").addEventListener("click", () => {
                images[index].classList.remove("active");
                index = (index - 1 + images.length) % images.length;
                images[index].classList.add("active");
            });
            document.querySelector(".next").addEventListener("click", () => {
                images[index].classList.remove("active");
                index = (index + 1) % images.length;
                images[index].classList.add("active");
            });
        });
    </script>
</head>

<body>
    <!-- Logo -->
    <?php include 'includes/header.php'; ?>

    <!-- Barre de navigation -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Paragraphe d'introduction d'Agora Shop -->
    <div class="d-flex justify-content-center">
        <p class="lead text-center" style="max-width:700px; text-align:justify;">
            Bienvenue sur <strong>Agora Francia</strong>, la plateforme innovante de vente de véhicules neufs et d’occasion. 
            Achetez, vendez, négociez ou enchérissez en toute simplicité et sécurité !
        </p>
    </div>

    <!-- Message de bienvenue si utilisateur connecté -->
    <?php if (isset($_SESSION['prenom'])) : ?>
        <p class="text-center welcome">Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?> !</p>
    <?php endif; ?>

        <!-- Section principale -->
        <main class="text-center mb-4">
            <h2 class="mb-4">🌟 Sélection du jour</h2>
            <!-- Code carroussel TP4 -->
            <div class="carrousel mx-auto" style="max-width:500px;">
                <?php foreach ($carrousel_annonces as $annonce): ?>
                    <a href="annonce.php?id=<?= $annonce['id'] ?>">
                        <img src="<?= htmlspecialchars($annonce['image']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>" width=500px height=500px>
                    </a>
                <?php endforeach; ?>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <button class="btn-carrousel prev">⬅️</button>
                    <button class="btn-carrousel next">➡️</button>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>