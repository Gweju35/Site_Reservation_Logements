<?php
session_start();
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
    <link rel="stylesheet" href="../assets/pages_css/Reservation/reservation.css">

    <!-- les étoiles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

    <!-- Bouton de devis client -->
    <form class="mesDevis"action="../Devis/devis_du_client.php" method="post">
        <input class="button_form" type="submit" value="Mes devis">
    </form>
    
    

    <?php
    include('../../libs/connect_params.php');
    // Établir une connexion à la base de données
    try {
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les réservations depuis la base de données
        $query = "SELECT r.*, l.*, d.*
                  FROM alhaiz_breizh._reservation AS r
                  JOIN alhaiz_breizh._logement AS l ON r.reservation_logement_id = l.logement_id
                  JOIN alhaiz_breizh._devis AS d ON r.reservation_devis_id = d.devis_id  where d.devis_id_compte_client = " . $_SESSION['user_id'] . " ";
        $stmt = $pdo->query($query);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);


        function afficherBarreEtoiles($note) {
            // Calcul du nombre d'étoiles pleines
            $nbEtoilesRemplies = floor($note);
        
            // Calcul du nombre d'étoiles vides
            $nbEtoilesVides = 5 - $nbEtoilesRemplies;
        
            // Affichage des étoiles pleines
            for ($i = 0; $i < $nbEtoilesRemplies; $i++) {
                echo "<i class='fas fa-star etoile'></i>";
            }
        
            // Affichage de la demi-étoile si nécessaire
            if ($note - $nbEtoilesRemplies >= 0.5) {
                echo "<i class='fas fa-star-half-alt etoile'></i>";
                $nbEtoilesVides--; // Réduire le nombre d'étoiles vides
            }
        
            // Affichage des étoiles vides
            for ($i = 0; $i < $nbEtoilesVides; $i++) {
                echo "<i class='far fa-star etoile etoile-vide'></i>";
            }
        }
        
            ?>

        <div id="overlay" class="overlay"></div>
        <div class="filter">
            <button class="filter-button">Trier</button>
        </div>

        
        <form method="post" action="traitementReservationClient.php">
        <div id="popup2" class="popup2">
            <div class="popup2-content">
                <div class="content">
                    <div id="tri">
                        <h3>Tris :</h3>
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
                    <form method="post" action="traitementReservationClient.php">
                        <button type="submit" value="reset" id="reset" name="reset" class="apply-button button_form">Réinitialiser</button>
                    </form>
                    </div>
                    </div>
                </div>
            </div>
        </form>
        <div id="overlay2" class="overlay2"></div>
            <br><br>

            <?php 
        if(!empty($_SESSION['reservations'])){
            if (count($_SESSION['reservations']) > 0) { ?>
                <div class="liste_logement">
                <?php foreach ($_SESSION['reservations'] as $reserv) { 
                    $nb_pers_total = $reserv['devis_nbr_personne'] + $reserv['devis_nbr_personne_sup']?>
                        <div class="logement">
                            <div class="image_logement">
                                <img src="<?php echo trim($reserv['logement_photo']) ?>"alt="Photo logement">
                            </div>
                            <?php
                                $dateDeb = $reserv['devis_date_debut'];
                                $listDeb = explode("-",$dateDeb);
                                $DateDebutVacances = $listDeb[2] . "/" . $listDeb[1] . "/" . $listDeb[0];
    
                                $dateFin = $reserv['devis_date_fin'];
                                $listFin = explode("-",$dateFin);
                                $DateFinVacances = $listFin[2] . "/" . $listFin[1] . "/" . $listFin[0];
                            ?>
                            <div class="infos_logement">
                                <h3 class="reservation"> Réservé du <?= $DateDebutVacances?> au <?= $DateFinVacances?></h3>
                                <p class="accroche"><?php echo $reserv['logement_accroche'] ?></p>
                                <p class="nombre_personne_max"><?php echo $reserv['logement_description'] ?></p>
                                <br>
                                <p class="nombre_personne_max"><?php echo "Adresse : " . $reserv['logement_adresse'] . ", " . $reserv['logement_ville'] . ", " . $reserv['logement_code_postal'] ?></p><br>
                                <!--Conditions pour l'affichage du nombre de personnes-->
                                <?php if($nb_pers_total == 1){ ?>
                                    <p class="nombre_personne_max"><?php echo $nb_pers_total . " personne" ?></p>
                                <?php } else { ?>
                                    <p class="nombre_personne_max"><?php echo $nb_pers_total . " personnes" ?></p>
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
                                <?php
                                    $logement_id = $reserv['logement_id'];
                                    $logement_url = "../Logements/afficher_logement.php?logement_id=$logement_id";
                                ?>
                                <a class="button_form" href="<?php echo $logement_url; ?>">Voir logement</a>                                    <form action="../Facture/facture.php" method="post">
                                        <input type="hidden" name="devis_id" value="<?= $reserv['devis_id'] ?>">
                                        <input class="button_form" type="submit" value="Facture">
                                    </form>
                                    <?php 
                                    $currentDate = date('Y-m-d'); // Récupère la date du jour
                                        if ($reserv['devis_date_fin'] <= $currentDate) {
                                            // Récuperer donné de la table avis
                                            $sql = "SELECT * FROM alhaiz_breizh._avis WHERE avis_id_logement = :logement_id AND avis_id_compte_client = :id_client";
                                            $stmt = $pdo->prepare($sql);
                                            $stmt->bindParam(':logement_id', $reserv['logement_id']);
                                            $stmt->bindParam(':id_client', $reserv['devis_id_compte_client']);
                                            $stmt->execute();

                                            $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            /* condition pour cacher le bouton "laissez un avis si le client a déjà laissez un avis */
                                            if( $avis == null || $reserv['devis_id_compte_client'] != $avis[0]['avis_id_compte_client']){ ?>
                                                <button class="button_form" id="myButton_<?php echo $reserv['reservation_id'] ?>" onclick="handleButtonClick('<?php echo $reserv['reservation_id'] ?>', 'myButton_<?php echo $reserv['reservation_id'] ?>')">Laissez un avis</button>
                                            <?php }
                                        ?>
                                    </div>

                                    
                                    
                                <div id="<?php echo $reserv['reservation_id'] ?>" class="hidden laissez_avis">
                                    <h2>Laissez un avis : </h2>
                                    <form action="./traitementLaissezAvis.php" method="post">
                                        <div class="rating">
                                            <input type="radio" id="star5_<?php echo $reserv['reservation_id'] ?>" name="rating" value="5" required><label for="star5_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star4_<?php echo $reserv['reservation_id'] ?>" name="rating" value="4" required><label for="star4_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star3_<?php echo $reserv['reservation_id'] ?>" name="rating" value="3" required><label for="star3_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star2_<?php echo $reserv['reservation_id'] ?>" name="rating" value="2" required><label for="star2_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star1_<?php echo $reserv['reservation_id'] ?>" name="rating" value="1" required><label for="star1_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                        </div>
                                        <div class="soumettre_avis">
                                            <input type="hidden" name="logement_id" value=<?php echo $reserv['logement_id'] ?>>
                                            <input type="hidden" name="client_id" value=<?php echo $reserv['devis_id_compte_client'] ?>>
                                            <input type="hidden" name="proprio_id" value=<?php echo $reserv['logement_compte_id_proprio'] ?>>
                                            <input type="hidden" class="ratingValueInput" name="ratingValue">
                                            <textarea class="commentaire" type="text" name="commentaire" id="commentaire" required minlength="10" maxlength="500"></textarea>
                                            <input id="button_sub" class="button_form" type="submit" value="Soummettre">
                                        </div>
                                    </form>
                                </div>
                                <?php } ?>
                            </div>                    
                        </div>
                </div>
            <?php } } 
            else {
                ?>
                <h1>Aucune réservation pour le moment</h1>
            <?php } 
        }elseif (count($reservations) > 0) { ?>
            <section class="liste_logement">
                
            <?php 
                $currentDate = date('Y-m-d'); // Récupère la date du jour
                foreach ($reservations as $reserv) { 
                        $nb_pers_total = $reserv['devis_nbr_personne'] + $reserv['devis_nbr_personne_sup']?>
                        <div class="logement">
                            <div class="image_logement">
                                <img src="<?php echo trim($reserv['logement_photo']) ?>"alt="Photo logement">
                            </div>
                            <?php
                                $dateDeb = $reserv['devis_date_debut'];
                                $listDeb = explode("-",$dateDeb);
                                $DateDebutVacances = $listDeb[2] . "/" . $listDeb[1] . "/" . $listDeb[0];

                                $dateFin = $reserv['devis_date_fin'];
                                $listFin = explode("-",$dateFin);
                                $DateFinVacances = $listFin[2] . "/" . $listFin[1] . "/" . $listFin[0];
                            ?>
                            <div class="infos_logement">
                                <h3 class="reservation"> Réservé du <?= $DateDebutVacances?> au <?= $DateFinVacances?></h3>
                                <p class="accroche"><?php echo $reserv['logement_accroche'] ?></p>
                                <p class="nombre_personne_max"><?php echo $reserv['logement_description'] ?></p>
                                <br>
                                <p class="nombre_personne_max"><?php echo "Adresse : " . $reserv['logement_adresse'] . ", " . $reserv['logement_ville'] . ", " . $reserv['logement_code_postal'] ?></p><br>
                                <!--Conditions pour l'affichage du nombre de personnes-->
                                <?php if($nb_pers_total == 1){ ?>
                                    <p class="nombre_personne_max"><?php echo $nb_pers_total . " personne" ?></p>
                                <?php } else { ?>
                                    <p class="nombre_personne_max"><?php echo $nb_pers_total . " personnes" ?></p>
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
                                    <?php
                                    $currentDate = date('Y-m-d'); // Récupère la date du jour
                                        if ($reserv['devis_date_fin'] <= $currentDate) {
                                            // Récuperer donné de la table avis
                                            $sql = "SELECT * FROM alhaiz_breizh._avis WHERE avis_id_logement = :logement_id AND avis_id_compte_client = :id_client";
                                            $stmt = $pdo->prepare($sql);
                                            $stmt->bindParam(':logement_id', $reserv['logement_id']);
                                            $stmt->bindParam(':id_client', $reserv['devis_id_compte_client']);
                                            $stmt->execute();

                                            $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            /* condition pour cacher le bouton "laissez un avis si le client a déjà laissez un avis */
                                            if( $avis == null || $reserv['devis_id_compte_client'] != $avis[0]['avis_id_compte_client']){ ?>
                                                <button class="button_form" id="<? echo "myButton".$reserv['reservation_id'] ?>" onclick="toggleCode(<? echo $reserv['reservation_id'] ?>, <? echo 'myButton'.$reserv['reservation_id'] ?>)">Laissez un avis</button>
                                            <?php }
                                        ?>
                                    </div>
                                    
                                    <div id="<?php echo $reserv['reservation_id'] ?>" class="hidden laissez_avis">
                                    <h2>Laissez un avis : </h2>
                                    <form action="./traitementLaissezAvis.php" method="post">
                                        <div class="rating">
                                            <input type="radio" id="star5_<?php echo $reserv['reservation_id'] ?>" name="rating" value="5" required><label for="star5_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star4_<?php echo $reserv['reservation_id'] ?>" name="rating" value="4" required><label for="star4_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star3_<?php echo $reserv['reservation_id'] ?>" name="rating" value="3" required><label for="star3_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star2_<?php echo $reserv['reservation_id'] ?>" name="rating" value="2" required><label for="star2_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star1_<?php echo $reserv['reservation_id'] ?>" name="rating" value="1" required><label for="star1_<?php echo $reserv['reservation_id'] ?>"><i class="fas fa-star"></i></label>
                                        </div>
                                        <div class="soumettre_avis">
                                            <input type="hidden" name="logement_id" value=<?php echo $reserv['logement_id'] ?>>
                                            <input type="hidden" name="client_id" value=<?php echo $reserv['devis_id_compte_client'] ?>>
                                            <input type="hidden" name="proprio_id" value=<?php echo $reserv['logement_compte_id_proprio'] ?>>
                                            <input type="hidden" class="ratingValueInput" name="ratingValue">
                                            <textarea class="commentaire" type="text" name="commentaire" id="commentaire" required minlength="10" maxlength="500"></textarea>
                                            <input id="button_sub" class="button_form" type="submit" value="Soummettre">
                                        </div>
                                    </form>
                                </div>
                                <?php } ?>
                                </div>
                            </div>                    
                        </div>
            <?php } 
           // } ?>
            </section>
        <?php } else {
            ?>
            <h1>Aucune réservation pour le moment</h1>
        <?php }

    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
    ?>

<script>

    function handleButtonClick(reservationId, buttonId) {
        toggleCode(reservationId, buttonId);
    }

    /* script pour cacher le bouton et afficher laissez avis*/
    function toggleCode(code, button) {
        var avis = document.getElementById(code);
        var myButton = document.getElementById(button.id);

        if (avis.classList.contains("hidden")) {
            avis.classList.remove("hidden");
            myButton.textContent = "Cacher avis";
        }else if(myButton.textContent == "Cacher avis"){
            avis.classList.add("hidden");
            myButton.textContent = "Laissez un avis";
        }
    }


    /* script pour la barre de notation */
    const ratings = document.getElementsByName('rating');
    const ratingValueInput = document.getElementById('ratingValueInput');

    ratings.forEach(rating => {
        rating.addEventListener('change', (e) => {
            const selectedRating = e.target.value;
            ratingValueInput.value = selectedRating;
        });
    });
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