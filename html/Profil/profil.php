<?php
session_start();
$headerVisiteur = '
<header>
        <div class="burger">
            <a href="#" id="openBtn">
                <span class="burger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>
        </div>
        <img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png" class="image_logo">
        <nav>
            <div id="mySidenav" class="sidenav">
                <a id="closeBtn" href="#" class="close">×</a>
                <ul>
                    <li id="accueil">
                        <a class="nav_button" data-element-id="accueil" href="../index.php">
                            <span class="icon house"></span>
                            <span id="txtAccueil">Accueil</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li id="se_connecter">
                        <a class="nav_button" data-element-id="se_connecter" href="../Profil/login.php">
                            <span class="icon account"></span>
                            <span>Se connecter</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>';
$_SESSION['headerVisiteur'] = $headerVisiteur;

$headerProprietaire = '<header>
<div class="burger">
    <a href="#" id="openBtn">
        <span class="burger-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>
</div>
<img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png" class="image_logo">
<nav>
    <div id="mySidenav" class="sidenav">
        <a id="closeBtn" href="#" class="close">×</a>
        <ul>
            <li id="accueil">
                <a class="nav_button" data-element-id="accueil" href="../index.php">
                    <span class="icon house"></span>
                    <span id="txtAccueil">Accueil</span>
                </a>
            </li>
            <li id="mes_logements">
                <a class="nav_button" data-element-id="mes_logements" href="../Logements/logement.php">
                    <span class="icon bed"></span>
                    <span>Mes logements</span>
                </a>
            </li>
            <li id="reservations">
                <a class="nav_button" data-element-id="reservations" href="../Reservations/reservations_proprio.php">
                    <span class="icon reservationIcone"></span>
                    <span>Réservations</span>
                </a>
            </li>
            <li id="calendrier">
                <a class="nav_button" data-element-id="calendrier" href="../Calendrier/calendrier.php">
                    <span class="icon calendar"></span>
                    <span>Calendrier</span>
                </a>
            </li>
        </ul>
        <ul>
            <li id="profil">
                <a class="nav_button" data-element-id="profil" href="../Profil/profil.php">
                    <span class="icon account"></span>
                    <span>Profil</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
</header>';
$_SESSION['headerProprietaire'] = $headerProprietaire;

$headerClient = '<header>
<div class="burger">
    <a href="#" id="openBtn">
        <span class="burger-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>
</div>
<img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png" class="image_logo">
<nav>
    <div id="mySidenav" class="sidenav">
        <a id="closeBtn" href="#" class="close">×</a>
        <ul>
            <li id="accueil">
                <a class="nav_button" data-element-id="accueil" href="../index.php">
                    <span class="icon house"></span>
                    <span id="txtAccueil">Accueil</span>
                </a>
            </li>
            <li id="reservations">
                <a class="nav_button" data-element-id="reservations" href="../Reservations/reservation_client.php">
                    <span class="icon reservationIcone"></span>
                    <span>Réservations</span>
                </a>
            </li>
        </ul>
        <ul>
            <li id="profil">
                <a class="nav_button" data-element-id="profil" href="../Profil/profil.php">
                    <span class="icon account"></span>
                    <span>Profil</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
</header>';
$_SESSION['headerClient'] = $headerClient;

$user_id = $_SESSION['user_id'];
// toutes les requêtes SQL

include("../../libs/connect_params.php");
try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if($_SESSION['user_type'] == 'proprietaire') {
        $sql = "SELECT proprietaire_langue_parlee FROM alhaiz_breizh._proprietaire WHERE compte_id = :compte_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':compte_id', $_SESSION['user_id']);
       
        $stmt->execute();
    
        $langue_parlee = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $sql = "SELECT * FROM alhaiz_breizh._compte WHERE compte_id = :compte_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':compte_id', $_SESSION['user_id']);
   
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
/* condition sur le user type 
client celle en bas 
proprio rependre afficher prorpio*/
    if($_SESSION['user_type']=='client'){
        // Récuperer donné de la table avis
        $sql = "SELECT COUNT(*) FROM alhaiz_breizh._avis WHERE avis_id_compte_client = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();

        $avis = $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
        // Récuperer donné de la table avis
        $sql = "SELECT COUNT(*) FROM alhaiz_breizh._avis WHERE avis_id_compte_proprietaire = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();

        $avis = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $sql = "SELECT * FROM alhaiz_breizh._logement WHERE logement_compte_id_proprio = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $logements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query = "SELECT r.*, l.*, d.*
                  FROM alhaiz_breizh._reservation AS r
                  JOIN alhaiz_breizh._logement AS l ON r.reservation_logement_id = l.logement_id
                  JOIN alhaiz_breizh._devis AS d ON r.reservation_devis_id = d.devis_id  where d.devis_id_compte_client = " . $_SESSION['user_id'] . " ";
    $stmt = $pdo->query($query);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    if ($user) {
        // Vérifiez si l'utilisateur est un client ou un propriétaire en recherchant son ID dans les tables correspondantes
    
        $_SESSION['user_photo_profil'] = $user['compte_photo_profil'];

        // Redirigez l'utilisateur vers la page de profil

    }
} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
// Vérifiez si l'utilisateur est authentifié
if (isset($_SESSION['user_id'])) {
    $user_nom = $_SESSION['user_nom'];
    $user_prenom = $_SESSION['user_prenom'];
    $user_photo_de_profil = $_SESSION['user_photo_profil'];
    $user_pseudo = $_SESSION['user_pseudo'];
    $type_user = $_SESSION['user_type'];
} else {
    // Redirigez l'utilisateur vers la page de login s'il n'est pas authentifié
    header("Location: ../Profil/login.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script defer src="../assets/index.js"></script>

    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/profil.css">
    <title>Alhaiz Breizh</title>
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">
</head>
<body>
    
<!------------------HEADER------------------>

<?php
if (isset($_SESSION['user_type']) == false){
    echo $_SESSION['headerVisiteur'];
} else if ($_SESSION['user_type'] == 'client'){  // affiche un header différent selon le type d'utilisateur
    echo $_SESSION['headerClient'];
} else {
    echo $_SESSION['headerProprietaire'];
}
?>

<!------------------MAIN------------------>

<main>
    
    <div class="profil">
        
        <div class="case_profil">
            
            <img class="photo_de_profil" src="<?php echo $user_photo_de_profil; ?>" alt="Photo de Profil">

            <div class="identite">
                <h2 class="prenom_nom"><?php echo $user_prenom." ".$user_nom ?></h2>
                <p class="pseudo"><?php echo "<i>" . $user_pseudo . "</i>" ?></p>
                <p class="type_user"><?php echo ($_SESSION['user_type']=='client') ? 'Client' : 'Propriétaire' ?></p>
            </div>

            <?php $adresse = explode(", ", $user['compte_adresse']);
                $deuxPremiers = $adresse[2][0] . $adresse[2][1];?>

            <div class="infos_secondaires">
                <?php include '../assets/ressources/data/depts-fix.php';
                foreach($depts as $numero => $departement) { 
                    if ($deuxPremiers == $numero) {
                        $lieuDeVie = $departement;
                        $num = $numero;}}?>
                
                <div>
                    <span class="icon pin"></span>
                    <p>Habite dans <?php echo $lieuDeVie . ' (' . $num . ')'?> </p>
                </div>  <!--// affiche le département de vie de l'utilisateur-->
                
                <div>
                    <span class="icon hotel"></span>
                    <?php if($_SESSION['user_type'] == 'proprietaire') { ?> <!-- // affiche le nombre de reservations ou de logement selon le type d'utilisateur-->
                        <p><?php if(count($logements) >1){ echo count($logements) . " Annonces";} else{echo count($logements) . " Annonce";}?> </p>
                    <?php } else { ?>
                        <p><?php if(count($reservations) >1){ echo count($reservations) . " Réservations";} else{echo count($reservations) . " Réservation";}?></p>
                    <?php } ?>
                </div>

                <div>
                    <span class="icon nb_avis"></span>
                    <?php if($avis['count'] != null) { 
                            if($_SESSION['user_type'] == 'client'){ ?>
                                <p>Avis laissé(s) : <?php echo $avis['count'] ; ?></p>
                            <?php }
                            else { ?>
                                <p>Avis reçu(s) : <?php echo $avis['count'] ; ?></p>
                            <?php }?>
                    <?php } 
                    else { ?>
                        <p>Aucun Avis</p>
                    <?php } ?>
                </div>

                <?php if($_SESSION['user_type'] == 'proprietaire') { ?>
                <div>
                    <span class="icon language"></span>
                    <p>
                        <?php if($langue_parlee['proprietaire_langue_parlee'] != ""){  // Affiche les langues parlées si il y en a 
                            echo $langue_parlee['proprietaire_langue_parlee']; 
                        } else {
                            echo "L'utilisateur n'a pas précisé les langues qu'il parle.";
                        } ?>
                    </p>
                </div>
                <?php } ?>
            </div>

            <div class="profil_button">

                <div class="modifier_compte">
                    <?php if($_SESSION['user_type'] == 'proprietaire') { ?>
                    <form action="../Profil/modif_compte_proprietaire.php" method="post">
                        <input class="button_form" type="submit" value="Modifier mes informations">  
                    </form>                                                                             <!--//Bouton pour modifier le compte-->
                    <?php } else { ?>
                    <form action="../Profil/modif_compte_client.php" method="post">
                        <input class="button_form" type="submit" value="Modifier mes informations">
                    </form>
                    <?php } ?>
                </div>

                <div class="deconnexion">
                    <form action="../Profil/deconnexion.php" method="post">
                        <input class="button_form" type="submit" value="Me déconnecter">        <!--//Bouton de déconnexion-->
                    </form>
                </div>

                <?php if ($_SESSION['user_type'] == 'proprietaire'){ ?>
                    <div class="api">
                        <form action="../API/gestion_cles_api.php" method="post">
                            <input class="button_form" type="submit" value="API">        <!--//Bouton pour api-->
                        </form>
                    </div>
                <?php } ?>    
            </div>

        </div>
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
