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

// Vérifiez si l'ID du logement a été passé via POST
if (isset($_POST['logement_id'])) {
    $logement_id = $_POST['logement_id'];
} else {
    // Redirigez l'utilisateur vers la liste des logements s'il n'y a pas d'ID de logement
    header("Location: ../Logements/logement.php");
    exit();
}

// Connexion à la base de données
include('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les informations du logement en fonction de l'ID
    $sql = "SELECT * FROM alhaiz_breizh._logement WHERE logement_id = :logement_id AND logement_compte_id_proprio = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':logement_id', $logement_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $logement = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT photo_1, photo_2, photo_3, photo_4, photo_5, photo_6 FROM alhaiz_breizh._photo WHERE logement_id = :logement_id ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':logement_id', $logement_id);
    $stmt->execute();

    $photo_comp = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$logement) {
        // Redirigez l'utilisateur vers la liste des logements s'il n'a pas la permission de modifier ce logement
        header("Location: ../Logements/logement.php");
        exit();
    }
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
    <link rel="stylesheet" href="../assets/pages_css/Logement/logement_modif.css">
</head>
<body>
<!------------------HEADER------------------>
<?php
    echo $_SESSION['headerProprietaire'];
?>



<!------------------MAIN------------------>
<main>

<div class="conteneur_titre_back">   
    <a href="javascript:history.back()">
        <div class="cercle_retour"><img src="../assets/ressources/icons/left-arrow.svg" alt="icone back" /></div>
    </a>
    <div class="titre_page">
        <h2>Modifier le Logement</h2>
    </div>
</div>


<!-- Création d'un Formulaire -->

<form onsubmit="return verifierNombreDeFichiers()"action="../Logements/traitement_modif_logement.php" method="post" enctype="multipart/form-data">

    <section>

        <h3 class="titre_section">Informations du logement</h3>

        <div class="section_form">
            <input type="hidden" name="logement_id" id="logement_id"  value="<?php echo $logement['logement_id'];?> " >
                <label for="logement_accroche">Accroche du logement * :</label>
                <input class="taille_champs_grand" type="text" name="logement_accroche" id="logement_accroche"  value="<?php echo $logement['logement_accroche']; ?>" required maxlength="100"><br>
                <hr>
                <label for="logement_description">Description du logement * :</label><br>
                <textarea class="taille_champs_texarea" name="logement_description" id="logement_description" required maxlength="1000"><?php echo $logement['logement_description']; ?></textarea>
                <hr>
                <div class="aligner">
                    <div class="gauche">
                        <label for="logement_nature">Nature du logement * :</label><br>
                        <input class="taille_champs_base" type="text" name="logement_nature" id="logement_nature" value="<?php echo $logement['logement_nature']; ?>" required maxlength="20" >
                    </div>

                    <div class="vertical-bar"></div>

                    <div class="droite">
                        <label for="logement_type">Type de logement * :</label><br>   
                        <input class="taille_champs_base" type="text" name="logement_type" id="logement_type" value="<?php echo $logement['logement_type']; ?>" maxlength="30"> 
                    </div>
                </div>
            </div>

            <div class="section_form">
                <label for="logement_adresse">Adresse du logement * :</label>
                <input class="taille_champs_grand" type="text" name="logement_adresse" id="logement_adresse"  value="<?php echo $logement['logement_adresse']; ?>" required maxlength="55"><br>
                <hr>
                <label for="logement_ville">Ville du logement * :</label>
                <input class="taille_champs_base" type="text" name="logement_ville" id="logement_ville" value="<?php echo $logement['logement_ville']; ?>" required maxlength="40" placeholder="Lannion">
                <hr>
                <div class="aligner">
                    <div class="gauche">
                        <label for="logement_code_postal">Code Postal * :</label><br>
                        <input class="taille_champs_base" type="text" name="logement_code_postal" id="logement_code_postal" pattern="\d{5}" title="Veuillez entrer exactement 5 chiffres" value="<?php echo $logement['logement_code_postal']; ?>" required>
                    </div>

                    
                </div>
            </div>

            <div class="form_nbr">  
                <div class="aligner">
                    <div class ="gauche">
                        <label for="logement_prix_nuit_base">Prix de base par nuit (€) * :</label><br>
                        <input class="taille_champs_mini" type="number" name="logement_prix_nuit_base" id="logement_prix_nuit_base" step="1" min="1" value="<?php echo $logement['logement_prix_nuit_base']; ?>" required>
                    </div>

                    <div class="vertical-bar"></div>


                    <div class="droite">
                        <label for="charges_taxe_sejour">Prix de la taxe de séjour * :</label><br>
                        <input class="taille_champs_base" type="number" name="charges_taxe_sejour" id="charges_taxe_sejour" step="0.01" min="0.20" value="<?php echo $logement['charges_taxe_sejour']; ?>" required>
                    </div>
                </div>
                <hr>
                <div class="aligner">
                    <div class="gauche">
                        <label for="logement_surface">Surface (m²) * :</label><br>
                        <input class="taille_champs_base" type="number" name="logement_surface" id="logement_surface" step="1" min="9" value="<?php echo $logement['logement_surface']; ?>" required>
                    </div>
                    
                    <div class="vertical-bar"></div>

                    <div class="droite">
                        <label for="logement_nb_chambre">Nombre de chambres * :</label><br>
                        <input class="taille_champs_base" type="number" name="logement_nb_chambre" id="logement_nb_chambre" step="1" min="0" value="<?php echo $logement['logement_nb_lit']; ?>" required>
                    </div>
                </div>
                <hr>
                <div class="aligner">
                    <div class="gauche">
                        <label for="logement_nb_lit">Nombre de lits * :</label><br>
                        <input class="taille_champs_base" type="number" name="logement_nb_lit" id="logement_nb_lit" step="1" min="1" value="<?php echo $logement['logement_surface']; ?>" required>
                    </div>

                    <div class="vertical-bar"></div>

                    <div class="droite">
                        <label for="logement_nb_lit_double">Nombre de lit doubles * :</label><br>
                        <input class="taille_champs_base" type="number" name="logement_nb_lit_double" id="logement_nb_lit_double" step="1" min="0"  value="<?php echo $logement['logement_nb_lit_double']; ?>" required>
                    </div>
                </div>
                <hr>
                <div class="aligner">
                    <div class="gauche">
                        <label for="logement_nb_salle_de_bain">Nombre de salle de bain * :</label>
                        <input class="taille_champs_base" type="number" name="logement_nb_salle_de_bain" id="logement_nb_salle_de_bain" step="1" min="0"  value="<?php echo $logement['logement_nb_salle_de_bain']; ?>" required>
                    </div>
                    
                    <div class="vertical-bar"></div>

                    <div class="droite">
                        <div class="special">
                            <label for="logement_personne_max">Nombre de personne * :</label>
                            <input class="taille_champs_base" type="number" name="logement_personne_max" id="logement_personne_max" step="1" min="1" value="<?php echo $logement['logement_personne_max']; ?>" required>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Vérification sur le nombre de lits --->
            <script>
                function verifierLits() {
                    var lits = parseInt(document.getElementById('logement_nb_lit').value);
                    var litsDoubles = parseInt(document.getElementById('logement_nb_lit_double').value);

                    if (litsDoubles > lits) {
                        alert("Le nombre de lits doubles ne peut pas être supérieur au nombre total de lits.");
                        return false; // Empêche l'envoi du formulaire
                    }

                    return true; // Le formulaire est envoyé si tout est correct
                }
            </script>
                <!-- 
                LE LOGEMENT A POUR VALEUR PAR DEFAULT -> TRUE

                <label for="logement_statut_ligne">Mettre en Ligne le Logement :</label>
                <input type="checkbox" name="logement_statut_ligne" id="logement_statut_ligne" <?php echo isset($_SESSION['logement_statut_ligne']) && $_SESSION['logement_statut_ligne'] ? 'checked' : ''; ?>><br>
                --->
            <div class="section_form">
                <div class="conteneur_photo_principale">
                    <label for="logement_photo" >Image principale du logement * :</label><br>
                    <div class="prev_image">
                        <img class="photo_principale" src="<?php echo $logement['logement_photo']; ?>"><br>
                        
                            <input class="champs_fichiers" type="file" name="logement_photo_new" id="logement_photo_new" accept=".jpg, .jpeg, .png"><br>
                            
                            <img id="previewImage" src="#" alt="Aperçu de l'image" style="display:none; max-width: 100%; max-height: 100px;">
                        <input type="hidden" name="logement_photo_old" id="logement_photo_old"  value="<?php echo $logement['logement_photo'];?> " >
                    </div>    
                </div>
            </div>
                
            <script>
                    document.getElementById('logement_photo_new').addEventListener('change', function(event) {
                    var input = event.target;
                    var previewImage = document.getElementById('previewImage');

                    if (input.files && input.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function(e) {
                            previewImage.style.display = 'block';
                            previewImage.src = e.target.result;
                        };

                        reader.readAsDataURL(input.files[0]);
                    }
                    });
                </script>
    </section>
    
    <input type="hidden" name="logement_compte_id_proprio" value="<?php echo $_SESSION['user_id'] ?>">
    
    <section>

        <h3 class="titre_section">Services & Charges Additionnelles</h3>

        <div class="section_form">

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="service_linge" id="service_linge" onclick="checkboxClicked('service_linge','charges_linge')" <?php echo $logement['service_linge'] ? 'checked' : ''; ?>>
                <label for="service_linge">Linge :</label>
                <input class="taille_champs_base" type="number" name="charges_linge" id="charges_linge" step="0.01"  <?php echo $logement['service_linge'] ? '' : 'disabled'; ?> value="<?php echo $logement['charges_linge']; ?>"><br>
                <p>€</p>
            </div>


            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="service_menage" id="service_menage" onclick="checkboxClicked('service_menage','charges_menage')" <?php echo $logement['service_menage'] ? 'checked' : ''; ?>>
                <label for="service_menage">Ménage :</label>
                <input class="taille_champs_base" type="number" name="charges_menage" id="charges_menage" step="0.01" <?php echo $logement['service_menage'] ? '' : 'disabled'; ?> value="<?php echo $logement['charges_menage']; ?>">
                <p>€</p>
            </div>


            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="service_transport" id="service_transport" onclick="checkboxClicked('service_transport','charges_transport')" <?php echo $logement['service_transport'] ? 'checked' : ''; ?>>
                <label for="service_transport">Transport :</label>
                <input class="taille_champs_base" type="number" name="charges_transport" id="charges_transport" step="0.01" <?php echo $logement['service_transport'] ? '' : 'disabled'; ?> value="<?php echo $logement['charges_transport']; ?>">
                <p>€</p>
            </div>


            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="service_animaux_domestique" id="service_animaux_domestique" onclick="checkboxClicked('service_animaux_domestique','charges_animaux')" <?php echo $logement['service_animaux_domestique'] ? 'checked' : ''; ?>>
                <label for="service_animaux_domestique">Animaux domestiques :</label>
                <input class="taille_champs_base" type="number" name="charges_animaux" id="charges_animaux" step="0.01" <?php echo $logement['service_animaux_domestique'] ? '' : 'disabled'; ?> value="<?php echo $logement['charges_animaux']; ?>">
                <p>€</p>
            </div>


            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="service_personne_sup" id="service_personne_sup" onclick="checkboxClicked('service_personne_sup','charges_personne_sup')" <?php echo $logement['service_personne_sup'] ? 'checked' : ''; ?>>
                <label for="service_personne_sup">Personnes supplémentaires :</label>
                <input class="taille_champs_base" type="number" name="charges_personne_sup" id="charges_personne_sup" step="0.01" <?php echo $logement['service_personne_sup'] ? '' : 'disabled'; ?> value="<?php echo $logement['charges_personne_sup']; ?>">
                <p>€</p>
            </div>

            <!-- Script qui permet de rendre la saisie possible des prix si le service est disponnible --> 
            
            <script>
                function checkboxClicked(checkboxId,fieldId){
                    var checkbox = document.getElementById(checkboxId);
                    var field = document.getElementById(fieldId);

                    field.disabled = !checkbox.checked;
                };
            </script>

            
        
        </div>
    </section>

    <section>
        <h3 class="titre_section">Équipements</h3>
        
        <div class="section_form">
            
            
            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="equipement_tv" id="equipement_tv" onclick="checkboxClicked()" <?php echo $logement['equipement_tv'] ? 'checked' : ''; ?>><br>
                <label for="equipement_tv">TV</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="equipement_machine_a_laver" id="equipement_machine_a_laver" onclick="checkboxClicked()" <?php echo $logement['equipement_machine_a_laver']  ? 'checked' : ''; ?>><br>
                <label for="equipement_machine_a_laver">Machine à laver</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="equipement_lave_vaisselle" id="equipement_lave_vaisselle" onclick="checkboxClicked()" <?php echo $logement['equipement_lave_vaisselle'] ? 'checked' : ''; ?>><br>
                <label for="equipement_lave_vaisselle">Lave-vaiselle</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="equipement_wifi" id="equipement_wifi" onclick="checkboxClicked()" <?php echo $logement['equipement_wifi'] ? 'checked' : ''; ?>><br>
                <label for="equipement_wifi">Wi-Fi</label>
            </div>
        </div>
    </section>

    <section>

        <h3 class="titre_section">Installations</h3>

        <div class="section_form">
            

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="installation_climatisation" id="installation_climatisation" onclick="checkboxClicked()" <?php echo $logement['installation_climatisation'] ? 'checked' : ''; ?>><br>
                <label for="installation_climatisation">Climatisation</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="installation_piscine" id="installation_piscine" onclick="checkboxClicked()" <?php echo $logement['installation_piscine'] ? 'checked' : ''; ?>><br>
                <label for="installation_piscine">Piscine</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="installation_jacuzzi" id="installation_jacuzzi" onclick="checkboxClicked()" <?php echo $logement['installation_jacuzzi'] ? 'checked' : ''; ?>><br>
                <label for="installation_jacuzzi">Jacuzzi</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="installation_hammam" id="installation_hammam"  onclick="checkboxClicked()" <?php echo $logement['installation_hammam'] ? 'checked' : ''; ?>><br>
                <label for="installation_hammam">Hammam</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="installation_sauna" id="installation_sauna" onclick="checkboxClicked()" <?php echo $logement['installation_sauna'] ? 'checked' : ''; ?>><br>
                <label for="installation_sauna">Sauna</label>
            </div>
        </div>
    </section>

    <section>

        <h3 class="titre_section">Aménagements</h3>

        <div class="section_form">
            

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="amenagement_jardin" id="amenagement_jardin" onclick="checkboxClicked()" <?php echo $logement['amenagement_jardin'] ? 'checked' : ''; ?>><br>
                <label for="amenagement_jardin">Jardin</label> 
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="amenagement_balcon" id="amenagement_balcon" onclick="checkboxClicked()" <?php echo $logement['amenagement_balcon'] ? 'checked' : ''; ?>><br>
                <label for="amenagement_balcon">Balcon</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="amenagement_terrasse" id="amenagement_terrasse" onclick="checkboxClicked()" <?php echo $logement['amenagement_terrasse'] ? 'checked' : ''; ?>><br>
                <label for="amenagement_terrasse">Terrasse</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="amenagement_parking_prive" id="amenagement_parking_prive" onclick="checkboxClicked()" <?php echo $logement['amenagement_parking_prive'] ? 'checked' : ''; ?>><br>
                <label for="amenagement_parking_prive">Parking privé</label>
            </div>

            <div class="check_form">
                <input class="taille_checkbox" type="checkbox" name="amenagement_parking_public" id="amenagement_parking_public" onclick="checkboxClicked()" <?php echo $logement['amenagement_parking_public'] ? 'checked' : ''; ?>><br>
                <label for="amenagement_parking_public">Parking public à proximité</label>
            </div>
        </div>
    </section>

    <section>

        <h3 class="titre_section">Photos Complémentaires</h3>

        <div class="section_form">

            
                <?php 
                    $cpt=1;
                    $cpt1=1;
                    foreach ($photo_comp as $photos){
                        if($photos != ''){?>                                
                            <div class="check_form">
                                <img class="photo_complementaire" src="<?php echo $photos; ?>" alt="Photo complémentaire">

                                <input class="taille_checkbox" type="checkbox" name="photos_<?php echo $cpt ?>_supprimer">
                                <label>Supprimer :</label>
                                <label for="Photo_complementaire">Photo <?php echo $cpt1 ?></label>
                                <input type="hidden" name="photo_att_<?php echo $cpt ?>" id="photo_att_<?php echo $cpt ?>"  value="<?php echo 'photo_'.$cpt ?>">
                                
                                <input type="hidden" name="photo_<?php echo $cpt ?>" id="photo_<?php echo $cpt ?>"  value="<?php echo $photos; ?>" >
                                
                                <input type="hidden" name="logement_id" id="logement_id"  value="<?php echo $logement['logement_id'];?>">
                                <?php $cpt1++; ?>
                            </div>
                    <?php   
                        }$cpt++;
                    } ?>    
                <br><label for="photo_complementaire">Les photos à sélectionner :</label><br>
                    <div class="prev_image">
                        <input class="champs_fichiers" type="file" name="photo_complementaire[]" id="photo_complementaire" accept=".jpg, .jpeg, .png" multiple><br>
                        <div id="previewImagesContainer"></div>
                    </div>
            
                    <script>
                        document.getElementById('photo_complementaire').addEventListener('change', function(event) {
                            var input = event.target;
                            var previewImagesContainer = document.getElementById('previewImagesContainer');
                            previewImagesContainer.innerHTML = ''; // Clear previous images

                            if (input.files && input.files.length > 0) {
                                for (var i = 0; i < input.files.length; i++) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        var img = document.createElement('img');
                                        img.src = e.target.result;
                                        img.style.maxWidth = '100%';
                                        img.style.maxHeight = '100px';
                                        img.style.margin = '10px';
                                        previewImagesContainer.appendChild(img);
                                    };
                                    reader.readAsDataURL(input.files[i]);
                                }
                            }
                        });



                        function blockInvalidCharacters(event) {
                        var inputElement = event.target;
                        var inputValue = inputElement.value;

                        // Vérifier chaque caractère de l'entrée
                        for (var i = 0; i < inputValue.length; i++) {
                            var char = inputValue.charAt(i);

                            // Autoriser les lettres, é, è, ".", ",", "-", "ç", "'"
                            if (!/[A-Za-zéè.ç',\- ]/.test(char)) {
                                // Bloquer la saisie du caractère non autorisé
                                inputElement.value = inputValue.replace(char, '');

                                // Si l'espace est en premier caractère, le remplacer par une chaîne vide
                                if (i === 0 && char === ' ') {
                                    inputElement.value = inputValue.slice(1);
                                }
                            }
                        }
                    }

                    document.getElementById("logement_accroche").addEventListener("input", blockInvalidCharacters);
                    document.getElementById("logement_nature").addEventListener("input", blockInvalidCharacters);
                    document.getElementById("logement_type").addEventListener("input", blockInvalidCharacters);
                    document.getElementById("logement_ville").addEventListener("input", blockInvalidCharacters);

                // Fonction pour bloquer la saisie de caractères non numériques et 5 chiffres max 
                function allowOnlyNumbers_code(event) {
                    var inputElement = event.target;
                    var inputValue = inputElement.value;

                    // Vérifier chaque caractère de l'entrée
                    for (var i = 0; i < inputValue.length; i++) {
                        var char = inputValue.charAt(i);

                        // Bloquer la saisie si le caractère n'est pas un chiffre
                        if (!/[0-9]/.test(char)) {
                            // Bloquer la saisie du caractère non autorisé
                            inputElement.value = inputValue.replace(char, '');
                        }
                    }
                    // Limiter la saisie à 5 chiffres
                    if (inputElement.value.length > 5) {
                        inputElement.value = inputElement.value.slice(0, 5);
                    }
                }

                document.getElementById("logement_code_postal").addEventListener("input", allowOnlyNumbers_code);
                    </script>
                <!-- Script qui permet de limiter le nombre de fichier séléectionné à 6 -->

        </div>
    </section>

<br>
    <!--Bouton confirmer-->
    <div class="popup" id="popup1">
        <div class="overlay"></div>
        <div class="content">
            <h1><b>Logement modifié</b></h1>
            <div class="bouton_confirmer">
                <input type="submit" value="Continuer" class="button_form" id="confirmerBtn">
            </div>
        </div>
    </div>
</form>
    <div class="centrer">
        <button onclick="submitForm()" class="button_form">Confirmer</button>
        <!-- Lien de retour vers la liste des logements -->              
        <a class="button_form" href="./logement.php">Retour à la liste des logements</a>
    </div>
    <script>
        function submitForm() {
            if(!verifierNombreDeFichiers()){
                alert("6 photos complémentaires maximum");
            }
            if (!code_post()) {
                    alert("Votre code postal n'est pas valide");
                    return;
            }

            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
            document.getElementById("popup1").classList.add("active");
        }

        function verifierNombreDeFichiers() {
            var input = document.getElementById('photo_complementaire');
            var files = input.files;
            var photosActuelles = document.querySelectorAll('.photo_complementaire').length; // Nombre actuel de photos
            var casesCochees = document.querySelectorAll('input[type="checkbox"][name^="photos_"]:checked').length; // Nombre de cases cochées
            

            var totalPhotos = photosActuelles + files.length - casesCochees;

            if (totalPhotos > 6) {
                return false; // Empêche l'envoi du formulaire
            }

            return true; // Le formulaire est envoyé si tout est correct
        }

        function code_post() {
                var code = document.getElementById('logement_code_postal').value;
                var pattern = /^\d{5}$/;
                var pattern_verif =  /^(?!00\d{3}).*$/;

                if (!pattern.test(code) || !pattern_verif.test(code)) {
                    return false;
                }
                return true;
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