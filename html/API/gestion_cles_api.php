<?php
session_start();

include('../../libs/connect_params.php');

try {

    $compte_id = $_SESSION['user_id'];

    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM alhaiz_breizh._cles_api WHERE cles_api_id_compte = :compte_id");
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
    <title>API</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="api_script.js"></script>
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
        <a href="../Profil/profil.php">
            <div class="cercle_retour"><img src="../assets/ressources/icons/left-arrow.svg" alt="icone back" /></div>
        </a>

            <div class="titre_page">
                <h2>Gestion des clés d'API</h2>
            </div>
        </div>
        <button id="btn_generer_api" class="button_form">Générer clé API</button>      
        
        <div class="table_bloc">
        <table>
            <thead>
                <th>Clés API</th>
                <th>Droit Consultation</th>
                <th>Droit Vérification</th>
                <th>Droit Modification</th>
                <th>Droit Apirator</th>
                <th>Supprimer la clé</th>
            </thead>
            <tbody>
        <?php
            foreach ($results as $result){
                ?>  
                <tr> 
                    <td><?= $result['cles_api_normal'] ?></td>
                    <td><input class="taille_checkbox" type="checkbox" name="cles_api_consultation_logement" id="<?= $result['cles_api_id'] ?>" onclick="checkboxClicked(name,id)" <?php echo $result['cles_api_consultation_logement'] ? 'checked' : ''; ?>></td>
                    <td><input class="taille_checkbox" type="checkbox" name="cles_api_verif_dispo" id="<?= $result['cles_api_id'] ?>" onclick="checkboxClicked(name,id)" <?php echo $result['cles_api_verif_dispo'] ? 'checked' : ''; ?>></td>
                    <td><input class="taille_checkbox" type="checkbox" name="cles_api_mise_indispo" id="<?= $result['cles_api_id'] ?>" onclick="checkboxClicked(name,id)" <?php echo $result['cles_api_mise_indispo'] ? 'checked' : ''; ?>></td>
                    <td><input class="taille_checkbox" type="checkbox" name="cles_api_apirator" id="<?= $result['cles_api_id'] ?>" onclick="checkboxClicked(name,id)" <?php echo $result['cles_api_apirator'] ? 'checked' : ''; ?>  ></td>
                    <td><input onclick="suppression(<?= $result['cles_api_id'] ?>)" name="Supprimer" id="button_suppr" class="button_form" type="submit" value="Supprimer"></td>                             
                </tr>
                <?php
            }    
        ?>
            </tbody>
        </table>
        </div>
    </main>

    <footer>
        <div class="footer">&copy ALHaIZ Breizh
            <a href="https://www.iubenda.com/privacy-policy/12300064" class="iubenda-white iubenda-noiframe iubenda-embed iubenda-noiframe " title="Politique de confidentialité ">Politique de confidentialité</a>
            <script type="text/javascript">
                (function(w, d) {
                    var loader = function() {
                        var s = d.createElement("script"),
                            tag = d.getElementsByTagName("script")[0];
                        s.src = "https://cdn.iubenda.com/iubenda.js";
                        tag.parentNode.insertBefore(s, tag);
                    };
                    if (w.addEventListener) {
                        w.addEventListener("load", loader, false);
                    } else if (w.attachEvent) {
                        w.attachEvent("onload", loader);
                    } else {
                        w.onload = loader;
                    }
                })(window, document);
            </script>
        </div>

    </footer>
</body>
</html>