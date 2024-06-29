<?php
session_start();

// Inclure le fichier connectParam.php pour les informations de connexion à la base de données
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    print_r($_POST);
    

    // Requête SQL pour récupérer tous les devis du propriétaire
    $sql = "UPDATE alhaiz_breizh._avis SET avis_note = :note, avis_contenue = :commentaire WHERE avis_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':note', $_POST['rating']);
    $stmt->bindParam(':commentaire', $_POST['commentaire']);
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->execute();

    /*
    echo '<script>window.history.back();</script>';
    exit();
    */

}     catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
?>