<?php
session_start();
// Connexion à la base de données
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Récupération des données envoyées depuis le client
$startString = $_POST['start'];
$endString = $_POST['end'];


$start = new DateTime($startString);
$end = new DateTime($endString);

$interval = new DateInterval('P1D'); // P1D représente une période d'un jour

$dateRange = array();

while ($start < $end) {
    $dateRange[] = $start->format('Y-m-d');
    $start->add($interval);
}

print_r($dateRange);





// Récupération des autres données nécessaires à partir de la session ou de toute autre source
$logementId =  $_POST['logement_id']; // Assurez-vous d'adapter cela à votre logique

// Préparation et exécution de la requête SQL de suppression
$query = "DELETE FROM alhaiz_breizh._periode_prix WHERE periode_jour_date= :date_jour AND periode_logement_id = :logementId;";

try {
    foreach ($dateRange as $date) {
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':date_jour', $date);
    $stmt->bindParam(':logementId', $logementId);
    $stmt->execute();
    }

    // Retourner une réponse au client (vous pouvez également retourner d'autres informations si nécessaire)
    echo json_encode(['success' => true, 'message' => 'Période de prix supprimée avec succès de la base de données.']);
} catch (PDOException $e) {
    // En cas d'erreur, retourner un message d'erreur au client
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la période de prix dans la base de données: ' . $e->getMessage()]);
}
?>