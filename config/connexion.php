<?php
    $host = 'localhost';
    $dbname = 'agora';
    $user = 'root';
    $password = '';

    try {
        $bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }
?>