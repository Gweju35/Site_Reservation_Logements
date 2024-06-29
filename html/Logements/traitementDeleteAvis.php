<?php
session_start();

// Inclure le fichier connectParam.php pour les informations de connexion à la base de données
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour récupérer tous les devis du propriétaire
    $sql = "DELETE FROM alhaiz_breizh._avis WHERE avis_id = :id" ;
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_POST['avis_id']);
    $stmt->execute();

    echo '<script>window.history.back();</script>';
    exit();

}     catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
