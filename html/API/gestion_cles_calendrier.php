<?php
session_start();

include('../../libs/connect_params.php');

try {

    $compte_id = $_SESSION['user_id'];
    
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM  alhaiz_breizh._abonnement WHERE abonnement_id_compte = :compte_id");
    $stmt->bindParam(':compte_id', $compte_id);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    


}catch (PDOException $e) {

    echo "Erreur de base de données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clé calendrier</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="abo_script.js"></script>
    <link rel="stylesheet" href="../assets/API/">
    <link rel="stylesheet" href="../assets/API/gestion_cles_api.css">
    <link rel="stylesheet" href="../assets/main.css">
    
</head>
<body>
    
<!------------------HEADER------------------>

<?php
if (isset($_SESSION['user_type']) != 'proprietaire'){
    header('Location: ../Profil/login.php');
}else{
    echo $_SESSION['headerProprietaire'];
}
?>

<!------------------MAIN------------------>
    <main>
        <div class="conteneur_titre_back">   
            <a href="javascript:history.back()">
                <div class="cercle_retour"><img src="../assets/ressources/icons/left-arrow.svg" alt="icone back" /></div>
            </a>
            <div class="titre_page">
                <h2>Gestion des clés du calendrier</h2>
            </div>
        </div>
              
        <div class="table_bloc">
        <table>
            <thead>
                <th>Clés abonnement</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Devis</th>
                <th>Réservation</th>
                <th>Raison personnelle</th>
                <th class="supprimer">Supprimer</th>
            </thead>
            <tbody>
        <?php
            foreach ($results as $result){
                ?>  
                <tr> 
                    <td id="<?= $result['abonnement_cle'] ?>"></td>
                    <td><input class="input_deb" type="date" name="abonnement_date_deb" id="<?= $result['abonnement_cle'] ?>" value="<?= $result['abonnement_date_deb'] ?>" onchange="dateDebutChanged(name,id,value)"></td>
                    <td><input class="input_fin" type="date" name="abonnement_date_fin" id="<?= $result['abonnement_cle'] ?>" value="<?= $result['abonnement_date_fin'] ?>" onchange="dateFinChanged(name,id,value)"></td>
                    <td>
                        <div class="check_form">
                            <input class="taille_checkbox" type="checkbox" name="abonnement_reservation" id="<?= $result['abonnement_cle'] ?>" onclick="checkboxClicked(name,id)" <?php echo $result['abonnement_reservation'] ? 'checked' : ''; ?>>
                        </div>
                    </td>
                    <td>
                        <div class="check_form">
                            <input class="taille_checkbox" type="checkbox" name="abonnement_devis" id="<?= $result['abonnement_cle'] ?>" onclick="checkboxClicked(name,id)" <?php echo $result['abonnement_devis'] ? 'checked' : ''; ?>>
                        </div>
                    </td>
                    <td>
                        <div class="check_form">
                            <input class="taille_checkbox" type="checkbox" name="abonnement_raison_perso" id="<?= $result['abonnement_cle'] ?>" onclick="checkboxClicked(name,id)" <?php echo $result['abonnement_raison_perso'] ? 'checked' : ''; ?>>
                        </div>
                    </td>
                    <td><input onclick="suppression('<?= $result['abonnement_cle'] ?>')" name="Supprimer" id="button_suppr" class="button_form" type="submit" value="Supprimer"></td>                              
                </tr>
                <?php 
            }
    
        ?>
            </tbody>
        </table>
        </div>
    </main>

</body>
</html>