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

        $sql = "SELECT devis_statut FROM alhaiz_breizh._devis  WHERE devis_id = :devis_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':devis_id', $devis_id);
        $stmt->execute();

        $statut = $stmt->fetch(PDO::FETCH_ASSOC);
        // BIG PROBLEME POUR FAIRE LE CHANGEMENT DE STATUT

        // Redirigez l'utilisateur vers la page des devis après la prise de l'information 
            // Changement d'etat pour gérer l'affichahe selon qui a vu le devis exp
        if($statut['devis_statut'] == 'SUPR_P' || $statut['devis_statut'] == 'SUPR_C'){
            $sql = "UPDATE alhaiz_breizh._devis SET devis_statut = 'SUPR' WHERE devis_id = :devis_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':devis_id', $devis_id);
            $stmt->execute();

            if($_SESSION['user_type']  == 'client'){
                header("Location: ../Devis/devis_du_client.php");
            } else {
                header("Location: ../Devis/devis_du_proprio.php");
            }

        } else if($_SESSION['user_type']  == 'client'){
            $sql = "UPDATE alhaiz_breizh._devis SET devis_statut = 'SUPR_C' WHERE devis_id = :devis_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':devis_id', $devis_id);
            $stmt->execute();

            header("Location: ../Devis/devis_du_client.php");
        } else {
            $sql = "UPDATE alhaiz_breizh._devis SET devis_statut = 'SUPR_C' WHERE devis_id = :devis_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':devis_id', $devis_id);
            $stmt->execute();

            header("Location: ../Devis/devis_du_proprio.php");
        }
        exit();
        
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}
?>