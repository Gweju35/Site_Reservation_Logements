<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['nom_titulaire'], $_POST['num_carte'], $_POST['date_expiration'], $_POST['cvv'], $_POST['accepte_conditions'])) {

        // Récupérer les données du formulaire
        
        $nom_titulaire = $_POST['nom_titulaire'];
        $num_carte = $_POST['num_carte'];
        $date_expiration = $_POST['date_expiration'];
        $cvv = $_POST['cvv'];
        $accepte_conditions = isset($_POST['accepte_conditions']) ? true : false;


        // Établir une connexion à la base de données
        include('../../libs/connect_params.php');
        try {
            $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query2 = "SELECT devis_id_logement from alhaiz_breizh._devis where devis_id = :devis_id";
            $stmt = $pdo->prepare($query2);
            $stmt->bindParam(':devis_id', $_SESSION['devis_id'], PDO::PARAM_INT);
            $stmt->execute();

            $logement_id = $stmt->fetchColumn();
            // Obtenez l'ID du client connecté
            $client_id = $_SESSION['user_id'];

            // Créez une réservation

            $devis_id = $_SESSION['devis_id'];
            $reservation_acceptation_cgv = $accepte_conditions;
            $reservation_facture = "FACT-" . date("YmdHis"); // Générez un numéro de facture unique

            $query = "INSERT INTO alhaiz_breizh._reservation (reservation_logement_id, reservation_devis_id, reservation_acceptation_cgv, reservation_facture) 
                      VALUES (:logement_id, :devis_id, :acceptation_cgv, :facture)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':logement_id', $logement_id, PDO::PARAM_INT);
            $stmt->bindParam(':devis_id', $devis_id, PDO::PARAM_INT);
            $stmt->bindParam(':acceptation_cgv', $reservation_acceptation_cgv, PDO::PARAM_BOOL);
            $stmt->bindParam(':facture', $reservation_facture, PDO::PARAM_STR);
            $stmt->execute();

            // Mettez à jour le statut du devis
            $nouveau_statut = 'PPC'; // Nouveau statut
            $date_validation = date("Y-m-d"); // Date de validation

            $update_query = "UPDATE alhaiz_breizh._devis SET devis_statut = :nouveau_statut, devis_date_validation = :date_validation WHERE devis_id = :devis_id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->bindParam(':nouveau_statut', $nouveau_statut, PDO::PARAM_STR);
            $update_stmt->bindParam(':date_validation', $date_validation, PDO::PARAM_STR);
            $update_stmt->bindParam(':devis_id', $devis_id, PDO::PARAM_INT);
            $update_stmt->execute();

            header('Location: ../index.php');
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    } 
    else {
        echo "Tous les champs du formulaire sont requis.";
    }

} else {
    echo "Requête non autorisée.";
}
