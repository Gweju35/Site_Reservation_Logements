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

    $devis = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../assets//pages_css/Devis/devis_du_proprio.css">
    <script src="../assets/index.js"></script>
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
        <a href="../Reservations/reservations_proprio.php">
            <div class="cercle_retour"><img src="../assets/ressources/icons/left-arrow.svg" alt="icone back" /></div>
        </a>
        <div class="titre_page">
            <h2>Demande de devis</h2>
        </div>
    </div>

    <div class="contenu">

        <?php if (count($devis) > 0) { 
            /* Déclaratin des variables sur la TVA */
            $tva = 20;
            $tva_calcul = 1.2;

            /* Dédlaration de la variable qui permet un affichage différent */ 
            $nb_devis_affiches = 0;

            foreach ($devis as $dev) { 

                /* récuprération du timestamp date expériation */
                try {
                    // Récupérez la date stockée dans l'attribut de la table
                    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                    $sql = "SELECT devis_date_expiration_v FROM alhaiz_breizh._devis WHERE devis_id = :id_devis";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id_devis', $dev['devis_id']);
                    $stmt->execute();
                
                    $date_verif = $stmt->fetch(PDO::FETCH_ASSOC);
                               
                    $timestamp = time(); // Obtenez le timestamp actuel
                
                    if ($timestamp >= $date_verif['devis_date_expiration_v'] && $dev['devis_statut'] == 'APP') {
                        // Si les dates sont égales ou si le timestamp actuel est supérieur, mettez à jour l'attribut dans la table
                        $sqlUpdate = "UPDATE alhaiz_breizh._devis SET devis_statut = 'EXP' WHERE _devis.devis_id = :id_devis";
                        $stmtUpdate = $pdo->prepare($sqlUpdate);
                        $stmtUpdate->bindParam(':id_devis', $dev['devis_id']);
                        $stmtUpdate->execute();
                        ?>
                    <?php }
                
                } catch (PDOException $e) {
                    echo "Erreur de base de données : " . $e->getMessage();
                }

                /* Initialisation des varaibles pour calculer le prix d'un devis TTC*/
                $nuit_pers_ttc = $dev['devis_prix_ht'] * $tva_calcul;
                $nuit_pers_sup_ttc = 0;
                $animal_ttc = 0;
                $linge_ttc = 0;
                $menage_ttc = 0;
                $transport_ttc = 0;

                if($dev['devis_statut'] == 'APP' || $dev['devis_statut'] == 'En attente'){ 
                    /* varible qui permet de changer l'affichage */
                    $nb_devis_affiches++;
                    ?>
                
                <section class="sectionForm">
                    <div class="photo_text">
                        <form action="../Profil/afficher_client.php" method="post">
                            <input type="hidden" name="id_compte" value="<?= $dev['devis_id_compte_client'] ?>">
                            <input class="photo_de_profil" class="visite_profil" type="image" src="<?php echo $dev['compte_photo_profil']?>" alt="Photo du client">
                        </form> 
                        <p>Le client est <?= $dev['client_nom'] . ' ' . $dev['client_prenom'] ?><br></p>
                    </div>
                    <br>
                    <p>
                    <?php 
                    if ($dev['devis_statut'] === 'APP') { 
                                // Calculer le temps restant en jours, heures, minutes et secondes
                                $restant = $dev['devis_date_expiration_v'] - time();?>

                                Vous avez accepté le devis pour le logement "<?= $dev['logement_accroche'] ?>"<br>le client lui reste <span id="counter"> </span> pour payer <br>
                            
                            <!-- Le script permet de faire un affichage dynamique du temps restant pour l'acceptation d'un devis -->
                            <script>
                                var count = <?= $restant ?>;
                                const counterElement = document.getElementById('counter');
                                var isCounting = true; // Nouvelle variable pour suivre l'état du script

                                // Utilisation d'une boucle while
                                var counterInterval = setInterval(function () {
                                    updateCounter();
                                }, 1000); // Mise à jour toutes les 1 seconde

                                function updateCounter() {
                                    if (isCounting) {
                                        var jours = Math.floor(count / (24 * 60 * 60)); // Jours complets
                                        var heures = Math.floor((count % (24 * 60 * 60)) / (60 * 60)); // Heures restantes
                                        var minutes = Math.floor((count % (60 * 60)) / 60); // Minutes restantes
                                        var secondes = count % 60; // Secondes restantes

                                        counterElement.textContent = jours + ' jours, ' + heures + ' heures, ' + minutes + ' minutes, ' + secondes + ' secondes';
                                        counterElement.style.fontFamily = 'Montserrat-Semibold, sans-serif';

                                        if (count <= 0) {
                                            clearInterval(counterInterval);
                                            isCounting = false; // Arrêter le comptage
                                            location.reload();
                                            exit();
                                        } else {
                                            count--;
                                        }
                                    }
                                }
                            </script>
                            
                                <?php }?>

                            <?php if ($dev['devis_statut'] === 'En attente') { 
                                /* mise en place de la date en français */
                                $dateAtt = $dev['devis_date'];
                                $listAtt = explode("-",$dateAtt);
                                $DateATT= $listAtt[2] . "/" . $listAtt[1] . "/" . $listAtt[0];
                            ?>
                                Il souhaite réserver le logement "<?= $dev['logement_accroche'] ?>" est en attente depuis le <?= $DateATT ?> <br>
                            <?php }?>
                        <br>
                        <?php
                            $dateDeb = $dev['devis_date_debut'];
                            $listDeb = explode("-",$dateDeb);
                            $DateDebutVacances = $listDeb[2] . "/" . $listDeb[1] . "/" . $listDeb[0];

                            $dateFin = $dev['devis_date_fin'];
                            $listFin = explode("-",$dateFin);
                            $DateFinVacances = $listFin[2] . "/" . $listFin[1] . "/" . $listFin[0];
                        ?>
                        Date d'entrée : <?php echo $DateDebutVacances?> <br>
                        Date de sortie : <?= $DateFinVacances?> <br>
                        Nombre de nuits : <?= $dev['devis_nb_nuit'] ?> <br>

                    </p>
                
                <br>
                
                <div class="position_table">
                    <table class="table_facture">
                        <thead class="case_table_color">
                            <tr>
                                <th>Désignation</th>
                                <th>Quantité</th>
                                <th>Prix HT</th>
                                <th>TVA</th>
                                <th>Montant TTC</th>
                            </tr>
                        </thead>

                            <tbody>
                            <tr>
                                <td>Nuitées pour <?= $dev['devis_nbr_personne'] ?> personnes</td>
                                <td><?= $dev['devis_nb_nuit'] ?></td>
                                <td><?= $dev['devis_prix_ht']?></td>
                                <td><?php echo "$tva%"?></td>
                                <td><?php echo round($nuit_pers_ttc,2);?></td>
                            </tr>

                            <!-- regarde si le nombre de personne sup est supérieur a 0 si oui on affiche la ligne -->
                            <?php if($dev['devis_nbr_personne_sup'] > 0) {
                                $nuit_pers_sup_ttc = $dev['charges_personne_sup'] * $dev['devis_nbr_personne_sup'] * $tva_calcul;
                                ?>
                                <tr>
                                    <td>Nuitées pour <?echo $dev['devis_nbr_personne_sup'];?> personne supplémentaire</td>
                                    <td><?= $dev['devis_nb_nuit'] ?></td>
                                    <td><?php echo $dev['charges_personne_sup'] * $dev['devis_nb_nuit'] ?></td>
                                    <td><?php echo "$tva%"?></td>
                                    <td><?php echo $nuit_pers_sup_ttc;?></td>
                                </tr>
                            <?php } ?>

                            <!-- regarde si le service animaux domestiques a été séléctionner si oui on affiche la ligne -->
                            <?php if($dev['devis_nbr_animaux'] >= 1) { 
                                $animal_ttc = $dev['charges_animaux'] *$tva_calcul;
                                ?>
                                <tr>
                                    <td>Animal</td>
                                    <td><?echo $dev['devis_nbr_animaux'];?></td>
                                    <td><?= $dev['charges_animaux'] * $dev['devis_nbr_animaux'] ?></td>
                                    <td><?php echo "$tva%"?></td>
                                    <td><?php echo $animal_ttc;?></td>
                                </tr>
                            <?php } ?>
                            
                            <!-- regarde si le service linge a été sélectionner si oui on affiche la ligne -->
                            <?php if($dev['devis_service_linge'] == 1) { 
                                $linge_ttc = $dev['charges_linge'] * $tva_calcul;
                                ?>
                                <tr>
                                    <td>Linge</td>
                                    <td>1</td>
                                    <td><?= $dev['charges_linge']?></td>
                                    <td><?php echo "$tva%"?></td>
                                    <td><?php echo $linge_ttc;?></td>
                                </tr>
                            <?php } ?>

                            <!-- on regarde si le service ménage a été selectionner si oui on affiche la ligne -->
                            <?php if($dev['devis_service_menage'] == 1) {
                                $menage_ttc = $dev['charges_menage'] * $tva_calcul;
                                ?>
                                <tr>
                                    <td>Ménage</td>
                                    <td>1</td>
                                    <td><?= $dev['charges_menage']?></td>
                                    <td><?php echo "$tva%"?></td>
                                    <td><?php echo $menage_ttc;?></td>
                                </tr>
                            <?php } ?>

                            <!-- on regarde si le service transport a été sélectionner si oui on affiche la ligne -->
                            <?php if($dev['devis_service_transport'] == 1 ) { 
                                $transport_ttc = $dev['charges_transport'] * $tva_calcul;
                                ?>
                                <tr>
                                    <td>Transport</td>
                                    <td>1</td>
                                    <td><?=$dev['charges_transport']?></td>
                                    <td><?php echo "$tva%"?></td>
                                    <td><?php echo $transport_ttc;?></td>
                                </tr>
                            <?php } ?>

                            <tr>
                                <td>Taxe de séjour</td>
                                <td><?php echo $dev['devis_nbr_personne']*$dev['devis_nb_nuit'] ?></td>
                                <td>-</td>
                                <td>-</td>
                                <td><?php echo round($dev['devis_taxe_sejour_ttc'],2); ?></td>
                            </tr>

                            <!-- calcule du devis total -->
                            <tr>
                                <td colspan="3"></td>
                                <td class="case_table_color">Total TTC</td>
                                <td class="case_table_color"><?php echo round($dev['devis_prix_ttc'],2)." €"; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                    <?php if ($dev['devis_statut'] == 'En attente') { ?>

                        <form action="accepter_devis.php" method="post" onsubmit="ajouter();">
                            <div class="popup" id="popup1">
                                <div class="overlay"></div>
                                <div class="content">
                                    <h1><b>Demande de devis acceptée</b></h1>
                                    <div class="bouton_confirmer">
                                        <input type="hidden" name="devis_id" value="<?= $dev['devis_id'] ?>">
                                        <input type="submit" value="Accepter" class="button_form" id="Accepter">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form action="refuser_devis.php" method="post" onsubmit="annuler();">
                            <div class="popup" id="popup2">
                                <div class="overlay"></div>
                                <div class="content">
                                    <h1><b>Demande de devis refusée</b></h1>
                                    <div class="bouton_confirmer">
                                        <input type="hidden" name="devis_id" value="<?= $dev['devis_id'] ?>">
                                        <input type="submit" value="Refuser" class="button_form" id="Refuser">
                                    </div>
                                </div>
                            </div>
                        </form>



                        <div class="right">
                                <input onclick="ajouter()" class="button_form" type="submit" value="Accepter">
                                <input onclick="annuler()" class="button_form" type="submit" value="Refuser">
                        </div>
                    
                    <?php } ?>
                    </section>
                <script>
                    function ajouter() {
                        document.body.scrollTop = 0;
                        document.documentElement.scrollTop = 0;
                        document.getElementById("popup1").classList.add("active");
                    }

                    function annuler() {
                        document.body.scrollTop = 0;
                        document.documentElement.scrollTop = 0;
                        document.getElementById("popup2").classList.add("active");
                    }
                </script>

                    <?= $nb_devis_affiches ?>
                <?php }
            
                if($dev['devis_statut'] == 'EXP' || $dev['devis_statut'] == 'SUPR_C'){
                    $nb_devis_affiches++; ?>
                    <section class="sectionForm contenu-indisponible">   
                        <div class="photo_text">
                                <img class="photo_de_profil" src="<?php echo $dev['compte_photo_profil']; ?>" alt="Photo du client">
                                <p>Le propriétaire est <?= $dev['client_nom'] . ' ' . $dev['client_prenom'] ?><br></p>
                            </div>

                            <div class="message_important">
                                <h1>EXPIRÉ</h1><br>
                            </div>
                            
                            <!-- choix d'affichage selon le statut du devis -->
                            <?php
                                $dateDeb = $dev['devis_date_debut'];
                                $listDeb = explode("-",$dateDeb);
                                $DateDebutVacances = $listDeb[2] . "/" . $listDeb[1] . "/" . $listDeb[0];

                                $dateFin = $dev['devis_date_fin'];
                                $listFin = explode("-",$dateFin);
                                $DateFinVacances = $listFin[2] . "/" . $listFin[1] . "/" . $listFin[0];
                            ?>
                            <p>
                                Le devis pour le logement "<?= $dev['logement_accroche'] ?>" pour non respect du délai du paiement du propriétaire<br>
                                <br>
                                Arrivée prévue le <?php echo $DateDebutVacances ?> <br>
                                Départ prévu le <?= $DateFinVacances ?> <br>
                                Nombre de nuits : <?= $dev['devis_nb_nuit'] ?> <br>
                            </p>

                            <div class="right">
                                <form action="../Devis/traitement_devis_exp.php" method="post">
                                    <input type="hidden" name="devis_id" value="<?= $dev['devis_id'] ?>">
                                    <input class="button_form" type="submit" value="OK">
                                </form>
                            </div>
                </section>

            <?php }
            }
            if($nb_devis_affiches == 0){ ?>
                <h1>Aucune demande de devis</h1>
            <?php }
            } else { ?>
                <h1>Aucune demande de devis</h1>
            <?php }
            ?>
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