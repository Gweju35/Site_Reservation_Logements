<?php
session_start();

// Connexion à la base de données
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour récupérer les devis acceptés par le propriétaire
    $devis_id = $_POST['devis_id'];

    $sql = "SELECT d.*, c.compte_nom AS client_nom, c.compte_prenom AS client_prenom, l.* , c.compte_adresse as client_adresse, co.compte_nom as proprio_nom, co.compte_prenom as proprio_prenom, co.compte_adresse as proprio_adresse, r.reservation_facture as id_facture
    FROM alhaiz_breizh._devis d
    INNER JOIN alhaiz_breizh._logement l ON d.devis_id_logement = l.logement_id
    INNER JOIN alhaiz_breizh._proprietaire p ON l.logement_compte_id_proprio = p.compte_id
    INNER JOIN alhaiz_breizh._client c ON d.devis_id_compte_client = c.compte_id
    INNER JOIN alhaiz_breizh._compte co ON l.logement_compte_id_proprio = co.compte_id
    INNER JOIN alhaiz_breizh._reservation r ON d.devis_id = r.reservation_devis_id
    WHERE d.devis_id = :devis_id";



    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':devis_id', $devis_id);
    $stmt->execute();

    $facture = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Alhaiz Breizh</title>
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script defer src="../assets/index.js"></script>
    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/Facture/facture.css">
</head>

<body>

    <!------------------HEADER------------------>

    <?php
    if ($_SESSION['user_type'] == 'client') {
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
                <h2>Facture :</h2>
            </div>
        </div>

        <?php /* permet de récuprer que le numéro de la facture */ $facture_id = explode("-", $facture['id_facture']); ?>

        <div class="center">
            <section class="contenu sectionForm">
                <div>
                    <h2>Facture <?= $facture_id[1] ?></h2><br><br>

                    <div class="aligner">
                        <p>
                            <?= $facture['proprio_nom'] . " " . $facture['proprio_prenom'] ?><br>
                            <?php
                            /* permet de récuperer chaque partie d'une adresse (n° et rue / ville / code postal */
                            $adresse = explode(",", $facture['proprio_adresse']);
                            echo $adresse[0]; ?>
                            <br>
                            <?= $adresse[1] . ", " . $adresse[2]; ?>
                        </p>
                        <p class="right_info_client">
                            Adressée à : <br><br>
                            <?= $facture['client_nom'] . " " . $facture['client_prenom'] ?><br>
                            <?php
                            /* permet de récuperer chaque partie d'une adresse (n° et rue / ville / code postal */
                            $adresse = explode(",", $facture['client_adresse']);
                            echo $adresse[0]; ?>
                            <br>
                            <?= $adresse[1] . ", " . $adresse[2]; ?>
                        </p>
                    </div>
                    <br>
                    <p>Coût du séjour pour le logement "<?= $facture['logement_accroche'] ?>" </p><br>
                </div>
                <div>
                    <?php
                    $dateDeb = $facture['devis_date_debut'];
                    $listDeb = explode("-", $dateDeb);
                    $DateDebutVacances = $listDeb[2] . "/" . $listDeb[1] . "/" . $listDeb[0];

                    $dateFin = $facture['devis_date_fin'];
                    $listFin = explode("-", $dateFin);
                    $DateFinVacances = $listFin[2] . "/" . $listFin[1] . "/" . $listFin[0];
                    ?>
                    <p>
                        Séjour du <?= $DateDebutVacances ?> au <?= $DateFinVacances ?> <br>

                        <!-- condition qui permet de changer l'affichage selon le nombre de personne -->
                        <?php if ($facture['devis_nbr_personne'] == 1) {
                            echo $facture['devis_nbr_personne'] . " personne";
                        } else {
                            echo $facture['devis_nbr_personne'] . " personnes";
                        }

                        /* condition qui permet de gérer l'affichage selon le nombre de personnes supplémentaire */
                        if ($facture['devis_nbr_personne_sup'] == 1) {
                            echo " - " . $facture['devis_nbr_personne'] . " personne supplémentaire";
                        }
                        if ($facture['devis_nbr_personne_sup'] > 1) {
                            echo " - " . $facture['devis_nbr_personne_sup'] . " personnes supplémentaires";
                        }

                        /* condition qui permet de gérer l'affichage selon le nombre d'animaux */
                        if ($facture['devis_nbr_animaux'] == 1) {
                            echo " - " . $facture['devis_nbr_animaux'] . " animal";
                        }
                        if ($facture['devis_nbr_animaux'] > 1) {
                            echo " - " . $facture['devis_nbr_animaux'] . " animaux";
                        }
                        ?>

                        <br><br>
                        Adresse de la location : <br>
                        <?= $facture['logement_adresse'] ?><br>
                        <?= $facture['logement_code_postal'] . ", " . $facture['logement_ville'] ?>
                    </p>
                </div>

                <?php
                /* Déclaratin des variables sur la TVA */
                $tva = 20;
                $tva_calcul = 1.2;
                /* Initialisation des varaibles pour calculer le prix d'un devis TTC*/
                $nuit_pers_ttc =  $facture['devis_prix_ht'] * $tva_calcul;
                $nuit_pers_sup_ttc = 0;
                $animal_ttc = 0;
                $linge_ttc = 0;
                $menage_ttc = 0;
                $transport_ttc = 0;
                ?>
                <br>
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
                                <td>Nuitées pour <?= $facture['devis_nbr_personne'] ?> personnes</td>
                                <td><?= $facture['devis_nb_nuit'] ?></td>
                                <td><?= $facture['devis_prix_ht']?></td>
                                <td><?php echo "$tva%"?></td>
                                <td><?php echo round($nuit_pers_ttc,2);?></td>
                            </tr>

                        <!-- regarde si le nombre de personne sup est supérieur a 0 si oui on affiche la ligne -->
                        <?php if ($facture['devis_nbr_personne_sup'] > 0) {
                            $nuit_pers_sup_ttc = $facture['charges_personne_sup'] * $facture['devis_nb_nuit'] * $tva_calcul;
                        ?>
                            <tr>
                                <td>Nuitées pour <? echo $facture['devis_nbr_personne_sup']; ?> personne supplémentaire</td>
                                <td><?= $facture['devis_nb_nuit'] ?></td>
                                <td><?php echo $facture['charges_personne_sup'] * $facture['devis_nbr_personne_sup'] ?></td>
                                <td><?php echo "$tva%" ?></td>
                                <td><?php echo $nuit_pers_sup_ttc; ?></td>
                            </tr>
                        <?php } ?>

                        <!-- regarde si le service animaux domestiques a été séléctionner si oui on affiche la ligne -->
                        <?php if ($facture['service_animaux_domestique'] >= 1) {
                            $animal_ttc = $facture['charges_animaux'] * $tva_calcul;
                        ?>
                            <tr>
                                <td>Animal</td>
                                <td><? echo $facture['devis_nbr_animaux']; ?></td>
                                <td><?= $facture['charges_animaux'] * $facture['devis_nbr_animaux'] ?></td>
                                <td><?php echo "$tva%" ?></td>
                                <td><?php echo $animal_ttc; ?></td>
                            </tr>
                        <?php } ?>

                        <!-- regarde si le service linge a été sélectionner si oui on affiche la ligne -->
                        <?php if ($facture['devis_service_linge'] == 1) {
                            $linge_ttc = $facture['charges_linge'] * $tva_calcul;
                        ?>
                            <tr>
                                <td>Linge</td>
                                <td>1</td>
                                <td><?= $facture['charges_linge'] ?></td>
                                <td><?php echo "$tva%" ?></td>
                                <td><?php echo $linge_ttc; ?></td>
                            </tr>
                        <?php } ?>

                        <!-- on regarde si le service ménage a été selectionner si oui on affiche la ligne -->
                        <?php if ($facture['devis_service_menage'] == 1) {
                            $menage_ttc = $facture['charges_menage'] * $tva_calcul;
                        ?>
                            <tr>
                                <td>Ménage</td>
                                <td>1</td>
                                <td><?= $facture['charges_menage'] ?></td>
                                <td><?php echo "$tva%" ?></td>
                                <td><?php echo $menage_ttc; ?></td>
                            </tr>
                        <?php } ?>

                        <!-- on regarde si le service transport a été sélectionner si oui on affiche la ligne -->
                        <?php if ($facture['devis_service_transport'] == 1) {
                            $transport_ttc = $facture['charges_transport'] * $tva_calcul;
                        ?>
                            <tr>
                                <td>Transport</td>
                                <td>1</td>
                                <td><?= $facture['charges_transport'] ?></td>
                                <td><?php echo "$tva%" ?></td>
                                <td><?php echo $transport_ttc; ?></td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td>Taxe de séjour</td>
                            <td><?php echo $facture['devis_nbr_personne']*$facture['devis_nb_nuit'] ?></td>
                            <td>-</td>
                            <td>-</td>
                            <td><?php echo round($facture['devis_taxe_sejour_ttc'],2) ?></td>
                        </tr>

                        <!-- calcule du devis total -->
                        <tr>
                            <td colspan="3"></td>
                            <td class="case_table_color">Total TTC</td>
                            <td class="case_table_color"><?php echo round($facture['devis_prix_ttc'],2)." €"; ?></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <p>Règlement réalisé par Carte Bleue</p>
            </section>
        </div>
    </main>

    <footer>
        <div class="footer">&copy ALHaIZ Breizh</div>
    </footer>
</body>

</html>