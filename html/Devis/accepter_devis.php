<?php
session_start();
include('../../libs/connect_params.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['devis_id'])) {
    // Récupérez l'ID du devis depuis le formulaire
    $devis_id = $_POST['devis_id'];
    // Connectez-vous à la base de données (inclusion du fichier connect_params.php)
    try {
        // Exécutez une requête SQL pour mettre à jour le statut du devis
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /* 
        // Obtenir la date du jour
        $dateActuelle = new DateTime();
        $dateDuJour = $dateActuelle->format('Y-m-d H:i');
        
        // REEL DELAIS 
                // Obtenir la date du jour
        $dateActuelle = new DateTime();
        
        // Calculer la date dans trois jours, 4 heures et 50 minutes
        $new_date = $dateActuelle->add(new DateInterval('P3DT4H50M'))->format('Y-m-d H:i');

        // Convertir la nouvelle date en timestamp
        $newTimestamp = strtotime($new_date);
        */

        //EXEMPLE DE TEST / REVIEW
        $timestamp = time(); // Obtenez le timestamp actuel

        $newTimestamp = $timestamp + 60;
        
        
        $sql = "UPDATE alhaiz_breizh._devis SET devis_statut = 'APP', devis_date_expiration_v = :date_attribut WHERE devis_id = :devis_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':devis_id', $devis_id);
        $stmt->bindParam(':date_attribut', $newTimestamp, PDO::PARAM_INT); 
        $stmt->execute();        

        // Redirigez l'utilisateur vers la page des devis du propriétaire après l'acceptation
        header("Location: ../Devis/devis_du_proprio.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}
?>