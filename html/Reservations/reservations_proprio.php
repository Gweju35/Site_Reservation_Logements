<?php
session_start();
// Vérifiez si l'utilisateur est authentifié et est un propriétaire
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'proprietaire') {
    $proprietaire_id = $_SESSION['user_id'];
} else {
    // Redirigez l'utilisateur vers la page de login s'il n'est pas authentifié ou s'il n'est pas un propriétaire
    header("Location: login.php");
    exit();
}


// Connexion à la base de données
include('../../libs/connect_params.php');
    

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour récupérer tous les devis du propriétaire
    $sql = "SELECT d.*, c.compte_nom AS client_nom, c.compte_prenom AS client_prenom, l.* , c.compte_photo_profil as compte_photo_profil
    FROM alhaiz_breizh._devis d
    INNER JOIN alhaiz_breizh._logement l ON d.devis_id_logement = l.logement_id
    INNER JOIN alhaiz_breizh._proprietaire p ON l.logement_compte_id_proprio = p.compte_id
    INNER JOIN alhaiz_breizh._client c ON d.devis_id_compte_client = c.compte_id
    WHERE p.compte_id = :proprietaire_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':proprietaire_id', $proprietaire_id);
    $stmt->execute();

    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // $tz = reset(iterator_to_array(IntlTimeZone::createEnumeration('FR')));
    // $formatter = IntlDateFormatter::create(
    //     'fr_FR',
    //     IntlDateFormatter::FULL,
    //     IntlDateFormatter::NONE,
    //     $tz,
    //     IntlDateFormatter::GREGORIAN
    // );
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

    <script defer src="../assets/reservation.js"></script>
    <script defer src="../assets/index.js"></script>
    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/Reservation/reservation.css">
</head>

<body>
    
<!------------------HEADER------------------>

<?php
if ($_SESSION['user_type'] == 'client'){
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
            <h2>Liste des Réservations</h2>
        </div>
    </div>
    
    <form class="mesDevis" action="../Devis/devis_du_proprio.php" method="post">
            <input class="button_form" type="submit" value="Mes devis">
    </form>
    
    <div id="overlay" class="overlay"></div>

        <button class="filter button_form filter-button">Trier</button>

        <form method="post" action="traitementReservationProprio.php">
        <div id="popup2" class="popup2">
            <div class="popup2-content">
                <div class="content">
                    <div id="tri">
                        <h3>Trie :</h3>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="tri" value="croissant">
                                Trier par prix croissant
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="tri" value="decroissant">
                                Trier par prix décroissant
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="tri" value="recent">
                                Trier par date, plus récent
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="tri" value="vieux">
                                Trier par date, plus ancien
                            </label>
                        </div><br>
                        
                    </div>
                    <div class="les_boutons">               
                        <button type="submit" class="apply-button button_form">Appliquer</button>
                    </form> 
                    <form method="post" action="traitementReservationProprio.php">
                        <button type="submit" value="reset" id="reset" name="reset" class="apply-button button_form">Réinitialiser</button>
                    </form>
                    </div>
                    </div>
                </div>
            </div>
        </form>
        <div id="overlay2" class="overlay2"></div>
        <br><br>
        <div class="liste_logement">
        <?php         
        $nb_devis_payer = 0;
        if(!empty($_SESSION['reservations'])){
            $currentDate = date('Y-m-d'); // Récupère la date du jour
            foreach ($_SESSION['reservations'] as $reserv) { 
                /* Condition qui permet de prendre seulement en compte les devis payer par le client */
                if($reserv['devis_statut'] == 'PPC') {
                    $endDateTimestamp = strtotime($reserv['devis_date_fin']);
                    if ($endDateTimestamp > strtotime($currentDate)) {
                        
                        $nb_devis_payer++;
                        $nb_pers_total = $reserv['devis_nbr_personne'] + $reserv['devis_nbr_personne_sup']?>
                        <div class="logement">
                            <div class="image_logement">
                                <img src="<?php echo trim($reserv['logement_photo']) ?>"alt="Photo logement">
                            </div>
                            <div class="infos_logement">
                                <div class="photo_text">
                                    <form action="../Profil/afficher_client.php" method="post">
                                        <input type="hidden" name="id_compte" value="<?= $reserv['devis_id_compte_client'] ?>">
                                        <input class="photo_de_profil" class="visite_profil" type="image" src="<?php echo $reserv['compte_photo_profil']?>" alt="photo_profil">
                                    </form> 
                                    <p class="info_client">Le client est <?= $reserv['client_nom'] . ' ' . $reserv['client_prenom'] ?><br></p>
                                </div>
                                
                                <?php
                                    $dateDeb = $reserv['devis_date_debut'];
                                    $listDeb = explode("-",$dateDeb);
                                    $DateDebutVacances = $listDeb[2] . "/" . $listDeb[1] . "/" . $listDeb[0];
                                    
                                    $dateFin = $reserv['devis_date_fin'];
                                    $listFin = explode("-",$dateFin);
                                    $DateFinVacances = $listFin[2] . "/" . $listFin[1] . "/" . $listFin[0];
                                    ?>
                                <p class="reservation"> Réservé du <?= $DateDebutVacances?> au <?= $DateFinVacances?></p>
                                <p class="accroche"><?php echo $reserv['logement_accroche'] ?></p>
                                <!--Conditions pour l'affichage du nombre de personnes-->
                                <?php if($nb_pers_total == 1){ ?>
                                    <p class="accroche"><?php echo $nb_pers_total . " personne" ?></p>
                                    <?php } else { ?>
                                        <p class="accroche"><?php echo $nb_pers_total . " personnes" ?></p>
                                        <?php } ?>
                                        
                                        <!-- calcule du devis total -->
                                        <!-- condition qui permet de calculer le prix total ttc du devis sans erreur -->
                                        <?php 
                                /* Déclaratin des variables sur la TVA */
                                $tva = 20;
                                $tva_calcul = 1.2;
                                /* Initialisation des varaibles pour calculer le prix d'un devis TTC*/
                                $nuit_pers_ttc = $reserv['logement_prix_nuit_base'] * $reserv['devis_nb_nuit'] * $tva_calcul;
                                $nuit_pers_sup_ttc = 0;
                                $animal_ttc = 0;
                                $linge_ttc = 0;
                                $menage_ttc = 0;
                                $transport_ttc = 0;
                                
                                if($reserv['devis_nbr_personne_sup'] > 0) {
                                    $nuit_pers_sup_ttc = $reserv['charges_personne_sup'] * $reserv['devis_nbr_personne_sup'] * $tva_calcul;
                                } 
                                
                                if($reserv['service_animaux_domestique'] >= 1) { 
                                    $animal_ttc = $reserv['charges_animaux'] *$tva_calcul;
                                    
                                }
                                
                                if($reserv['service_linge'] == 1) { 
                                    $linge_ttc = $reserv['charges_linge'] * $tva_calcul;
                                }
                                
                                if($reserv['devis_service_menage'] == 1) {
                                    $menage_ttc = $reserv['charges_menage'] * $tva_calcul;
                                } 
                                
                                if($reserv['devis_service_transport'] == 1 ) { 
                                    $transport_ttc = $reserv['charges_transport'] * $tva_calcul;
                                }
                                ?>
                                <p class="prix_nuit"><?php echo round($reserv['devis_prix_ttc'],2). "€" ?></p>
                                
                                <div class="bouton_logement">
                                    <a class="un_logement" href="../Logements/afficher_logement.php?logement_id=<?php echo $reserv['logement_id']?>"><button class="button_form">Voir logement</button></a>
                                    <form action="../Facture/facture.php" method="post">
                                        <input type="hidden" name="devis_id" value="<?= $reserv['devis_id'] ?>">
                                        <input class="button_form" type="submit" value="Facture">
                                    </form>
                                </div>
                                
                            </div>                    
                        </div>
                    </div>
                    <?php } 
                }
            }
        }else{
                foreach ($reservations as $reserv) { 
                    $currentDate = date('Y-m-d'); // Récupère la date du jour

                    /* Condition qui permet de prendre seulement en compte les devis payer par le client */
                    if($reserv['devis_statut'] == 'PPC') {
                        $endDateTimestamp = strtotime($reserv['devis_date_fin']);
                        if ($endDateTimestamp > strtotime($currentDate)) {
                        $nb_devis_payer++;
                        $nb_pers_total = $reserv['devis_nbr_personne'] + $reserv['devis_nbr_personne_sup']?>
                            <div class="logement">
                                <div class="image_logement">
                                    <img src="<?php echo trim($reserv['logement_photo']) ?>"alt="Photo logement">
                                </div>
                                <div class="infos_logement">
                                    <div class="photo_text">
                                        <form action="../Profil/afficher_client.php" method="post">
                                            <input type="hidden" name="id_compte" value="<?= $reserv['devis_id_compte_client'] ?>">
                                            <input class="photo_de_profil" class="visite_profil" type="image" src="<?php echo $reserv['compte_photo_profil']?>" alt="photo_profil">
                                        </form> 
                                        <p class="info_client">Le client est <?= $reserv['client_nom'] . ' ' . $reserv['client_prenom'] ?><br></p>
                                    </div>
    
                                    <?php
                                        $dateDeb = $reserv['devis_date_debut'];
                                        $listDeb = explode("-",$dateDeb);
                                        $DateDebutVacances = $listDeb[2] . "/" . $listDeb[1] . "/" . $listDeb[0];
    
                                        $dateFin = $reserv['devis_date_fin'];
                                        $listFin = explode("-",$dateFin);
                                        $DateFinVacances = $listFin[2] . "/" . $listFin[1] . "/" . $listFin[0];
                                    ?>
                                    <p class="reservation"> Réservé du <?= $DateDebutVacances?> au <?= $DateFinVacances?></p>
                                    <p class="accroche"><?php echo $reserv['logement_accroche'] ?></p>
                                    <!--Conditions pour l'affichage du nombre de personnes-->
                                    <?php if($nb_pers_total == 1){ ?>
                                        <p class="accroche"><?php echo $nb_pers_total . " personne" ?></p>
                                    <?php } else { ?>
                                        <p class="accroche"><?php echo $nb_pers_total . " personnes" ?></p>
                                    <?php } ?>
                                    
                                    <!-- calcule du devis total -->
                                    <!-- condition qui permet de calculer le prix total ttc du devis sans erreur -->
                                    <?php 
                                    /* Déclaratin des variables sur la TVA */
                                    $tva = 20;
                                    $tva_calcul = 1.2;
                                    /* Initialisation des varaibles pour calculer le prix d'un devis TTC*/
                                    $nuit_pers_ttc = $reserv['logement_prix_nuit_base'] * $reserv['devis_nb_nuit'] * $tva_calcul;
                                    $nuit_pers_sup_ttc = 0;
                                    $animal_ttc = 0;
                                    $linge_ttc = 0;
                                    $menage_ttc = 0;
                                    $transport_ttc = 0;
                                    
                                    if($reserv['devis_nbr_personne_sup'] > 0) {
                                        $nuit_pers_sup_ttc = $reserv['charges_personne_sup'] * $reserv['devis_nbr_personne_sup'] * $tva_calcul;
                                    } 
    
                                    if($reserv['service_animaux_domestique'] >= 1) { 
                                        $animal_ttc = $reserv['charges_animaux'] *$tva_calcul;
                                        
                                    }
                                    
                                    if($reserv['service_linge'] == 1) { 
                                        $linge_ttc = $reserv['charges_linge'] * $tva_calcul;
                                    }
    
                                    if($reserv['devis_service_menage'] == 1) {
                                        $menage_ttc = $reserv['charges_menage'] * $tva_calcul;
                                    } 
    
                                    if($reserv['devis_service_transport'] == 1 ) { 
                                        $transport_ttc = $reserv['charges_transport'] * $tva_calcul;
                                    }
    
                                    ?>
                                    <p class="accroche"><?php echo round($reserv['devis_prix_ttc'],2). "€ TTC" ?></p>
    
                                    <div class="bouton_logement">
                                        <a class="un_logement" href="../Logements/afficher_logement.php?logement_id=<?php echo $reserv['logement_id']?>"><button class="button_form">Voir logement</button></a>
                                        <form action="../Facture/facture.php" method="post">
                                            <input type="hidden" name="devis_id" value="<?= $reserv['devis_id'] ?>">
                                            <input class="button_form" type="submit" value="Facture">
                                        </form>
                                    </div>
    
                                </div>                    
                            </div>
                    </div>
                <?php } }
                }
            }
            if($nb_devis_payer == 0) { ?>
                <h1>Aucune réservation faite sur vos logements</h1>
            <?php }
        ?>
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
</html>