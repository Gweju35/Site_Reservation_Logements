<?php
session_start();
print_r($_POST);
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
//$logementId = $_SESSION['logement_id']; // Assurez-vous d'adapter cela à votre logique
$logementId =  $_POST['logement_id'];
// Préparation et exécution de la requête SQL d'insertion
$query = "INSERT INTO alhaiz_breizh._calendrier (calendrier_date_jour, calendrier_logement_id, calendrier_motif_indisponibilite) VALUES (:date_jour, :logementId, 'Raison personnelle')";

try {
    foreach ($dateRange as $date) {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':date_jour', $date);
        $stmt->bindParam(':logementId', $logementId);
        $stmt->execute();
    }
    // Retourner une réponse au client (vous pouvez également retourner d'autres informations si nécessaire)
    echo json_encode(['success' => true, 'message' => 'Période d\'indisponibilité ajoutée avec succès à la base de données.']);
} catch (PDOException $e) {
    // En cas d'erreur, retourner un message d'erreur au client
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la période d\'indisponibilité à la base de données: ' . $e->getMessage()]);
}
?>