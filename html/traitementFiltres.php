<?php
include('../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les logements du propriétaire
    $sql = "SELECT * FROM alhaiz_breizh._logement WHERE logement_statut_ligne = true";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();


    $logements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    
    $sql = "SELECT * FROM alhaiz_breizh._avis  ";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute();

    $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
    
    $json_data = json_encode(array('success' => true, 'events' => $logements, 'avis' => $avis), JSON_PRETTY_PRINT);
    echo $json_data;
} catch (PDOException $e) {
    // En cas d'erreur, retourner une réponse d'échec
    $json_error = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_error;
}
?>
 

 