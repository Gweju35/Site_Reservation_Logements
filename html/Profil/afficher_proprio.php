<?php
session_start();
// Connexion à la base de données
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les logements du propriétaire
    $sql = "SELECT * FROM alhaiz_breizh._compte 
    NATURAL JOIN alhaiz_breizh._proprietaire
    WHERE compte_id = :proprio_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':proprio_id', $_POST['id_compte']); 
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récuperer donné de la table avis
    $sql = "SELECT COUNT(*) FROM alhaiz_breizh._avis WHERE avis_id_compte_proprietaire = :proprio_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':proprio_id',  $_POST['id_compte']);
    $stmt->execute();

    $avis = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Alhaiz Breizh</title>
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script defer src="../assets/index.js"></script>
    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/Profil/afficher.css">
    
    
</head>
<body>
<!------------------HEADER------------------>

<?php
if ($_SESSION['user_type'] == 'visiteur'){
    echo $_SESSION['headerVisiteur'];
} else if ($_SESSION['user_type'] == 'client'){
    echo $_SESSION['headerClient'];
} else {
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
            <h2>Retour</h2>
        </div>
    </div>

    <div class="profil">
        <img class="photo_de_profil" src="<?php echo $user['compte_photo_profil']; ?>" alt="Photo de Profil">
        <div class="case_profil">
            <div class="identite">
                <h2 class="prenom_nom"><?php echo $user['compte_prenom']." ".$user['compte_nom'] ?></h2>
                <p class="pseudo"><?php echo "<i>" . $user['compte_pseudo'] . "</i>" ?></p>
                <p class="type_user">Propriétaire</p>
            </div>

            <?php
                $adresse = explode(", ", $user['compte_adresse']);
                $deuxPremiers = $adresse[2][0] . $adresse[2][1];
            ?>

            <div class="infos_secondaires">
                <?php include '../assets/ressources/data/depts-fix.php';
                
            
                foreach($depts as $numero => $departement) { 
                   
                    if ($deuxPremiers == $numero) {
                        
                        $lieuDeVie = $departement;
                        $num = $numero;
                   }
                }?>
                
                <div><span class="icon pin"></span><p>Habite dans <?php echo $lieuDeVie . ' (' . $num . ')'?> </p></div>  <!--// affiche le département de vie de l'utilisateur-->

                <div>
                    <span class="icon nb_avis"></span>
                    <?php if($avis['count'] != null) { ?>
                        <p>Avis reçu(s) : <?php echo $avis['count'] ; ?></p>
                    <?php } 
                    else { ?>
                        <p>Aucun Avis</p>
                    <?php } ?>
                </div>                <div>
                    <span class="icon language"></span>
                    <p>
                        
                        <?php if($user['proprietaire_langue_parlee'] != ""){  // Affiche les langues parlées si il y en a 
                            echo $user['proprietaire_langue_parlee']; 
                        } else {
                            echo "L'utilisateur n'a pas précisé les langues qu'il parle.";
                        } ?>
                        
                    </p>
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
    <script src="../assets/carousel.js"></script>
</body>
</html>