<?php 
session_start();
include('../../libs/connect_params.php');
try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO alhaiz_breizh._abonnement( abonnement_id_compte, abonnement_logement_id, abonnement_cle, abonnement_date_deb, abonnement_date_fin, abonnement_reservation, abonnement_devis, abonnement_raison_perso) VALUES (:id_compte, :logement_id,:cle_abo,:date_deb,:date_fin,:resa,:devis,:motif_perso);";
    
    $stmt = $pdo->prepare($sql);

    $abonnement_key = uniqid('', true);
    $stmt->bindParam(':id_compte', $_SESSION['user_id']);
    $stmt->bindParam(':logement_id', $_POST['choixLogement_abo']);
    $stmt->bindParam(':cle_abo', $abonnement_key);
    $stmt->bindParam(':date_deb', $_POST['date_debut']);
    $stmt->bindParam(':date_fin', $_POST['date_fin']);
    $stmt->bindValue(':resa', isset($_POST['checkbox1']) ? true : false, PDO::PARAM_BOOL);
    $stmt->bindValue(':devis', isset($_POST['checkbox2']) ? true : false, PDO::PARAM_BOOL);
    $stmt->bindValue(':motif_perso', isset($_POST['checkbox3']) ? true : false, PDO::PARAM_BOOL);
    $stmt->execute();

    $json_data = json_encode(array('success' => true, 'res' => $abonnement_key), JSON_PRETTY_PRINT);
    echo $json_data;



} catch (PDOException $e) {
    // En cas d'erreur, retourner une réponse d'échec
    $json_error = json_encode(array('success' => false, 'error' => $e->getMessage()));
    echo $json_error;
} 
?>