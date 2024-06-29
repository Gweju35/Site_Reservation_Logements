<?php
session_start();
$client_id = $_SESSION['user_id']; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclure le fichier connectParam.php pour les informations de connexion à la base de données
    include('../../libs/connect_params.php');

    try {
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les réservations depuis la base de données
        $query = "SELECT r.*, l.*, d.*
                  FROM alhaiz_breizh._reservation AS r
                  JOIN alhaiz_breizh._logement AS l ON r.reservation_logement_id = l.logement_id
                  JOIN alhaiz_breizh._devis AS d ON r.reservation_devis_id = d.devis_id  where d.devis_id_compte_client = " . $_SESSION['user_id'] . " ";
        $stmt = $pdo->query($query);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les valeurs du formulaire
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id']; 
        }

        // Définir la fonction de comparaison pour le tri
        function trier($logement1, $logement2) {
            if ($_POST['tri'] == 'croissant') {
                $prix1 = $logement1['devis_prix_ttc'];
                $prix2 = $logement2['devis_prix_ttc'];
                return ($prix1 < $prix2) ? -1 : 1;
            } elseif ($_POST['tri'] == 'decroissant') {
                $prix1 = $logement1['devis_prix_ttc'];
                $prix2 = $logement2['devis_prix_ttc'];
                return ($prix1 > $prix2) ? -1 : 1;
            }elseif ($_POST['tri'] == 'vieux') {
                $date1 = strtotime($logement1['devis_date_debut']);
                $date2 = strtotime($logement2['devis_date_debut']);
                return ($date1 > $date2) ? -1 : 1;
            } elseif ($_POST['tri'] == 'recent') {
                $date1 = strtotime($logement1['devis_date_debut']);
                $date2 = strtotime($logement2['devis_date_debut']);
                return ($date1 < $date2) ? -1 : 1;
            } else {
                
                // Aucun tri sélectionné, pas de changement dans l'ordre
                return 0;
            }
        }

        // Appliquer le tri
        if (isset($_POST['tri'])) {
            usort($reservations, 'trier');
        }
        $_SESSION['reservations']=$reservations;

        // Redirection vers la page d'accueil pour afficher les résultats filtrés
        header("Location: ../Reservations/reservation_client.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}
?>
