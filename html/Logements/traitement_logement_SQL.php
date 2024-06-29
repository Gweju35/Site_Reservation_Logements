<?php
session_start();

// Vérifier si le formulaire a été soumis et si la clé 'valider' existe dans $_POST
if (isset($_POST['valider']) && $_POST['valider'] == "valider") {
    
    // Informations de connexion à la base de données
    include('../../libs/connect_params.php');

    // Connexion à la base de données avec PDO
    try {
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Préparez la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO alhaiz_breizh._logement (
            logement_compte_id_proprio,
            logement_accroche,
            logement_description,
            logement_nature,
            logement_type,
            logement_prix_nuit_base,
            logement_surface,
            logement_nb_chambre,
            logement_nb_lit,
            logement_nb_lit_double,
            logement_nb_salle_de_bain,
            logement_personne_max,
            logement_photo,
            logement_adresse,
            logement_ville,
            logement_code_postal,
            logement_coordonnees_gps,

            equipement_tv,
            equipement_machine_a_laver,
            equipement_lave_vaisselle,
            equipement_wifi,

            service_linge,
            service_menage,
            service_transport,
            service_animaux_domestique,
            service_personne_sup,

            installation_climatisation,
            installation_piscine,
            installation_jacuzzi,
            installation_hammam,
            installation_sauna,

            amenagement_jardin,
            amenagement_balcon,
            amenagement_parking_public,
            amenagement_parking_prive,
            amenagement_terrasse,
            
            charges_linge,
            charges_menage,
            charges_transport,
            charges_animaux,
            charges_personne_sup,
            charges_taxe_sejour,
            logement_statut_ligne)
        VALUES (
            :compte_id_proprio, 
            :accroche, 
            :description, 
            :nature, 
            :type, 
            :prix_nuit_base,
            :surface,
            :nb_chambre,
            :nb_lit,
            :nb_lit_double,
            :nb_salle_de_bain,
            :personne_max,
            :photo,
            :adresse, 
            :ville,
            :code_postal,
            :coordonnees_gps, 
            :tv, 
            :machine_a_laver, 
            :lave_vaisselle, 
            :wifi, 
            :linge, 
            :menage, 
            :transport, 
            :animaux_domestique, 
            :personne_sup, 
            :climatisation, 
            :piscine, 
            :jacuzzi, 
            :hammam, 
            :sauna, 
            :jardin, 
            :balcon, 
            :parking_public, 
            :parking_prive, 
            :terrasse, 
            :charges_linge, 
            :charges_menage, 
            :charges_transport, 
            :charges_animaux, 
            :charges_personne_sup, 
            :charges_taxe_sejour,
            :statut
            )");


        // Liaison des valeurs des paramètres, y compris les coordonnées GPS
        $stmt->bindParam(':compte_id_proprio', $_SESSION['logement_compte_id_proprio']);
        $stmt->bindParam(':accroche', $_SESSION['logement_accroche']);
        $stmt->bindParam(':description', $_SESSION['logement_description']);
        $stmt->bindParam(':nature', $_SESSION['logement_nature']);
        $stmt->bindParam(':type', $_SESSION['logement_type']);
        $stmt->bindParam(':adresse', $_SESSION['logement_adresse']);
        $stmt->bindParam(':coordonnees_gps', $_SESSION['logement_coordonnees_gps']);
        $stmt->bindParam(':prix_nuit_base', $_SESSION['logement_prix_nuit_base']);
        $stmt->bindParam(':surface', $_SESSION['logement_surface']);
        $stmt->bindParam(':nb_chambre', $_SESSION['logement_nb_chambre']);
        $stmt->bindParam(':nb_lit', $_SESSION['logement_nb_lit']);
        $stmt->bindParam(':nb_lit_double', $_SESSION['logement_nb_lit_double']);
        $stmt->bindParam(':nb_salle_de_bain', $_SESSION['logement_nb_salle_de_bain']);
        $stmt->bindParam(':personne_max', $_SESSION['logement_nb_pers_sup']);
        $stmt->bindParam(':ville', $_SESSION['logement_ville']);
        $stmt->bindParam(':code_postal', $_SESSION['logement_code_postal']);
        $stmt->bindParam(':photo', $_SESSION['logement_photo']);
        $stmt->bindValue(':tv', $_SESSION['choix_tv'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':machine_a_laver', $_SESSION['choix_machine_a_laver'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':lave_vaisselle', $_SESSION['choix_lave_vaiselle'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':wifi', $_SESSION['choix_wifi'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':linge', $_SESSION['choix_linge'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':menage', $_SESSION['choix_menage'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':transport', $_SESSION['choix_transport'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':animaux_domestique', $_SESSION['choix_animaux'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':personne_sup', $_SESSION['choix_personne_sup'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':climatisation', $_SESSION['choix_climatisation'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':piscine', $_SESSION['choix_piscine'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':jacuzzi', $_SESSION['choix_jacuzzi'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':hammam', $_SESSION['choix_hammam'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':sauna', $_SESSION['choix_sauna'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':jardin', $_SESSION['choix_jardin'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':balcon', $_SESSION['choix_balcon'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':parking_public', $_SESSION['choix_parking_public'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':parking_prive', $_SESSION['choix_parking_prive'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':terrasse', $_SESSION['choix_terrasse'] ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':charges_linge', floatval($_SESSION['charges_add_linge']), PDO::PARAM_STR);
        $stmt->bindValue(':charges_menage', floatval($_SESSION['charges_add_menage']), PDO::PARAM_STR);
        $stmt->bindValue(':charges_transport', floatval($_SESSION['charges_add_transport']), PDO::PARAM_STR);
        $stmt->bindValue(':charges_animaux', floatval($_SESSION['charges_add_prix_sup_animaux']), PDO::PARAM_STR);
        $stmt->bindValue(':charges_personne_sup', floatval($_SESSION['charges_add_prix_sup_personne']), PDO::PARAM_STR);
        $stmt->bindValue(':charges_taxe_sejour', floatval($_SESSION['charges_add_taxe_sejour']), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $_SESSION['logement_statut_ligne'] ? true : false, PDO::PARAM_BOOL);
        
        // Exécution de la requête
        $stmt->execute();
        $logement_id = $pdo->lastInsertId();
        
        // Afficher une boîte de dialogue de confirmation
        $fichiers = $_SESSION['photos_complementaires'];
       
        if ($logement_id !== false && $logement_id !== null) {
            $query = $pdo->prepare("INSERT INTO alhaiz_breizh._photo (logement_id, photo_1, photo_2, photo_3, photo_4, photo_5, photo_6) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
            // Utilisation de bindValue pour le premier paramètre
            $query->bindValue(1, $logement_id, PDO::PARAM_INT);
        
            $num_photo = 1;
            
            for ($i = 0; $i < 6; $i++) {
                // Utilisation de bindParam pour les paramètres suivants
                $query->bindParam($num_photo + 1, $fichiers[$i]);
                $num_photo++;
           }
        
            $query->execute();
        } else {
            echo "Erreur : logement_id non valide";
        }
        
        // Rediriger l'utilisateur après l'insertion des données
        header('Location: logement.php');
        exit();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
} else {
    // Rediriger l'utilisateur si le formulaire n'a pas été soumis correctement
    header('Location: ../index.php');
    exit();
}
?>
