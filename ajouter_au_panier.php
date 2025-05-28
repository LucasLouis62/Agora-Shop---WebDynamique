<?php
session_start();
require_once 'config/connexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = $bdd->prepare("SELECT id, titre, description, image, prix FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produit) {
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        $déjàDansPanier = false;

        foreach ($_SESSION['panier'] as &$article) {
            if ($article['id'] == $produit['id']) {
                $article['quantite'] += 1;
                $déjàDansPanier = true;
                break;
            }
        }

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

        // Stocker un message de confirmation temporaire (optionnel)
        $_SESSION['message_panier'] = "« {$produit['titre']} » a été ajouté au panier.";
    }
}

// Rediriger vers panier
header('Location: panier.php');
exit();
