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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Alhaiz Breizh</title>
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/Logement/prev_logement.css">
    <script defer src="../assets/index.js"></script>
    
</head>

<body>
    
<!------------------HEADER------------------>
<?php
if (isset($_SESSION['user_type']) == false){
    echo $_SESSION['headerVisiteur'];
} else if ($_SESSION['user_type'] == 'client'){
    echo $_SESSION['headerClient'];
} else {
    echo $_SESSION['headerProprietaire'];
}
?>


<?php
    function getCoordinates($address) {
        // Encodez l'adresse pour qu'elle soit utilisable dans une URL
        $encoded_address = urlencode($address);
        
        // Clé API Google Maps (remplacez 'YOUR_API_KEY' par votre propre clé)
        $api_key = 'AIzaSyDmOaihDngGhTuTKe9623bFFTNzrdLAzus';
        
        // Construisez l'URL pour la requête à l'API de géocodage inversé
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$encoded_address&key=$api_key";
        
        // Effectuez la requête et obtenez la réponse JSON
        $response = file_get_contents($url);
        
        // Analysez la réponse JSON
        $data = json_decode($response);
        
        // Vérifiez si la requête a réussi
        if ($data->status == 'OK') {
            // Récupérez les coordonnées géographiques du premier résultat
            $location = $data->results[0]->geometry->location;
            $latitude = $location->lat;
            $longitude = $location->lng;
            return array('latitude' => $latitude, 'longitude' => $longitude);
        } else {
            // En cas d'erreur, retournez null ou une autre valeur appropriée
            return null;
        }
    }


    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Récupérez les données du formulaire
        $_SESSION['logement_compte_id_proprio'] = $_POST['logement_compte_id_proprio'];
        $_SESSION['logement_accroche'] = $_POST['logement_accroche'];
        $_SESSION['logement_description'] = $_POST['logement_description'];
        $_SESSION['logement_nature'] = $_POST['logement_nature'];
        $_SESSION['logement_type'] = $_POST['logement_type'];
        $_SESSION['logement_adresse'] = $_POST['logement_adresse'];
        //$_SESSION['logement_coordonnees_gps'] = $_POST['logement_coordonnees_gps'];
        $_SESSION['logement_prix_nuit_base'] = $_POST['logement_prix_nuit_base'];
        $_SESSION['logement_surface'] = $_POST['logement_surface'];
        $_SESSION['logement_nb_chambre'] = $_POST['logement_nb_chambre'];
        $_SESSION['logement_nb_lit'] = $_POST['logement_nb_lit'];
        $_SESSION['logement_nb_lit_double'] = $_POST['logement_nb_lit_double'];
        $_SESSION['logement_nb_salle_de_bain'] = $_POST['logement_nb_salle_de_bain'];
        $_SESSION['logement_nb_pers_sup'] = $_POST['logement_nb_pers_sup'];
        $_SESSION['logement_ville'] = $_POST['logement_ville'];
        $_SESSION['logement_code_postal'] = $_POST['logement_code_postal'];
        // Fusion pour obtenir l'adresse complète du logement 
        $address = $_SESSION['logement_adresse'].', '.$_SESSION['logement_code_postal'].', '.$_SESSION['logement_ville'];
        // On récupère les coordonnées GPS grâce à la fonction getCoordinates
        $coordinates = getCoordinates($address);
        
        $_SESSION['logement_coordonnees_gps'] =  $coordinates['latitude'].','.$coordinates['longitude'];
        //echo $_SESSION['logement_coordonnees_gps'];
        if (isset($_POST['choix_linge'])) {
            $_SESSION['charges_add_linge'] = $_POST['charges_add_linge'];
        } else {
            $_SESSION['charges_add_linge'] = 0;
        }

        if (isset($_POST['choix_menage'])) {
            $_SESSION['charges_add_menage'] = $_POST['charges_add_menage'];
        } else {
            $_SESSION['charges_add_menage'] = 0;
        }

        if (isset($_POST['choix_transport'])) {
            $_SESSION['charges_add_transport'] = $_POST['charges_add_transport'];
        } else {
            $_SESSION['charges_add_transport'] = 0;
        }

        if (isset($_POST['choix_animaux'])) {
            $_SESSION['charges_add_prix_sup_animaux'] = $_POST['charges_add_prix_sup_animaux'];
        } else {
            $_SESSION['charges_add_prix_sup_animaux'] = 0;
        }

        if (isset($_POST['choix_personne_sup'])) {
            $_SESSION['charges_add_prix_sup_personne'] = $_POST['charges_add_prix_sup_personne'];
        } else {
            $_SESSION['charges_add_prix_sup_personne'] = 0;
        }

        $_SESSION['charges_add_taxe_sejour'] = $_POST['charges_add_taxe_sejour'];

        $_SESSION['logement_statut_ligne'] = isset($_POST['logement_statut_ligne'])  ? true : false;
        $_SESSION['choix_linge'] = isset($_POST['choix_linge'])  ? true : false;
        $_SESSION['choix_menage'] = isset($_POST['choix_menage']) ? true : false;
        $_SESSION['choix_transport'] = isset($_POST['choix_transport']) ? true : false;
        $_SESSION['choix_animaux'] = isset($_POST['choix_animaux']) ? true : false;
        $_SESSION['choix_personne_sup'] = isset($_POST['choix_personne_sup']) ? true : false;

        $_SESSION['choix_tv'] = isset($_POST['choix_tv']) ? true : false;
        $_SESSION['choix_machine_a_laver'] = isset($_POST['choix_machine_a_laver']) ? true : false;
        $_SESSION['choix_lave_vaiselle'] = isset($_POST['choix_lave_vaiselle']) ? true : false;
        $_SESSION['choix_wifi'] = isset($_POST['choix_wifi']) ? true : false;

        $_SESSION['choix_climatisation'] = isset($_POST['choix_climatisation']) ? true : false;
        $_SESSION['choix_piscine'] = isset($_POST['choix_piscine']) ? true : false;
        $_SESSION['choix_jacuzzi'] = isset($_POST['choix_jacuzzi']) ? true : false;
        $_SESSION['choix_hammam'] = isset($_POST['choix_hammam']) ? true : false;
        $_SESSION['choix_sauna'] = isset($_POST['choix_sauna']) ? true : false;
        $_SESSION['choix_jardin'] = isset($_POST['choix_jardin']) ? true : false;
        $_SESSION['choix_balcon'] = isset($_POST['choix_balcon']) ? true : false;
        $_SESSION['choix_parking_public'] = isset($_POST['choix_parking_public']) ? true : false;
        $_SESSION['choix_parking_prive'] = isset($_POST['choix_parking_prive']) ? true : false;
        $_SESSION['choix_terrasse'] = isset($_POST['choix_terrasse']) ? true : false;

    // Vérifiez si le dossier "photo_principale" existe, sinon, créez-le
    $photo_principale_dir = "../assets/ressources/images/photo_principale/";
    if (!is_dir($photo_principale_dir)) {
        mkdir($photo_principale_dir, 0755, true);
    }

    // Vérifiez si le dossier "photo_complementaires" existe, sinon, créez-le
    $photo_complementaires_dir = "../assets/ressources/images/photo_complementaires/";
    if (!is_dir($photo_complementaires_dir)) {
        mkdir($photo_complementaires_dir, 0755, true);
    }


        // Traitement de l'image principale (si nécessaire)
    if (($_FILES['logement_photo']) && $_FILES['logement_photo']['error'] == 0) {
        $target_dir = "../assets/ressources/images/photo_principale/";
        $target_file = $target_dir . date('YmdHis') . '.png';
        move_uploaded_file($_FILES['logement_photo']['tmp_name'], $target_file);
        $_SESSION['logement_photo'] = $target_file;
    } else {
        $_SESSION['logement_photo'] = ""; // Si aucune image n'a été téléchargée
    }

    /// Traitement des images complémentaires
    if (isset($_FILES['photo_complementaire'])) {
        $target_dir_temp = "../assets/ressources/images/photo_complementaires/";
        $uploaded_files = array();

        foreach ($_FILES['photo_complementaire']['tmp_name'] as $key => $tmp_name) {
            $file_extension = pathinfo($_FILES['photo_complementaire']['name'][$key], PATHINFO_EXTENSION);
            if($file_extension != ''){
                $target_file = $target_dir_temp . date('YmdHis') . '_' . $key . '.' . $file_extension;
                move_uploaded_file($tmp_name, $target_file);
                $uploaded_files[] = $target_file;
            }
        }

        $_SESSION['photos_complementaires'] = $uploaded_files;
    } else {
        $_SESSION['photos_complementaires'] = array(); // Si aucune image n'a été téléchargée
    }  
?>

<main>
    <div id="group">
        <section id="left_part">
            <article>
                <div id="infos_principales">
                    <!--Accroche-->
                    <h2><?php echo $_SESSION['logement_accroche']?><span id="separation_point"> : </span></h2>
                            
                    <!--Lieu-->
                    <div id = "lieu_logement">
                        <p><?php echo $_SESSION['logement_ville'] . ', ' . $_SESSION['logement_code_postal'] ?></p>
                    </div>
        <!-- affichage des données liée au logement -->
        <h2>Informations sur le Logement</h2>

                    <div id="description">
                        <!--Description -->
                        <div>
                            <h3>Description :</h3>
                            <p id="txt_description"><?php echo  $_SESSION['logement_description'] ?></p>
                        </div>        
                    </div>
                    <hr class="trait_horizontal">

            </article>
        </section>
            <div class="image__selector" id="carousel_sejour">
            <img id="img_carousel" src="<?php echo $_SESSION['logement_photo'] ?>" alt="Image de plongée" />
                <div>

                </div>
            </div>
    </div>



    <div class="infos_comp_et_bloc_reserve">
        <section>
            <article>
                <h3 id="titre_info_comp">Logement :</h3>
                <div class="infos_complementaires">
                    
                    <?php if($_SESSION['logement_nb_pers_sup'] > 0){ ?>
                        <div>
                            <span class="icon people"></span>
                            <p><?php echo "⸱ " . $_SESSION['logement_nb_pers_sup'] . " personnes" ?></p>
                        </div>
                    <?php } ?>

                    <?php if($_SESSION['logement_nb_chambre'] > 0){ ?>
                        <div>
                            <span class = "icon bedroom"></span>
                            <p><?php echo "⸱ " . $_SESSION['logement_nb_chambre'] . " chambres" ?></p>
                        </div>
                    <?php } ?>

                    <?php if($_SESSION['logement_nb_lit_double'] > 0){ ?>
                        <div>
                            <span class="icon double_bed"></span>
                            <p><?php echo "⸱ " . $_SESSION['logement_nb_lit_double'] . " lits doubles"?></p>
                        </div>
                    <?php } ?>

                    <?php if($_SESSION['logement_nb_lit'] > 0){ ?>
                        <div>
                            <span class="icon single_bed"></span>
                            <p><?php echo "⸱ " . $_SESSION['logement_nb_lit'] . " lits simples" ?></p>
                        </div>
                    <?php } ?>

                    <?php if($_SESSION['logement_surface'] > 0){ ?>
                        <div>
                            <span class="icon ruler"></span>
                            <p><?php echo "⸱ " . $_SESSION['logement_surface'] . " m²"?></p>
                        </div>
                    <?php } ?>

                    <?php if($_SESSION['logement_nb_salle_de_bain'] > 0){ ?>
                        <div>
                            <span class="icon shower"></span>
                            <p><?php echo "⸱ " . $_SESSION['logement_nb_salle_de_bain'] . " salle de bain" ?></p>
                        </div>
                    <?php } ?>

                    <?php if($_SESSION['choix_animaux'] == true){ ?>
                        <div>
                            <span class = "icon dog"></span>
                            <p><?php echo "⸱ animaux autorisés" ?></p>
                        </div>
                    <?php } ?>
                </div>

                <div class="dispo_list">
                    <div class="dispo_list_left">
                        <div class="dispo_list_installations">
                            <h3 class="titres_dispo_list">Installations</h3>

                            <?php if($_SESSION['choix_climatisation'] == true){ ?>
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

                            <?php if($_SESSION['choix_piscine'] == true){ ?>
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

                            <?php if($_SESSION['choix_jacuzzi'] == true){ ?>
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
                                    
                            <?php if($_SESSION['choix_hammam'] == true){ ?>
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

                            <?php if($_SESSION['choix_sauna'] == true){ ?>
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

                            <?php if($_SESSION['choix_linge'] == true){ ?>
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

                            <?php if($_SESSION['choix_menage'] == true){ ?>
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

                            <?php if($_SESSION['choix_transport'] == true){ ?>
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

                            <?php if($_SESSION['choix_tv'] == true){ ?>
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

                            <?php if($_SESSION['choix_machine_a_laver'] == true){ ?>
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

                            <?php if($_SESSION['choix_lave_vaiselle'] == true){ ?>
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
                                    
                            <?php if($_SESSION['choix_wifi'] == true){ ?>
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

                            <?php if($_SESSION['choix_jardin'] == true){ ?>
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

                            <?php if($_SESSION['choix_balcon'] == true){ ?>
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

                            <?php if($_SESSION['choix_parking_public'] == true){ ?>
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

                            <?php if($_SESSION['choix_parking_prive'] == true){ ?>
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

                            <?php if($_SESSION['choix_terrasse'] == true){ ?>
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
            </article>
        </section>

        <section>
            <article>
                <div class="bloc_reserver">
                    <h3><?php echo $_SESSION['logement_prix_nuit_base'] . " € la nuit"?></h3>
                    <?php 
                        if($_SESSION['user_type'] == 'client'){ ?>
                                    <form action="../Devis/demandeDeDevis.php" method="post">
                                        <input class="button_form reserver_button" type="submit" value="Demande de devis">
                                    </form>
                    <?php } ?>
                </div>
            </article>
        </section>
                
    </div>

    <hr>
    <form action="./traitement_logement_SQL.php" method="post">
        <div class="popup" id="popup1">
            <div class="overlay"></div>
            <div class="content">
                <h1><b>Logement ajouté</b></h1>
                <div class="bouton_confirmer">
                    <input type="submit" value="valider" class="button_form" id="valider" name="valider">
                </div>
            </div>
        </div>
    </form>
    <form action="./traitement_logement_SQL.php" method="post">
        <div class="popup" id="popup2">
            <div class="overlay"></div>
            <div class="content">
                <h1><b>Logement non ajouté</b></h1>
                <div class="bouton_confirmer">
                    <input type="submit" value="Annuler" class="button_form" id="Annuler">
                </div>
            </div>
        </div>
    </form>

    <div class="centrer">
        <button onclick="ajouter()" class="button_form">Valider</button>
        <button onclick="annuler()" class="button_form">Annuler</button>
    </div>

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
    </main>
        
        
    <?php
    }
    ?>
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