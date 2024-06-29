<?php
session_start();

include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier le nom d'utilisateur (compte_pseudo) et le mot de passe (compte_mdp)
    $compte_pseudo = $_POST['login'];
    $compte_mdp = $_POST['mot_de_passe'];

    $sql = "SELECT compte_id, compte_nom, compte_prenom, compte_pseudo, compte_photo_profil FROM alhaiz_breizh._compte WHERE compte_pseudo = :compte_pseudo AND compte_mdp = :compte_mdp";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':compte_pseudo', $compte_pseudo);
    $stmt->bindParam(':compte_mdp', $compte_mdp);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Vérifiez si l'utilisateur est un client ou un propriétaire en recherchant son ID dans les tables correspondantes
        $sql_client = "SELECT compte_id FROM alhaiz_breizh._client WHERE compte_id = :user_id";
        $stmt_client = $pdo->prepare($sql_client);
        $stmt_client->bindParam(':user_id', $user['compte_id']);
        $stmt_client->execute();

        $sql_proprietaire = "SELECT compte_id FROM alhaiz_breizh._proprietaire WHERE compte_id = :user_id";
        $stmt_proprietaire = $pdo->prepare($sql_proprietaire);
        $stmt_proprietaire->bindParam(':user_id', $user['compte_id']);
        $stmt_proprietaire->execute();

        if ($stmt_client->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['user_type'] = 'client';
        } elseif ($stmt_proprietaire->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['user_type'] = 'proprietaire';
        }

        // L'utilisateur est authentifié, stockez ses informations dans la session
        $_SESSION['user_id'] = $user['compte_id'];
        $_SESSION['user_nom'] = $user['compte_nom'];
        $_SESSION['user_prenom'] = $user['compte_prenom'];
        $_SESSION['user_pseudo'] = $user['compte_pseudo'];
        $_SESSION['user_photo_profil'] = $user['compte_photo_profil'];

        // Redirigez l'utilisateur vers la page de profil
        header("Location: ../Profil/profil.php");
    } else {
        // Identifiants incorrects, redirigez l'utilisateur vers la page de login
        header("Location: ../Profil/login.php");
    }
} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
?>
