<?php
session_start();
// Connexion à la base de données
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les logements du propriétaire
    $sql = "SELECT _logement.*, _proprietaire.compte_nom, _proprietaire.compte_prenom, _proprietaire.compte_photo_profil FROM alhaiz_breizh._logement JOIN alhaiz_breizh._proprietaire ON _logement.logement_compte_id_proprio = _proprietaire.compte_id WHERE _logement.logement_id = :logement_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':logement_id',$_GET['logement_id']);
    $stmt->execute();

    $logements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les photos complémentaires d'un logement
    $sql = "SELECT photo_1, photo_2, photo_3, photo_4, photo_5, photo_6 FROM alhaiz_breizh._photo WHERE logement_id = :logement_id ";     
    $stmt = $pdo->prepare($sql);     
    $stmt->bindParam(':logement_id', $_GET['logement_id']);     
    $stmt->execute();
    
    $photo_comp = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}

// Fonction pour afficher la barre d'étoiles
function afficherBarreEtoiles($note) {
    // Calcul du nombre d'étoiles pleines
    $nbEtoilesRemplies = floor($note);

    // Calcul du nombre d'étoiles vides
    $nbEtoilesVides = 5 - $nbEtoilesRemplies;

    // Affichage des étoiles pleines
    for ($i = 0; $i < $nbEtoilesRemplies; $i++) {
        echo "<i class='fas fa-star'></i>";
    }

    // Affichage de la demi-étoile si nécessaire
    if ($note - $nbEtoilesRemplies >= 0.5) {
        echo "<i class='fas fa-star-half-alt'></i>";
        $nbEtoilesVides--; // Réduire le nombre d'étoiles vides
    }

    // Affichage des étoiles vides
    for ($i = 0; $i < $nbEtoilesVides; $i++) {
        echo "<i class='far fa-star'></i>";
    }
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
    <link rel="stylesheet" href="../assets/pages_css/Logement/afficher_logement.css">
    <!-- les étoiles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    
    
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
        <div class="titre_page"><h2>Logements</h2></div>
    </div>
    
    <?php
    if (count($logements) > 0) {?>
        <?php foreach ($logements as $logement) { 
                // Récuperer la  moyenne des notes du logement 
                $sql = "SELECT AVG(avis_note), COUNT(*) FROM alhaiz_breizh._avis WHERE avis_id_logement = :logement_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':logement_id', $logement['logement_id']);
                $stmt->execute();

                $avis = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class=button_modif>
            <?php
            if ($_SESSION['user_type']== 'proprietaire'){
                if ($logement['logement_compte_id_proprio'] == $_SESSION['user_id']){

                    // Ajoutez un bouton "Modifier" pour ce logement
                    echo '<form action="../Logements/logement_modif.php" method="post">';
                    echo '<input type="hidden" name="logement_id" value="' . $logement['logement_id'] . '">';
                    echo '<input class="button_form" type="submit" value="Modifier">';
                    echo '</form>';
                    
                }

            }?>
            </div>

            <div id="group">
                <section id="left_part">
                    <article id="infos_logement">
                        <div>
                            <!--Accroche-->
                            <h2><?php echo $logement['logement_accroche']?><span id="separation_point"> - </span><?php echo 'Proposé par ' . $logement['compte_prenom']?></h2>
                            
                            <!--Lieu-->
                            <div id="log_avis">
                                <div id = "lieu_logement">
                                    <p><?php echo $logement['logement_ville'] . ', ' . $logement['logement_code_postal'] ?></p>
                                </div>
                                <?php
                                    if($avis['count'] > 0) { ?>
                                        <div id="text_avis">
                                            <span class="avis">
                                            <p class="note"><?php echo number_format($avis['avg'], 1) . " / 5 "; ?><i class='fas fa-star'></i> (<?php echo $avis['count'] ; ?> avis)</p>
                                            </span>
                                        </div>
                                <?php } ?>
                            </div>

                            <!--Voir profil-->
                            <form action="../Profil/afficher_proprio.php" method="post">
                                <input type="hidden" name="id_compte" value="<?= $logement['logement_compte_id_proprio'] ?>">
                                <input class="visite_profil" type="submit" value="<?php echo 'Voir le profil de ' . $logement['compte_prenom']?>">
                            </form>
                        </div>

                        <div id="description">
                            <!--Description-->
                            <span>
                                <form action="../Profil/afficher_proprio.php" method="post">
                                    <input type="hidden" name="id_compte" value="<?= $logement['logement_compte_id_proprio'] ?>">
                                    <input id=photo_profil type="image" src="<?php echo $logement['compte_photo_profil']?>">
                                </form>    
                            </span>
                            <span class="triangle"></span>
                            <!--Description -->
                            <div>
                                <h3>Description :</h3>
                                <p id="txt_description"><?php echo  $logement['logement_description'] ?></p>
                            </div>        
                        </div>
                        <hr class="trait_horizontal">

                    </article>

                        <section class="info_logement">
                        <article>
                            <h3 id="titre_info_comp">Logement :</h3>
                            <div class="infos_complementaires_container">
                            <div class="infos_complementaires">
                                
                                <?php if($logement['logement_personne_max'] > 0){ ?>
                                    <div>
                                        <span class="icon people"></span>
                                        <p><?php echo "⸱ " . $logement['logement_personne_max'] . " personnes" ?></p>
                                    </div>
                                <?php } ?>

                                <?php if($logement['logement_nb_chambre'] > 0){ ?>
                                    <div>
                                        <span class = "icon bedroom"></span>
                                        <p><?php echo "⸱ " . $logement['logement_nb_chambre'] . " chambres" ?></p>
                                    </div>
                                <?php } ?>

                                <?php if($logement['logement_nb_lit_double'] > 0){ ?>
                                    <div>
                                        <span class="icon double_bed"></span>
                                        <p><?php echo "⸱ " . $logement['logement_nb_lit_double'] . " lits doubles"?></p>
                                    </div>
                                <?php } ?>

                                <?php if($logement['logement_nb_lit'] > 0){ ?>
                                    <div>
                                        <span class="icon single_bed"></span>
                                        <p><?php echo "⸱ " . $logement['logement_nb_lit'] . " lits simples" ?></p>
                                    </div>
                                <?php } ?>

                                <?php if($logement['logement_surface'] > 0){ ?>
                                    <div>
                                        <span class="icon ruler"></span>
                                        <p><?php echo "⸱ " . $logement['logement_surface'] . " m²"?></p>
                                    </div>
                                <?php } ?>

                                <?php if($logement['logement_nb_salle_de_bain'] > 0){ ?>
                                    <div>
                                        <span class="icon shower"></span>
                                        <p><?php echo "⸱ " . $logement['logement_nb_salle_de_bain'] . " salle de bain" ?></p>
                                    </div>
                                <?php } ?>

                                <?php if($logement['service_animaux_domestique'] == true){ ?>
                                    <div>
                                        <span class = "icon dog"></span>
                                        <p><?php echo "⸱ animaux autorisés" ?></p>
                                    </div>
                                <?php } ?>
                            </div>
                            </div>
                            <div class="dispo_list_container">
                            <div class="dispo_list">
                                <div class="dispo_list_left">
                                    <div class="dispo_list_installations">
                                        <h3 class="titres_dispo_list">Installations</h3>

                                        <?php if($logement['installation_climatisation'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Climatisation</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Climatisation</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['installation_piscine'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Piscine</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Piscine</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?> 

                                        <?php if($logement['installation_jacuzzi'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Jacuzzi</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Jacuzzi</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>
                                        
                                        <?php if($logement['installation_hammam'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Hammam</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Hammam</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['installation_sauna'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Sauna</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Sauna</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                    </div>

                                    <div class="dispo_list_services">
                                        <h3 class="titres_dispo_list">Services</h3>

                                        <?php if($logement['service_linge'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Linge</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Linge</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['service_menage'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Ménage</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Ménage</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['service_transport'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Navette</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Navette</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>
                                        
                                    </div>
                                </div>

                                <div class="dispo_list_right">
                                    <div class="dispo_list_equipement">
                                        <h3 class="titres_dispo_list">Équipements</h3>

                                        <?php if($logement['equipement_tv'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Télevision</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Télevision</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['equipement_machine_a_laver'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Machine à laver</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Machine à laver</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['equipement_lave_vaisselle'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Lave-vaisselle</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Lave-vaisselle</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>
                                        
                                        <?php if($logement['equipement_wifi'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Wi-Fi</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Wi-Fi</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                    </div>

                                    <div class="dispo_list_amenagement">
                                        <h3 class="titres_dispo_list">Aménagements</h3>

                                        <?php if($logement['amenagement_jardin'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Jardin</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Jardin</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['amenagement_balcon'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Balcon</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Balcon</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['amenagement_parking_public'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Parking public</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Parking public</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['amenagement_parking_prive'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Parking privé</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Parking privé</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>

                                        <?php if($logement['amenagement_terrasse'] == true){ ?>
                                            <div  class = "checked">
                                                <p>Terrasse</p>
                                                <span class="icon checkmark"></span>
                                            </div>
                                        <?php } else { ?>
                                            <div class = "crossed">
                                                <p>Terrasse</p>
                                                <span class="icon crossmark"></span>
                                            </div>
                                        <?php } ?>
                                        
                                    </div>

                                </div>
                            </div>
                            </div>
                        </article>
                    </section>

                    <section>
                        
                    </section>
                </section>
                <!--
                <div class="image__selector" id="carousel_sejour">
                <img id="img_carousel" src="<?php echo $logement['logement_photo'] ?>" alt="Image de plongée" />
                    <div>

                    </div>
                </div>
                -->
                <section class="container_carousel_bloc_reserver">
                    <div class="container_carousel">
                        <div class="carousel">
                            <div class="slider">
                                <section><img src="<?php echo $logement['logement_photo'] ?>" alt=""></section>
                                <?php $cpt = 0; ?>
                                <?php if (is_array($photo_comp)) { ?>
                                <?php foreach ($photo_comp as $photo) { 
                                    if($photo != ''){ ?>
                                    
                                    <section><img src="<?php echo $photo ?>" alt="Photos complémentaires"></section>
                                    <?php $cpt+=1; ?>
                                <?php }}} ?>
                            </div>
                            <div class="controls">
                                <span class="icon arrow-left arrow left" tabindex="0" aria-label="Flèche gauche"></span>
                                <span class="icon arrow-right arrow right" tabindex="0" aria-label="Flèche droite"></span>

                                <ul>
                                <?php for ($i = 0; $i < $cpt + 1; $i++) { ?>
                                        <li <?php echo ($i === 0) ? ' class="selected"' : ''; ?>></li>
                                <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <article class="bloc_reserver_tkt">
                            <div class="bloc_reserver">
                                <h3><?php echo $logement['logement_prix_nuit_base'] . " € la nuit"?></h3>
                                <?php 
                                    if($_SESSION['user_type'] == 'client'){ ?>
                                                <form action="../Devis/demandeDeDevis.php" method="post">
                                                    <input class="button_form reserver_button" type="submit" value="Demande de devis">
                                                </form>
                                <?php } ?>
                                <?php 
                                    if($_SESSION['user_type'] == 'visiteur'){ ?>
                                                <form action="../Profil/creation_compte_client.php" method="post">
                                                    <input class="button_form reserver_button" type="submit" value="Demande de devis">
                                                </form>
                                <?php } ?>
                            </div>
                        </article>
                    </div>
                
                </section>
            </div>
            <hr>

            <br>
            <h3 id="titre_info_comp">Avis :</h3>
            <?php
                // Récupérer les informations pour les avis
                $sql = "SELECT avis_id, avis_contenue, avis_date_post, avis_signalement, avis_id_compte_client, avis_note  FROM alhaiz_breizh._avis WHERE avis_id_logement = :logement_id ";     
                $stmt = $pdo->prepare($sql);     
                $stmt->bindParam(':logement_id', $_GET['logement_id']);     
                $stmt->execute();
                
                $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);

                /* condition qui permet d'afficher la section avis si il en a */ 
                if($avis!=null){  ?>
                    <section class="liste_avis">
                        <?php foreach($avis as $avis_perso){  
                                // Récupérer les informations pour afficher les données du client
                                $sql = "SELECT compte_nom, compte_prenom, compte_photo_profil FROM alhaiz_breizh._client WHERE compte_id = :compte_id ";     
                                $stmt = $pdo->prepare($sql);     
                                $stmt->bindParam(':compte_id', $avis_perso['avis_id_compte_client']);     
                                $stmt->execute();

                                $client = $stmt->fetch(PDO::FETCH_ASSOC);

                                /* Script de mise en date Anglaise et date Française*/ 
                                $date = new DateTime($avis_perso["avis_date_post"]);
                                
                                // Obtenir le jour, le mois et l'année
                                $day = $date->format("d");
                                $month = $date->format("M");
                                $year = $date->format("Y");
                                
                                // Convertir le mois en anglais et le mettre en fr
                                $month_translation = [
                                    'jan' => 'Janvier',
                                    'feb' => 'Février',
                                    'mar' => 'Mars',
                                    'apr' => 'Avril',
                                    'may' => 'Mai',
                                    'jun' => 'Juin',
                                    'jul' => 'Juillet',
                                    'aug' => 'Août',
                                    'sep' => 'Septembre',
                                    'oct' => 'Octobre',
                                    'nov' => 'Novembre',
                                    'dec' => 'Décembre'
                                ];
                                $month_fr = $month_translation[strtolower($month)];
                                
                                // Formater la date dans le format requis
                                $date_fr = $day . " " . $month_fr . " " . $year;
                            ?>
                            <div class="avis">
                                <div class="profil">
                                    <img class="photo_de_profil" src="<?php echo $client["compte_photo_profil"]; ?>" alt="Photo de Profil">
                                    <p class="user"><?php echo $client["compte_nom"] . " " . $client["compte_prenom"] ?></p>
                                </div>
                                <div id="modif_avis">
                                    <span class="notation">
                                        <div id="etoile_et_p">
                                            <div class="etoile">
                                                <p class="barre-etoiles" id="<? echo 'star'.$avis_perso['avis_id_compte_client'] ?>"> <?php echo  afficherBarreEtoiles($avis_perso['avis_note']);  ?></p>
                                            </div>
                                            <p>• Avis posté le <?php echo $date_fr ; ?></p>
                                        </div>
                                        
                                    </span>
                                
                                    <p class="description" id="<? echo "commentaire".$avis_perso['avis_id_compte_client'] ?>"> <?php echo  $avis_perso["avis_contenue"] ;?></p>
                                    
                                </div>
                                <?php
                                    if ($_SESSION['user_type'] == 'client' && $avis_perso['avis_id_compte_client'] == $_SESSION['user_id']){?>
                                        <div id="lesBoutons">
                                            <div id="boutton">
                                                <form action="./traitementDeleteAvis.php" method="post" id="delete">
                                                    <input type="hidden" name="avis_id" value="<?= $avis_perso['avis_id'] ?>">
                                                    <input type="submit" value="Supprimer" id="<? echo "button_delete".$avis_perso['avis_id_compte_client'] ?>" class="button_form">
                                                </form>
                                                <button class="button_form" id="<? echo "button".$avis_perso['avis_id_compte_client'] ?>" onclick="avis(<? echo 'star'.$avis_perso['avis_id_compte_client'] ?>, <? echo 'commentaire'.$avis_perso['avis_id_compte_client'] ?>, <? echo 'button'.$avis_perso['avis_id_compte_client'] ?>, <? echo 'button_delete'.$avis_perso['avis_id_compte_client'] ?>, <? echo $avis_perso['avis_id'] ; ?>)">Modifier</button>
                                            </div>
                                        </div>
                                    <?php } ?>
                            </div>
                        <?php } ?>
                    </section>
                <?php
                }
                else{ ?>
                    <h1>Aucun avis sur le logement</h1>
                <?php
                }
                
        }
        echo '</ul>';
    } else {
        echo 'Aucun logement trouvé pour ce propriétaire.';
    }

    $_SESSION['logement_id'] = $logement['logement_id']; ?>
    
    <script>
        document.getElementById("delete").addEventListener("submit", function(event) {
            event.preventDefault();

            // Afficher l'alerte
            if (confirm("Êtes-vous sûr de vouloir supprimer votre avis ?")) {
                this.submit();
            } else {
                return false;
            }
        });

        /* script pour la barre de notation */
        const ratings = document.getElementsByName('rating');
        const ratingValueInput = document.getElementById('ratingValueInput');

        ratings.forEach(rating => {
            rating.addEventListener('change', (e) => {
                const selectedRating = e.target.value;
                ratingValueInput.value = selectedRating;
            });
        });

        function avis(star, commentaire, button_modif, button_delete, id_avis) {
            var texte = document.getElementById(commentaire.id);
            var boutonModifier = document.getElementById(button_modif.id);
            var boutonSupprimer =document.getElementById(button_delete.id);
            var avisDiv = document.getElementById(star.id);

            // Le textarea
            var champEdition = document.createElement('textarea');
            champEdition.value = texte.textContent;
            champEdition.classList.add("commentaire");
            texte.parentNode.replaceChild(champEdition, texte);


            // Création du formulaire d'évaluation avec la barre d'étoile
            var formulaireEtoiles = document.createElement('div');
            formulaireEtoiles.classList.add('rating');
            formulaireEtoiles.classList.add('no-color'); 
            formulaireEtoiles.innerHTML = `
                <input type="radio" id="star5" name="rating" value="5" required><label for="star5"><i class="fas fa-star"></i></label>
                <input type="radio" id="star4" name="rating" value="4" required><label for="star4"><i class="fas fa-star"></i></label>
                <input type="radio" id="star3" name="rating" value="3" required><label for="star3"><i class="fas fa-star"></i></label>
                <input type="radio" id="star2" name="rating" value="2" required><label for="star2"><i class="fas fa-star"></i></label>
                <input type="radio" id="star1" name="rating" value="1" required><label for="star1"><i class="fas fa-star"></i></label>
            `;

            // Remplacement de div
            avisDiv.parentNode.replaceChild(formulaireEtoiles, avisDiv);

            //Création d'un div pour les boutons
            var divModif_avis = document.getElementById('modif_avis');

            // Création du bouton annuler
            var boutonAnnuler = document.createElement('button');
            boutonAnnuler.classList.add('button_form');
            
            boutonAnnuler.textContent = 'Annuler';
            boutonAnnuler.addEventListener('click', function() {
                if (confirm("Êtes-vous sûr de vouloir annuler les modifications ?")) {
                    // Recharger la page pour annuler les modifications
                    window.location.reload();
                }
            });
            

            // Création du bouton soumettre
            var boutonSoumettre = document.createElement('button');
            boutonSoumettre.classList.add('button_form');
            

            boutonSoumettre.textContent = 'Soumettre';
            boutonSoumettre.addEventListener('click', function() {
                if (confirm("Êtes-vous sûr de vouloir soumettre les modifications ?")) {
                    var ratingValue = $('input[name="rating"]:checked').val();
                    var commentaireValue = champEdition.value;
                    var idValue = id_avis;

                    $.ajax({
                        url: '../Logements/traitementUpdateAvis.php',
                        type: 'POST',
                        data: {
                            rating: ratingValue,
                            commentaire: commentaireValue,
                            id:  idValue
                        },
                        success: function(response) {
                            alert("Les modifications ont été soumises avec succès !");
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert("Une erreur est survenue lors de la soumission des modifications.");
                            console.error(xhr.responseText);
                        }
                    });
                }
            });


            var divBouton = document.createElement('div');
            divBouton.setAttribute('id', 'boutton_v2');
            divBouton.appendChild(boutonAnnuler);
            divBouton.appendChild(boutonSoumettre);

            // Ajout des boutons à la page

            var lesBoutons = document.getElementById('lesBoutons');
            lesBoutons.appendChild(divBouton)
            
            

            // Masquage du bouton Modifier
            boutonModifier.style.display = 'none';

            // Masquage du bouton Supprimer
            boutonSupprimer.style.display = 'none';
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
    <script src="../assets/carousel.js"></script>
</body>
</html>