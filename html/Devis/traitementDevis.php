<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Inclure le fichier connectParam.php pour les informations de connexion à la base de données
    include('../../libs/connect_params.php');
    
    session_start();
    try {
        // Connexion à la base de données avec PDO
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les valeurs du formulaire
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id']; 
        }
        date_default_timezone_set('UTC'); 



        $dateDuJour = date('Y-m-d');
        
        $dateArrtemp  = str_replace('/','-',$_POST['dateArr']);
        $dateArrtemp2 = DateTime::createFromFormat('d-m-Y',$dateArrtemp); // Remplacez par la valeur appropriée
        $dateArr = $dateArrtemp2->format('Y-m-d');

        $dateDeptemp  = str_replace('/','-',$_POST['dateDep']);
        $dateDeptemp2 = DateTime::createFromFormat('d-m-Y',$dateDeptemp); // Remplacez par la valeur appropriée
        $dateDep = $dateDeptemp2->format('Y-m-d');

        
        $startDate = new DateTime($dateArr);

        // Créer un objet DateTime pour la date de fin
        $endDate = new DateTime($dateDep);

        // Ajouter un jour à la date de fin pour inclure cette date dans la période
        $endDate->modify('+1 day');

        // Créer une période entre la date de départ et la date de fin avec un intervalle d'un jour
        $dateInterval = new DateInterval('P1D'); // P1D signifie une période de 1 jour
        $dateRange    = new DatePeriod($startDate, $dateInterval, $endDate);

        //Recuperer les prix par nuit selon périodes
        $stmt = $pdo->prepare("SELECT periode_jour_prix  FROM alhaiz_breizh._periode_prix WHERE periode_logement_id = :logement_id AND periode_jour_date BETWEEN :date_debut AND :date_fin;");
        $stmt->bindParam(':date_debut', $dateArr, PDO::PARAM_STR);
        $stmt->bindParam(':date_fin', $dateDep, PDO::PARAM_STR);
        $stmt->bindParam(':logement_id', $_SESSION['logement_id'], PDO::PARAM_INT);
        $stmt->execute();

        $date_prix_periode  = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialiser un tableau pour stocker les dates
        $dateArray = array();

        // Parcourir la période et stocker chaque date dans le tableau
        foreach ($dateRange as $date) {
            $dateArray[] = $date->format('Y-m-d');
        }

        // Maintenant, $dateArray contient toutes les dates entre la date d'arrivée et la date de départ
        $raison = 'Devis';
        $debut = 'debut';
        $millieu = 'millieu';
        $fin = 'fin';
        foreach ($dateArray as $date){
            if($date == $dateArr){

                $stmt = $pdo->prepare("INSERT INTO alhaiz_breizh._calendrier ( calendrier_date_jour, calendrier_logement_id, calendrier_motif_indisponibilite,deb_fin) VALUES (:date_jour, :logement_id, :motif_indisponibilite,:deb_fin)");
                
                $stmt->bindParam(':date_jour', $date, PDO::PARAM_STR);
                $stmt->bindParam(':logement_id', $_SESSION['logement_id'], PDO::PARAM_INT);
                $stmt->bindParam(':motif_indisponibilite',$raison);
                $stmt->bindParam(':deb_fin',$debut);
                
                $stmt->execute();

            }elseif($date == $dateDep){
    
                $stmt = $pdo->prepare("INSERT INTO alhaiz_breizh._calendrier ( calendrier_date_jour, calendrier_logement_id, calendrier_motif_indisponibilite,deb_fin) VALUES (:date_jour, :logement_id, :motif_indisponibilite,:deb_fin)");
                
                $stmt->bindParam(':date_jour', $date, PDO::PARAM_STR);
                $stmt->bindParam(':logement_id', $_SESSION['logement_id'], PDO::PARAM_INT);
                $stmt->bindParam(':motif_indisponibilite',$raison);
                $stmt->bindParam(':deb_fin',$fin);
                
                $stmt->execute();

            }else{
                $stmt = $pdo->prepare("INSERT INTO alhaiz_breizh._calendrier ( calendrier_date_jour, calendrier_logement_id, calendrier_motif_indisponibilite,deb_fin) VALUES (:date_jour, :logement_id, :motif_indisponibilite,:deb_fin)");
                
                $stmt->bindParam(':date_jour', $date, PDO::PARAM_STR);
                $stmt->bindParam(':logement_id', $_SESSION['logement_id'], PDO::PARAM_INT);
                $stmt->bindParam(':motif_indisponibilite',$raison);
                $stmt->bindParam(':deb_fin',$millieu);
                
                $stmt->execute();   
            }
        }



        $nb_nuit = $dateDeptemp2->diff($dateArrtemp2);

        
        $prix_nuit = $_SESSION['logement_prix_nuit_base'] * $nb_nuit->format('%a');

        $devis_prix_ht = 0;
        
        if(isset($date_prix_periode)){
            $devis_prix_ht = $prix_nuit-($_SESSION['logement_prix_nuit_base']*sizeof($date_prix_periode));
            foreach ($date_prix_periode as $prix_pd_nuit) {
                $devis_prix_ht= $devis_prix_ht + $prix_pd_nuit['periode_jour_prix'];
            }
        } else {
            $devis_prix_ht=$prix_nuit;
        }
        
        $prix_nuit = $devis_prix_ht/$nb_nuit->format('%a');

        $pers_en_plus = 0;
        $valeurChoisieEnInt = 0;
        $prix_service = 0;
        $prix_menage = 0;
        $prix_linge = 0;
        $prix_transport = 0;
        $prix_animaux = 0;
        $prix_pers_sup = 0;

        // Utilisation de isset pour vérifier si la clé existe dans le tableau POST
        if (isset($_POST['nbr_accueillis'])) {
            // Conversion en entier en utilisant (int)
            $valeurChoisieEnInt = (int)$_POST['nbr_accueillis']; 
        }

        // Vérification de l'existence de la clé dans la variable de session
        if (isset($_POST['pers_en_plus'])!= 0) {
            if($_POST['pers_en_plus']!=0){
                $pers_en_plus = $_POST['pers_en_plus'];
                $prix_pers_sup = $_POST['prix_pers_sup'];
                $prix_service = $prix_pers_sup + $prix_service;
            }
        }
        if (isset($_POST['nbr_animaux'])!= 0) {
            if($_POST['nbr_animaux']!=0){
                $nb_animaux = $_POST['nbr_animaux'];
                $prix_animaux = $_POST['prix_animaux'];
                $prix_service = $prix_animaux + $prix_service;
            }
        }

        if(isset($_POST['linge'])){
            $linge = true;
            $prix_linge = $_POST['charges_linge'];
            $prix_service = $prix_linge + $prix_linge;
        }else{
            $linge = false;
        }
        if(isset($_POST['menage'])){
            $menage = true;
            $prix_menage = $_POST['charges_menage'];
            $prix_service = $prix_menage + $prix_service;
        }else{
            $menage = false;
        }
        if(isset($_POST['navette'])){
            $navette = true;
            $prix_transport = $_POST['charges_transport'];
            $prix_service = $prix_transport + $prix_service;
        }else{
            $navette = false;
        }


        // Préparez la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO alhaiz_breizh._devis 
            (devis_id_compte_client, devis_id_logement, devis_nbr_personne, devis_date_debut, devis_date_fin, devis_date,
            devis_tarif_nuit_ht, devis_taxe_sejour_prix, devis_service_linge, devis_service_menage,
            devis_service_transport, devis_nbr_personne_sup, devis_nbr_animaux, devis_service_prix, devis_statut, devis_service_linge_prix, devis_service_menage_prix , devis_service_transport_prix, devis_service_personne_sup_prix, devis_service_animaux_prix, devis_prix_nuit,devis_prix_ht) 
            VALUES (:id_compte_client, :id_logement, :nbr_personne, :date_debut, :date_fin, :date_jour,
            :tarif_nuit_ht, :taxe_sejour_prix, :service_linge, :service_menage,
            :service_transport, :service_nbr_personne_sup, :nbr_animaux, :service_prix, :statut, :prix_linge, :prix_menage, :prix_transport, :prix_pers_sup, :prix_animaux, :prix_nuit,:devis_prix_ht)");

        // Liaison des valeurs des paramètres
        $stmt->bindParam(':id_compte_client', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':id_logement', $_SESSION['logement_id'], PDO::PARAM_INT);
        $stmt->bindParam(':nbr_personne', $valeurChoisieEnInt, PDO::PARAM_INT);
        $stmt->bindParam(':date_debut', $dateArr, PDO::PARAM_STR);
        $stmt->bindParam(':date_fin', $dateDep, PDO::PARAM_STR);
        $stmt->bindParam(':date_jour', $dateDuJour, PDO::PARAM_STR);
        $stmt->bindValue(':tarif_nuit_ht', floatval($_SESSION['logement_prix_nuit_base']), PDO::PARAM_STR);
        $stmt->bindValue(':taxe_sejour_prix', floatval($_SESSION['charges_taxe_sejour']), PDO::PARAM_STR);

        $stmt->bindParam(':service_linge', $linge, PDO::PARAM_BOOL);
        $stmt->bindParam(':service_menage', $menage, PDO::PARAM_BOOL);
        $stmt->bindParam(':service_transport', $navette, PDO::PARAM_BOOL);        

        $stmt->bindParam(':service_nbr_personne_sup', $pers_en_plus, PDO::PARAM_INT);
        $stmt->bindParam(':nbr_animaux', $_POST['nbr_animaux'], PDO::PARAM_INT);
        $stmt->bindValue(':service_prix', floatval($prix_service), PDO::PARAM_STR);
        $stmt->bindParam(':statut', $_SESSION['devis_statut'], PDO::PARAM_STR);
        $stmt->bindValue(':prix_linge', floatval($prix_linge), PDO::PARAM_STR);
        $stmt->bindValue(':prix_menage',  floatval($prix_menage), PDO::PARAM_STR);
        $stmt->bindValue(':prix_transport',  floatval($prix_transport), PDO::PARAM_STR);
        $stmt->bindValue(':prix_pers_sup', floatval($prix_pers_sup), PDO::PARAM_STR);
        $stmt->bindValue(':prix_animaux', floatval($prix_animaux), PDO::PARAM_STR);
        $stmt->bindValue(':prix_nuit', floatval($prix_nuit), PDO::PARAM_STR);
        $stmt->bindValue(':devis_prix_ht', floatval($devis_prix_ht), PDO::PARAM_STR);

        // Exécution de la requête
        $stmt->execute();

        // Redirection vers une page de confirmation ou une autre page appropriée
        header("Location: ../index.php");

    } catch (PDOException $e) {
        // Gérer les erreurs de base de données
        echo "<p>Erreur de base de données : " . $e->getMessage() . "</p>";
    }
} else {
    // Si la méthode de requête n'est pas POST, rediriger l'utilisateur vers une page appropriée
    header("Location: erreurPage.php"); 
}
?>
