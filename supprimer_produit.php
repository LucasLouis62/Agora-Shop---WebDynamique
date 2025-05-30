<?php
session_start();
require_once 'config/connexion.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'vendeur') {
    header("Location: connexion.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Produit introuvable.";
    exit;
}

$produit_id = intval($_GET['id']);
$id_vendeur = $_SESSION['id'];

// Suppression sécurisée du produit
$stmt = $bdd->prepare("DELETE FROM produits WHERE id = ? AND id_vendeur = ?");
$stmt->execute([$produit_id, $id_vendeur]);

header("Location: espace_vendeur.php");
exit;
?>
