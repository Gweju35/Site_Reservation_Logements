<?php
session_start();
$toto = false;
$photo_recto = false;
$photo_verso = false;
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

    $photo_profil_dir = "../assets/ressources/images/photo_compte_proprietaire/";
    if (!is_dir($photo_profil_dir)) {
        mkdir($photo_profil_dir, 0755, true);
    }

        // Traitement de l'image principale (si nécessaire)
    
    if (isset($_FILES['compte_photo_profil_new'])&& $_FILES['compte_photo_profil_new']['error'] == 0) {
        $toto = true;
        $target_dir = "../assets/ressources/images/photo_compte_proprietaire/";
        $target_file = $target_dir . date('YmdHis') . '.png';
        move_uploaded_file($_FILES['compte_photo_profil_new']['tmp_name'], $target_file);
        $_SESSION['compte_photo_profil_new'] = $target_file;
        
    }   else {
        $_SESSION['compte_photo_profil'] = ""; // Si aucune image n'a été téléchargée
    }

    if (isset($_FILES['proprietaire_photo_recto_new'])&& $_FILES['proprietaire_photo_recto_new']['error'] == 0) {
        $photo_recto = true;
        $target_dir = "../assets/ressources/images/photo_identite/";
        $target_file = $target_dir . date('YmdHis') . '.png';
        move_uploaded_file($_FILES['proprietaire_photo_recto_new']['tmp_name'], $target_file);
        $_SESSION['proprietaire_photo_recto_new'] = $target_file;
        
    }   else {
        $_SESSION['proprietaire_photo_recto_old'] = ""; // Si aucune image n'a été téléchargée
    }

    if (isset($_FILES['proprietaire_photo_verso_new'])&& $_FILES['proprietaire_photo_verso_new']['error'] == 0) {
        $photo_verso = true;
        $target_dir = "../assets/ressources/images/photo_identite/";
        $target_file = $target_dir . date('YmdHis') . '.png';
        move_uploaded_file($_FILES['proprietaire_photo_verso_new']['tmp_name'], $target_file);
        $_SESSION['proprietaire_photo_verso_new'] = $target_file;
        
    }else {
        $_SESSION['proprietaire_photo_verso_old'] = ""; // Si aucune image n'a été téléchargée
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
            $sql = "UPDATE alhaiz_breizh._proprietaire 
                    SET proprietaire_langue_parlee = :proprietaire_langue_parlee, compte_email = :compte_email, compte_adresse = :compte_adresse, compte_telephone = :compte_telephone,  compte_mdp = :compte_mdp, compte_photo_profil = :compte_photo_profil, proprietaire_iban = :proprietaire_iban, proprietaire_photo_recto = :proprietaire_photo_recto, proprietaire_photo_verso = :proprietaire_photo_verso  
                    WHERE compte_id = :compte_id"; 

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':compte_id', $compte_id);
            $stmt->bindParam(':compte_telephone', $_POST['compte_telephone']);
            $stmt->bindParam(':compte_adresse', $adresse_complete);
            $stmt->bindParam(':compte_mdp', $_POST['compte_mdp']);
            $stmt->bindParam(':compte_email', $_POST['compte_email']);
            $stmt->bindParam(':proprietaire_langue_parlee', $_POST['proprietaire_langue_parlee']);

            if ($toto == true) {                
                $photo = $_SESSION['compte_photo_profil_new'];
                if($_POST['compte_photo_profil_old']!="../assets/ressources/images/photo_compte_proprietaire/image_pdp.jpg"){
                    unlink(trim($_POST['compte_photo_profil_old']));
                }
            } else {
                $photo = $_POST['compte_photo_profil_old'];
            }     

            $stmt->bindParam(':compte_photo_profil', $photo);  

            $stmt->bindParam(':proprietaire_iban',  $_POST['proprietaire_iban']);

            if ($photo_recto == true) {                
                $photo_1 = $_SESSION['proprietaire_photo_recto_new'];
                unlink(trim($_POST['proprietaire_photo_recto_old']));
            } else {
                $photo_1 = $_POST['proprietaire_photo_recto_old'];
            }    

            $stmt->bindParam(':proprietaire_photo_recto', $photo_1);

            if ($photo_verso == true) {                
                $photo_2 = $_SESSION['proprietaire_photo_verso_new'];
                unlink(trim($_POST['proprietaire_photo_verso_old']));
            } else {
                $photo_2 = $_POST['proprietaire_photo_verso_old'];
            }    

            $stmt->bindParam(':proprietaire_photo_verso', $photo_2);

            $stmt->execute();     
            header("Location: ../Profil/profil.php");
            exit();

        }catch (PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
        }
    }else {
        echo "Données manquantes dans le formulaire de modification.";
    }
}else {
    echo "Requête incorrecte. Utilisez le formulaire de modification.";
}   

?>