<?php
include("../../libs/connect_params.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Récupérer les données du formulaire
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $date_naissance = $_POST["date_naissance"];
        $civilite = $_POST["civilite"];
        $telephone = $_POST["compte_telephone"];

        $adresse = $_POST["adresse"];
        $code_postal = $_POST["code_postal"];
        $ville = $_POST["ville"];
        $adresse_complete = $adresse . ', ' . $ville . ', ' . $code_postal;

        $email = $_POST["compte_email"];
        $pseudo = $_POST["compte_pseudo"];
        $mdp = $_POST["mdp"];
        $langue = $_POST['proprietaire_langue_parlee'];
        $iban = $_POST['proprietaire_iban'];     
        
        $photo_proprio_dir = "../assets/ressources/images/photo_compte_proprietaire/";
        $photo_identite_dir = "../assets/ressources/images/photo_identite/";

        if (!is_dir($photo_proprio_dir)) {
            mkdir($photo_proprio_dir, 0755, true);
        }
        if (!is_dir($photo_identite_dir)) {
            mkdir($photo_identite_dir, 0755, true);
        }

        if (($_FILES['photo_recto']) && $_FILES['photo_recto']['error'] == 0) {
            $target_dir = $photo_proprio_dir;
            $target_file = $target_dir . date('YmdHis') . '.png';
            move_uploaded_file($_FILES['photo_recto']['tmp_name'], $target_file);
            $_SESSION['photo_recto'] = $target_file;
        } else {
            $_SESSION['photo_recto'] = ""; // Si aucune image n'a été téléchargée
        }
        if (($_FILES['photo_verso']) && $_FILES['photo_verso']['error'] == 0) {
            $target_dir = $photo_identite_dir;
            $target_file = $target_dir . date('YmdHis') . '.png';
            move_uploaded_file($_FILES['photo_verso']['tmp_name'], $target_file);
            $_SESSION['photo_verso'] = $target_file;
        } else {
            $_SESSION['photo_verso'] = ""; // Si aucune image n'a été téléchargée
        }
        if (($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] == 0) {
            $target_dir = $photo_identite_dir;
            $target_file = $target_dir . date('YmdHis') . '.png';
            move_uploaded_file($_FILES['photo_profil']['tmp_name'], $target_file);
            $_SESSION['photo_profil'] = $target_file;
        } else {
            $_SESSION['photo_profil'] = "../assets/ressources/images/photo_compte_proprietaire/image_pdp.jpg"; // Si aucune image n'a été téléchargée
        }
    

        // Connexion à la base de données (à adapter selon votre configuration)
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Préparation de la requête SQL
        $sql = "INSERT INTO alhaiz_breizh._proprietaire (compte_nom, compte_prenom, compte_date_naissance, compte_civilite, compte_telephone, compte_adresse, compte_email, compte_pseudo, compte_mdp, compte_photo_profil, proprietaire_photo_recto, proprietaire_photo_verso, proprietaire_langue_parlee, proprietaire_iban)
                VALUES (:nom, :prenom, :date_naissance, :civilite, :telephone, :adresse, :email, :pseudo, :mdp,:photo_profil, :photo_recto, :photo_verso, :langue, :iban)";
        $stmt = $pdo->prepare($sql);

        // Exécution de la requête avec les valeurs des champs
        $stmt->bindParam(":nom", $nom, PDO::PARAM_STR);
        $stmt->bindParam(":prenom", $prenom, PDO::PARAM_STR);
        $stmt->bindParam(":date_naissance", $date_naissance, PDO::PARAM_STR); // Assurez-vous que $date_naissance est au bon format (par exemple, "YYYY-MM-DD")
        $stmt->bindParam(":civilite", $civilite, PDO::PARAM_STR);
        $stmt->bindParam(":telephone", $telephone, PDO::PARAM_STR);
        $stmt->bindParam(":adresse", $adresse_complete, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
        $stmt->bindParam(":mdp", $mdp, PDO::PARAM_STR);
        $stmt->bindParam(":photo_profil", $_SESSION['photo_profil'], PDO::PARAM_STR);
        $stmt->bindParam(":photo_recto", $_SESSION['photo_recto'], PDO::PARAM_STR);
        $stmt->bindParam(":photo_verso", $_SESSION['photo_verso'], PDO::PARAM_STR);
        $stmt->bindParam(":langue", $langue, PDO::PARAM_STR);
        $stmt->bindParam(":iban", $iban, PDO::PARAM_STR);


        if ($stmt->execute()) {
            // Redirection vers une page de succès ou de confirmation
            header("Location: ../Profil/login.php");
        } else {
            // En cas d'échec de l'insertion, afficher un message d'erreur
            echo "Erreur lors de la création du compte.";
        }
    } catch (PDOException $e) {
        // Gérer l'erreur de la base de données
        echo "Erreur de la base de données : " . $e->getMessage();
    }
} else {
    // Redirection en cas d'accès direct au script sans soumission du formulaire
    header("Location: ../Profil/login.php");
}
?>
