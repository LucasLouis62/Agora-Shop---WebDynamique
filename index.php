<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agora Francia – Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const images = document.querySelectorAll(".carrousel img");
            let index = 0;
            images[index].classList.add("active");

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
    <div class="container my-4 p-4 border rounded shadow bg-white">
        <!-- Logo -->
        <header class="text-center mb-4">
            <img src="images/logo_agora.png" alt="Logo Agora Francia" width="220" class="img-fluid">
        </header>

        <!-- Barre de navigation -->
        <nav class="navbar navbar-expand justify-content-center mb-4">
            <div class="navbar-nav gap-2">
                <a class="btn btn-primary" href="index.php">Accueil</a>
                <a class="btn btn-primary" href="toutparcourir.php">Tout Parcourir</a>
                <a class="btn btn-primary" href="notifications.php">Notifications</a>
                <a class="btn btn-primary" href="panier.php">Panier</a>
                <?php if (isset($_SESSION['id'])) : ?>
                    <a class="btn btn-success" href="compte.php">👤 Mon compte</a>
                <?php else : ?>
                    <a class="btn btn-primary" href="votrecompte.php">Votre compte</a>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Message de bienvenue -->
        <?php if (isset($_SESSION['prenom'])) : ?>
            <p class="text-center welcome">Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?> !</p>
        <?php endif; ?>

        <!-- Section principale -->
        <main class="text-center mb-4">
            <h2 class="mb-4">🌟 Sélection du jour</h2>
            <div class="carrousel mx-auto" style="max-width:500px;">
                <img src="images/alpine.jpg" class="img-fluid">
                <img src="images/ferrari.jpg" class="img-fluid">
                <img src="images/koenigsegg.jpg" class="img-fluid">
                <img src="images/lambo.jpg" class="img-fluid">
                <img src="images/porsche.jpg" class="img-fluid">
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <button class="btn-carrousel prev">⬅️</button>
                    <button class="btn-carrousel next">➡️</button>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-5">
            <div class="row text-center text-md-start align-items-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>Contact</h5>
                    <p>Email : <a href="mailto:agora.francia@gmail.com">agora.francia@gmail.com</a></p>
                    <p>Téléphone : 01 23 45 67 89</p>
                    <p>Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <p class="mb-0">&copy; 2025 Agora Francia</p>
                </div>
                <div class="col-md-4">
                    <h5>Nous trouver</h5>
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.8878757609433!2d2.2847854156752096!3d48.850725779286154!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1685534176532!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
