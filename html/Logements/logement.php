<?php
session_start();

// Vérifiez si l'utilisateur est authentifié
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'proprietaire') {
    $user_id = $_SESSION['user_id'];
} else {
    // Redirigez l'utilisateur vers la page de login s'il n'est pas authentifié ou s'il n'est pas un propriétaire
    header("Location: ../Profil/login.php");
    exit();
}
// Connexion à la base de données
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les logements du propriétaire
    $sql = "SELECT * FROM alhaiz_breizh._logement WHERE logement_compte_id_proprio = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $logements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récuperer les photos du logement 
    $sql = "SELECT * FROM alhaiz_breizh._photo WHERE logement_id = :logement_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':logement_id', $logement_id);
    $stmt->execute();

    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/Logement/logement.css">

    <!-- les étoiles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script defer src="../assets/index.js"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

</head>

<body>

    <!------------------HEADER------------------>

    <?php

    if (isset($_SESSION['user_type']) == false) {
        echo $_SESSION['headerVisiteur'];
    } else if ($_SESSION['user_type'] == 'client') {
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
                <h2>Logements du propriétaire</h2>
            </div>
        </div>


        <!--Si l'utilisateur est de type "proprietaire", affichez le lien "Logement"-->

        <form action="../Logements/ajout_logement.php" method="post">
            <input class="button_form" type="submit" value="Ajouter un logement">
        </form>


        <?php if (count($logements) > 0) { ?>
            <div class="liste_logements">
                <?php foreach ($logements as $logement) { 
                    // Récuperer la  moyenne des notes du logement 
                    $sql = "SELECT AVG(avis_note), COUNT(*) FROM alhaiz_breizh._avis WHERE avis_id_logement = :logement_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':logement_id', $logement['logement_id']);
                    $stmt->execute();

                    $avis = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <div class="un_logement">
                        <a class="logement" href="../Logements/afficher_logement.php?logement_id=<?php echo $logement['logement_id'] ?>">
                            <div class="image_logement">
                                <img src="<?php echo trim($logement['logement_photo']) ?>" alt="Photo logement">
                            </div>
                            <div class="infos_logement">
                                <div class="cercle_statut"></div>
                                <h2 class="accroche"><?php echo $logement['logement_accroche'] ?></h2>
                                <p class="description"><?php echo $logement['logement_description'] ?></p>
                                <?php 
                                    if($avis['avg'] != null){ ?>
                                        <p class="note"><?php echo $avis['avg'] . " / 5 "; ?><i class='fas fa-star'></i> (<?php echo $avis['count'] ; ?> avis)</p>
                                    <?php }
                                ?>
                                <p class="nombre_personne_max"><?php echo $logement['logement_personne_max'] . " personnes" ?></p>
                                <p class="prix_nuit"><?php echo $logement['logement_prix_nuit_base'] . "€ par nuit" ?></p>
                            </div>

                        </a>
                        <div class="bouton_logement">
                            <!--Ajoutez un bouton "Modifier" pour ce logement-->
                            <div>
                                <form action="./logement_modif.php" method="post">
                                    <input type="hidden" name="logement_id" value="<?= $logement['logement_id'] ?>">
                                    <input class="button_form" type="submit" value="Modifier">
                                </form>
                            </div>
                            <div>
                                <?php
                                if ($logement['logement_statut_ligne'] == true) { ?>
                                    <input onclick="mettreHorsLigne(<?= $logement['logement_id']; ?>)" name="<?= $logement['logement_id']; ?>" class="button_form button_off" type="submit" value="Mettre hors ligne">
                                <?php } else { ?>
                                    <input onclick="mettreEnLigne(<?= $logement['logement_id']; ?>)" name="<?= $logement['logement_id']; ?>" class="button_form button_on" type="submit" value="Mettre en ligne">
                                <?php } ?>
                            </div>
                            <div class="popup" id="popup1">
                                <div class="content">
                                    <h1><b>Logement hors-ligne</b></h1>
                                </div>
                            </div>
                            <div class="popup" id="popup2">
                                <div class="content">
                                    <h1><b>Logement en ligne</b></h1>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
    <?php }
            } ?>
    </div>

    <script>
        function mettreEnLigne(logementId) {
            $.ajax({
                url: 'logement_ligne.php',
                method: 'POST',
                data: {
                    logement_id: logementId
                },
                success: function(response) {
                    document.getElementById("popup2").classList.add("active");

                    var nouveauBouton = document.createElement('input');
                    nouveauBouton.setAttribute('type', 'submit');
                    nouveauBouton.setAttribute('class', 'button_form');
                    nouveauBouton.setAttribute('onclick', `mettreHorsLigne(${logementId})`);
                    nouveauBouton.setAttribute('id', 'button_off_pop_up2');
                    nouveauBouton.setAttribute('name', logementId);
                    nouveauBouton.value = 'Mettre hors-ligne';

                    var ancienBouton = document.getElementsByName(logementId)[0];
                    ancienBouton.parentNode.replaceChild(nouveauBouton, ancienBouton);

                    setTimeout(function() {
                        document.getElementById("popup2").classList.remove("active");
                    }, 2000);

                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la mise en ligne', error);
                }
            });
        }

        function mettreHorsLigne(logementId) {
            $.ajax({
                url: 'logement_hors_ligne.php',
                method: 'POST',
                data: {
                    logement_id: logementId
                },
                success: function(response) {
                    document.getElementById("popup1").classList.add("active");

                    var nouveauBouton = document.createElement('input');
                    nouveauBouton.setAttribute('type', 'submit');
                    nouveauBouton.setAttribute('class', 'button_form');
                    nouveauBouton.setAttribute('onclick', `mettreEnLigne(${logementId})`);
                    nouveauBouton.setAttribute('id', 'button_on_pop_up1');
                    nouveauBouton.setAttribute('name', logementId);
                    nouveauBouton.value = 'Mettre en ligne';

                    var ancienBouton = document.getElementsByName(logementId)[0];
                    ancienBouton.parentNode.replaceChild(nouveauBouton, ancienBouton);

                    setTimeout(function() {
                        document.getElementById("popup1").classList.remove("active");
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la mise en ligne', error);
                }
            });

        }
    </script>

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