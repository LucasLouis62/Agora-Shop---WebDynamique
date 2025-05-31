<?php
// Démarrage de la session utilisateur
session_start();
// Inclusion de la connexion PDO à la base de données
require_once 'config/connexion.php';

// Récupération de l'ID du produit à ajouter au panier depuis l'URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Si l'ID est valide
if ($id > 0) {
    // Récupération des informations du produit depuis la base
    $stmt = $bdd->prepare("SELECT id, titre, description, image, prix FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produit) {
        // Initialisation du panier si besoin
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        $déjàDansPanier = false;

        // Vérifie si le produit est déjà dans le panier
        foreach ($_SESSION['panier'] as &$article) {
            if ($article['id'] == $produit['id']) {
                // Si oui, on incrémente la quantité
                $article['quantite'] += 1;
                $déjàDansPanier = true;
                break;
            }
        }

        // Si le produit n'est pas encore dans le panier, on l'ajoute
        if (!$déjàDansPanier) {
            $_SESSION['panier'][] = [
                'id' => $produit['id'],
                'nom' => $produit['titre'],
                'description' => $produit['description'],
                'image' => $produit['image'],
                'prix' => $produit['prix'],
                'quantite' => 1
            ];
        }

        // Message de confirmation temporaire (affiché sur la page panier)
        $_SESSION['message_panier'] = "« {$produit['titre']} » a été ajouté au panier.";
    }
}

// Redirection vers la page panier
header('Location: panier.php');
exit();
