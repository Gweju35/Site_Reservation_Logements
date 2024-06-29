<?php
session_start();
include('../../libs/connect_params.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['devis_id'])) {
    // Récupérez l'ID du devis depuis le formulaire
    $devis_id = $_POST['devis_id'];

    // Connectez-vous à la base de données (inclusion du fichier connect_params.php)

    try {
        // Exécutez une requête SQL pour supprimer le devis
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "DELETE FROM alhaiz_breizh._devis WHERE devis_id = :devis_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':devis_id', $devis_id);
        $stmt->execute();

        // Redirigez l'utilisateur vers la page des devis du propriétaire après le refus
        if ($_SESSION['user_type'] === 'proprietaire') {
            header("Location: ../Devis/devis_du_proprio.php");
        } else if ($_SESSION['user_type'] === "client") {
            header("Location: ../Devis/devis_du_client.php");
        }
    
        
        
        exit();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}
?>
