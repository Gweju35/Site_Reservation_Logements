<?php
session_start();

// Inclure le fichier connectParam.php pour les informations de connexion à la base de données
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour récupérer tous les devis du propriétaire
    $sql = "INSERT INTO alhaiz_breizh._avis (avis_contenue, avis_date_post, avis_signalement, avis_id_compte_client, avis_id_compte_proprietaire, avis_id_logement, avis_note)
    VALUES
        (:commentaire, CURRENT_DATE, false, :compte_id, :proprio_id, :logement_id, :note) ;" ;
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':commentaire', $_POST['commentaire']);
    $stmt->bindParam(':compte_id', $_POST['client_id']);
    $stmt->bindParam(':logement_id', $_POST['logement_id']);
    $stmt->bindParam(':note', $_POST['rating']);
    $stmt->bindParam(':proprio_id', $_POST['proprio_id']);
    $stmt->execute();

    // Redirection vers la page d'accueil pour afficher les résultats filtrés
    header("Location: ../Reservations/reservation_client.php");
    exit();

}     catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
