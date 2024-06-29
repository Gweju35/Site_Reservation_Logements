<?php
session_start();
$toto=false;

// Vérifiez si l'utilisateur est authentifié
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'proprietaire') {
    $user_id = $_SESSION['user_id'];
} else {
    // Redirigez l'utilisateur vers la page de login s'il n'est pas authentifié ou s'il n'est pas un propriétaire
    header("Location: ../Profil/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $photo_principale_dir = "../assets/ressources/images/photo_principale/";
    if (!is_dir($photo_principale_dir)) {
        mkdir($photo_principale_dir, 0755, true);
    }

        // Traitement de l'image principale (si nécessaire)
    
    if (isset($_FILES['logement_photo_new'])&& $_FILES['logement_photo_new']['error'] == 0) {
        $toto=true;
        $target_dir = "../assets/ressources/images/photo_principale/";
        $target_file = $target_dir . date('YmdHis') . '.png';
        move_uploaded_file($_FILES['logement_photo_new']['tmp_name'], $target_file);
        $_SESSION['logement_photo_new'] = $target_file;
        
    }   else {
        $_SESSION['logement_photo'] = ""; // Si aucune image n'a été téléchargée
    }

    // Vérifiez si le dossier "photo_complementaires" existe, sinon, créez-le
    $photo_complementaires_dir = "../assets/ressources/images/photo_complementaires/";
    if (!is_dir($photo_complementaires_dir)) {
        mkdir($photo_complementaires_dir, 0755, true);
    }

    if(isset($_FILES['logement_photo_new'])&& $_FILES['logement_photo_new']['error'] == 0) {
        $toto=true;
        $target_dir = "../assets/ressources/images/photo_principale/";
        $target_file = $target_dir . date('YmdHis') . '.png';
        move_uploaded_file($_FILES['logement_photo_new']['tmp_name'], $target_file);
        $_SESSION['logement_photo_new'] = $target_file;
        
    }   else {
        $_SESSION['logement_photo'] = ""; // Si aucune image n'a été téléchargée
    }

    // TRAITEMENT des coordonées gps
    function getCoordinates($address) {
        // Encodez l'adresse pour qu'elle soit utilisable dans une URL
        $encoded_address = urlencode($address);
        
        // Clé API Google Maps (remplacez 'YOUR_API_KEY' par votre propre clé)
        $api_key = 'AIzaSyDmOaihDngGhTuTKe9623bFFTNzrdLAzus';
        
        // Construisez l'URL pour la requête à l'API de géocodage inversé
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$encoded_address&key=$api_key";
        
        // Effectuez la requête et obtenez la réponse JSON
        $response = file_get_contents($url);
        
        // Analysez la réponse JSON
        $data = json_decode($response);
        
        // Vérifiez si la requête a réussi
        if ($data->status == 'OK') {
            // Récupérez les coordonnées géographiques du premier résultat
            $location = $data->results[0]->geometry->location;
            $latitude = $location->lat;
            $longitude = $location->lng;
            return array('latitude' => $latitude, 'longitude' => $longitude);
        } else {
            // En cas d'erreur, retournez null ou une autre valeur appropriée
            return null;
        }
    }

    $address = $_POST['logement_adresse'].', '.$_POST['logement_code_postal'].', '.$_POST['logement_ville'];
    $coordinates = getCoordinates($address);
    if($coordinates != null){
        $gps = $coordinates['latitude'] . "," . $coordinates['longitude'];
    } else {
        $gps = null;
    }
    



    if (isset($_POST['logement_id'])) {
        // Récupérez les données soumises depuis le formulaire
        $logement_id = $_POST['logement_id'];
        // Connexion à la base de données
        include('../../libs/connect_params.php');

        try {
            $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Mettez à jour les informations du logement
            $sql = "UPDATE alhaiz_breizh._logement 
                    SET logement_accroche = :accroche, logement_description = :description, logement_nature = :nature,
                    logement_type = :type,  logement_prix_nuit_base = :prix_nuit_base, logement_surface = :surface,
                    logement_nb_chambre = :nb_chambre, logement_nb_lit = :nb_lit, logement_nb_lit_double = :nb_lit_double,
                    logement_nb_salle_de_bain = :nb_salle_de_bain, logement_personne_max = :personne_max, logement_photo = :photo,logement_adresse = :adresse,
                    logement_ville = :ville , logement_code_postal = :code_postal , logement_coordonnees_gps = :gps,
                    equipement_tv = :tv, equipement_machine_a_laver = :machine_a_laver, equipement_lave_vaisselle = :lave_vaisselle, equipement_wifi = :wifi,
                    service_linge = :linge, service_menage = :menage, service_transport = :transport, service_animaux_domestique = :animaux_domestique,
                    service_personne_sup = :personne_sup, installation_climatisation = :climatisation, installation_piscine = :piscine , installation_jacuzzi = :jacuzzi,
                    installation_hammam = :hammam, installation_sauna = :sauna, amenagement_jardin = :jardin ,  amenagement_balcon = :balcon, amenagement_parking_public = :parking_public,
                    amenagement_parking_prive = :parking_prive, amenagement_terrasse = :terrasse , charges_linge = :charges_linge, charges_menage = :charges_menage,  charges_transport = :charges_transport,
                    charges_animaux = :charges_animaux, charges_personne_sup = :charges_personne_sup, charges_taxe_sejour = :charges_taxe_sejour
                    WHERE logement_id = :logement_id"; 

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':logement_id', $logement_id);
            $stmt->bindParam(':accroche', $_POST['logement_accroche']);
            $stmt->bindParam(':description', $_POST['logement_description']);
            $stmt->bindParam(':nature', $_POST['logement_nature']);
            $stmt->bindParam(':type', $_POST['logement_type']);
            $stmt->bindParam(':prix_nuit_base', $_POST['logement_prix_nuit_base']);
            $stmt->bindParam(':surface', $_POST['logement_surface']);
            $stmt->bindParam(':nb_chambre', $_POST['logement_nb_chambre']);
            $stmt->bindParam(':nb_lit', $_POST['logement_nb_lit']);
            $stmt->bindParam(':nb_lit_double', $_POST['logement_nb_lit_double']);
            $stmt->bindParam(':nb_salle_de_bain', $_POST['logement_nb_salle_de_bain']);
            $stmt->bindParam(':personne_max', $_POST['logement_personne_max']);
            if ($toto == true) {
                $photo = $_SESSION['logement_photo_new'];
                unlink(__DIR__.'/'.trim($_POST['logement_photo_old']));
            } else {
                $photo = $_POST['logement_photo_old'];
            }            
            $stmt->bindParam(':photo', $photo);
            $stmt->bindParam(':adresse', $_POST['logement_adresse']);
            $stmt->bindParam(':ville', $_POST['logement_ville']);
            $stmt->bindParam(':code_postal', $_POST['logement_code_postal']);
            $stmt->bindParam(':gps', $gps);
            $stmt->bindValue(':tv', isset($_POST['equipement_tv']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':machine_a_laver', isset($_POST['equipement_machine_a_laver']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':lave_vaisselle', isset($_POST['equipement_lave_vaisselle']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':wifi', isset($_POST['equipement_wifi']) ? true : false, PDO::PARAM_BOOL);    
            $stmt->bindValue(':linge', isset($_POST['service_linge']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':menage', isset($_POST['service_menage']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':transport', isset($_POST['service_transport']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':animaux_domestique', isset($_POST['service_animaux_domestique']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':personne_sup', isset($_POST['service_personne_sup']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':climatisation', isset($_POST['installation_climatisation']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':piscine', isset($_POST['installation_piscine']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':jacuzzi', isset($_POST['installation_jacuzzi']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':hammam', isset($_POST['installation_hammam']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':sauna', isset($_POST['installation_sauna']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':jardin', isset($_POST['amenagement_jardin']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':balcon', isset($_POST['amenagement_balcon']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':parking_public', isset($_POST['amenagement_parking_public']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':parking_prive', isset($_POST['amenagement_parking_prive']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':terrasse', isset($_POST['amenagement_terrasse']) ? true : false, PDO::PARAM_BOOL);
            $stmt->bindValue(':charges_linge', isset($_POST['service_linge']) ? floatval($_POST['charges_linge']) : 0, PDO::PARAM_STR);
            $stmt->bindValue(':charges_menage', isset($_POST['service_menage']) ? floatval($_POST['charges_menage']) : 0, PDO::PARAM_STR);
            $stmt->bindValue(':charges_transport', isset($_POST['service_transport']) ? floatval($_POST['charges_transport']) : 0, PDO::PARAM_STR);
            $stmt->bindValue(':charges_animaux', isset($_POST['service_animaux_domestique']) ? floatval($_POST['charges_animaux']) : 0, PDO::PARAM_STR);
            $stmt->bindValue(':charges_personne_sup', isset($_POST['service_personne_sup']) ? floatval($_POST['charges_personne_sup']) : 0, PDO::PARAM_STR);
            $stmt->bindValue(':charges_taxe_sejour', floatval($_POST['charges_taxe_sejour']), PDO::PARAM_STR);
            $stmt->execute();

            if(isset($_POST['photo_att_1']) == 'photo_1' && isset($_POST['photos_1_supprimer']) == true){
                $sql2 = "UPDATE alhaiz_breizh._photo set photo_1 = NULL WHERE logement_id = :logement_id";
            
                $stmt = $pdo->prepare($sql2);
                $stmt->bindParam(':logement_id', $logement_id);
                $stmt->execute();
                $photo_att[1] = true;
                unlink(__DIR__.'/'.trim($_POST['photo_1']));
            }


            if(isset($_POST['photo_att_2']) == 'photo_2' && isset($_POST['photos_2_supprimer']) == true){
                $sql3 = "UPDATE alhaiz_breizh._photo set photo_2 = NULL WHERE logement_id = :logement_id";
            
                $stmt = $pdo->prepare($sql3);
                $stmt->bindParam(':logement_id', $logement_id);
                $stmt->execute();
                $photo_att[2] = true;
                
                unlink(__DIR__.'/'.trim($_POST['photo_2']));
            }

            if(isset($_POST['photo_att_3']) == 'photo_3' && isset($_POST['photos_3_supprimer']) == true){
                $sql4 = "UPDATE alhaiz_breizh._photo set photo_3 = NULL WHERE logement_id = :logement_id";
            
                $stmt = $pdo->prepare($sql4);
                $stmt->bindParam(':logement_id', $logement_id);
                $stmt->execute();
                $photo_att[3] = true;
                
                unlink(__DIR__.'/'.trim($_POST['photo_3']));
            }

            if(isset($_POST['photo_att_4']) == 'photo_4' && isset($_POST['photos_4_supprimer']) == true){
                $sql5 = "UPDATE alhaiz_breizh._photo set photo_4 = NULL WHERE logement_id = :logement_id";
            
                $stmt = $pdo->prepare($sql2);
                $stmt->bindParam(':logement_id', $logement_id);
                $stmt->execute();
                $photo_att[4] = true;
                
                unlink(__DIR__.'/'.trim($_POST['photo_4']));
            }

            if(isset($_POST['photo_att_5']) == 'photo_5' && isset($_POST['photos_5_supprimer']) == true){
                $sql6 = "UPDATE alhaiz_breizh._photo set photo_5 = NULL WHERE logement_id = :logement_id";
                
                $stmt = $pdo->prepare($sql6);
                $stmt->bindParam(':logement_id', $logement_id);
                $stmt->execute();
                $photo_att[5] = true;
                
                unlink(__DIR__.'/'.trim($_POST['photo_5']));
            }

            if(isset($_POST['photo_att_6']) == 'photo_6' && isset($_POST['photos_6_supprimer']) == true){
                $sql7 = "UPDATE alhaiz_breizh._photo set photo_6 = NULL WHERE logement_id = :logement_id";

                $stmt = $pdo->prepare($sql7);
                $stmt->bindParam(':logement_id', $logement_id);
                $stmt->execute();
                $photo_att[6] = true;
                
                unlink(__DIR__.'/'.trim($_POST['photo_6']));
            }

            if ($_FILES['photo_complementaire']['name'][0] != '') {
                
                $target_dir_temp = "../assets/ressources/images/photo_complementaires/";
                $uploaded_files = array();
        
                foreach ($_FILES['photo_complementaire']['tmp_name'] as $key => $tmp_name) {
                    $file_extension = pathinfo($_FILES['photo_complementaire']['name'][$key], PATHINFO_EXTENSION);
                    $target_file = $target_dir_temp . date('YmdHis') . '_' . $key . '.' . $file_extension;
                    move_uploaded_file($tmp_name, $target_file);
                    $uploaded_files[] = $target_file;
                }
        
                $photo_comple[] = $uploaded_files;
            } else {
                $photo_comple[] = array(); // Si aucune image n'a été téléchargée
            }  
            
            if($_FILES['photo_complementaire']['name'][0] != ''){
                $cpt1 = 1;
                foreach($photo_comple as $photos){
                    foreach($photos as $photo){
                        $cpt = $cpt1;
                        $cpt1=$cpt;
                        $place = false;
                        while ($cpt1<=6 && $place == false) {
                            if(isset($_POST['photo_att_' . $cpt1 ]) == false || isset($photo_att[$cpt1])){
                                $sql8 = "UPDATE alhaiz_breizh._photo set photo_". $cpt1 ." = :photo WHERE logement_id = :logement_id";
                    
                                $stmt = $pdo->prepare($sql8);
                                $stmt->bindParam(':logement_id', $logement_id);
                                $stmt->bindParam(':photo', $photo);
                                $stmt->execute();
                                
                                $place = true;
                            }
                            $cpt1++;
                        }
                    }
                }
            }
            // Redirigez l'utilisateur vers la page de logements après la mise à jour
            header("Location: ../Logements/logement.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
        }
    } else {
       print_r($_POST);
    }
} else {
    echo "Requête incorrecte. Utilisez le formulaire de modification.";
}
?>