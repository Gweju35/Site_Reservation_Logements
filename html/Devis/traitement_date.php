<?php 
    include("../../libs/connect_params.php");;
    session_start();
    $logement_id = $_SESSION['logement_id'];

    try {
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Récupérer les logements du propriétaire
        $sql = "SELECT calendrier_date_jour, deb_fin FROM alhaiz_breizh._calendrier WHERE calendrier_logement_id = $logement_id ORDER BY calendrier_date_jour;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    
        $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $events = array();

        if(isset($dates)){
            foreach ($dates as $date) {
                $event = array(
                    'start' => $date['calendrier_date_jour'],
                    'etat' => $date['deb_fin']
                );
                $events[] = $event;
            }
        }
        $json_data = json_encode(array('success' => true, 'events' => $events), JSON_PRETTY_PRINT);
        echo $json_data;
    } catch (PDOException $e) { 
        $json_data = json_encode(array('success' => false, 'error' => $e->getMessage()));
        echo $json_data;
    }
?>