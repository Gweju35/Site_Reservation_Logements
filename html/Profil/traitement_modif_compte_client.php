<?php
session_start();
$toto = false;

// Vérifiez si l'utilisateur est authentifié
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'proprietaire' || isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'client') {
    $user_id = $_SESSION['user_id'];
} else {
    // Redirigez l'utilisateur vers la page de login s'il n'est pas authentifié ou s'il n'est pas un propriétaire
    header("Location: ../Profil/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $adresse = $_POST["adresse"];
    $code_postal = $_POST["code_postal"];
    $ville = $_POST["ville"];
    $adresse_complete = $adresse . ', ' . $ville . ', ' . $code_postal;

    $adresse_facturation = $_POST["adresse_facturation"];
    $ville_facturation = $_POST["ville_facturation"];
    $code_postal_facturation = $_POST["code_postal_facturation"];
    $adresse_complete_facturation = $adresse_facturation . ', ' . $ville_facturation . ', ' . $code_postal_facturation;

    $photo_profil_dir = "../assets/ressources/images/photo_compte_client/";
    if (!is_dir($photo_profil_dir)) {
        mkdir($photo_profil_dir, 0755, true);
    }

        // Traitement de l'image principale (si nécessaire)
    
    if (isset($_FILES['compte_photo_profil_new'])&& $_FILES['compte_photo_profil_new']['error'] == 0) {
        $toto = true;
        $target_dir = "../assets/ressources/images/photo_compte_client/";
        $target_file = $target_dir . date('YmdHis') . '.png';
        move_uploaded_file($_FILES['compte_photo_profil_new']['tmp_name'], $target_file);
        $_SESSION['compte_photo_profil_new'] = $target_file;
        
    }   else {
        $_SESSION['compte_photo_profil'] = ""; // Si aucune image n'a été téléchargée
    }

    if (isset($_POST['compte_id'])) {
        // Récupérez les données soumises depuis le formulaire
        $compte_id = $_POST['compte_id'];
        // Connexion à la base de données
        include('../../libs/connect_params.php');

        try {
            $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Mettez à jour les informations du logement
            $sql = "UPDATE alhaiz_breizh._client 
                    SET compte_adresse = :compte_adresse, compte_telephone = :compte_telephone, compte_photo_profil = :compte_photo_profil, compte_email = :compte_email, client_adresse_facturation = :client_adresse_facturation
                    WHERE compte_id = :compte_id"; 

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':compte_id', $compte_id);
            $stmt->bindParam(':compte_telephone', $_POST['compte_telephone']);
            $stmt->bindParam(':compte_adresse', $adresse_complete);
            $stmt->bindParam(':compte_email', $_POST['compte_email']);
            $stmt->bindParam(':client_adresse_facturation', $adresse_complete_facturation);


            if ($toto == true) {                
                $photo = $_SESSION['compte_photo_profil_new'];
                if($_POST['compte_photo_profil_old']!="../assets/ressources/images/photo_compte_client/image_pdp.jpg"){
                    unlink(trim($_POST['compte_photo_profil_old']));
                }
            } else {
                $photo = $_POST['compte_photo_profil_old'];
            }     
            $stmt->bindParam(':compte_photo_profil', $photo);  
            $stmt->execute();     
            header("Location: ../Profil/profil.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
        }
    } else {
        echo "Données manquantes dans le formulaire de modification.";
    }
} else {
    echo "Requête incorrecte. Utilisez le formulaire de modification.";
}
?>