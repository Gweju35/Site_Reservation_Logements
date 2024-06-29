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

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Alhaiz Breizh</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">
    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/Logement/ajout_logement.css">
    
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmOaihDngGhTuTKe9623bFFTNzrdLAzus&libraries=places"></script>
    
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



    <main>

        <div class="conteneur_titre_back">   
            <a href="javascript:history.back()">
                <div class="cercle_retour"><img src="../assets/ressources/icons/left-arrow.svg" alt="icone back" /></div>
            </a>
            <div class="titre_page">
                <h2>Formulaire d'ajout de logement</h2>
            </div>
        </div>

        
        <!-- Création d'un Formulaire -->

        <form action="../Logements/prev_logement_SQL.php" method="post" enctype="multipart/form-data" onsubmit="return verifierNombreDeFichiers();">

            <section>

                <h3 class="titre_section">Informations du logement</h3>

                <div class="section_form">  
                        <label for="logement_accroche">Accroche du logement * :</label>
                        <input class="taille_champs_grand" type="text" name="logement_accroche" id="logement_accroche" required maxlength="100" placeholder="Accroche du logement" ><br>
                    <hr>
                        <label for="logement_description">Description du logement * :</label>
                        <textarea class="taille_champs_texarea" name="logement_description" id="logement_description" required maxlength="1000" placeholder="Description du logement" ></textarea><br>
                    <hr>
                    <!-- permet de mettre a côté deux éléments -->
                    <div class="aligner">
                        <div class="gauche">
                            <label for="logement_nature">Nature du logement * :</label><br>
                            <input class="taille_champs_base" type="text" name="logement_nature" id="logement_nature" required maxlength="20" placeholder="Appartement">
                        </div>  

                        <div class="vertical-bar"></div>

                        <div class="droite">
                            <label for="logement_type">Type de logement * :</label><br>
                            <input class="taille_champs_base" type="text" name="logement_type" id="logement_type" maxlength="30" placeholder="Appartement de ville"> 
                        </div>
                    </div>
                </div>

                <div class="section_form">
                        <label for="logement_adresse">Adresse du logement * :</label>
                        <input class="taille_champs_grand" type="text" name="logement_adresse" id="logement_adresse" required maxlength="100" placeholder="22 rue de la mairie">
                    <hr>
                        <label for="logement_ville">Ville du logement * :</label>
                        <input class="taille_champs_base" type="text" name="logement_ville" id="logement_ville" required maxlength="100" placeholder="Lannion">                           
                    <hr>
                    <div class="aligner">
                        <div class="gauche">
                            <label for="logement_code_postal">Code Postal * :</label><br>
                            <input class="taille_champs_base" type="text" name="logement_code_postal" id="logement_code_postal" pattern="\d{5}" title="Veuillez entrer exactement 5 chiffres" placeholder="22300" required>
                        </div>

                        <!-- 
                        <div class="vertical-bar"></div>
                        
                        
                        <div class="droite">
                            <label for="logement_coordonnees_gps">Coordonnées GPS du logement * :</label><br>
                            <input class="taille_champs_base" type="text" name="logement_coordonnees_gps" id="logement_coordonnees_gps" title="Veuillez entrer un montant au format 00.000,00.000" placeholder="00.000,00.000"> /  pattern="^\d{2}\.\d{3},\d{2}\.\d{3}$" 
                        </div>
                        -->
                    </div>  
                </div>




                <div class="form_nbr">
                    <div class="aligner">
                        <div class="gauche">
                            <label for="logement_prix_nuit_base">Prix de base par nuit (€) * :</label><br>
                            <input class="taille_champs_mini" type="number" name="logement_prix_nuit_base" id="logement_prix_nuit_base" placeholder="5€" step="1" min="1" required>
                        </div>

                        <div class="vertical-bar"></div>

                        <div class="droite">
                            <label for="charges_add_taxe_sejour">Prix de la taxe de séjour (€) * :</label><br>
                            <input class="taille_champs_base" type="number" name="charges_add_taxe_sejour" id="charges_add_taxe_sejour" step="0.01" min="0.20" placeholder="0.20€" required>
                        </div>
                    </div>
                    <hr>
                    <div class="aligner">
                        <div class="gauche">
                            <label for="logement_surface">Surface (m²) * :</label><br>
                            <input class="taille_champs_base" type="number" name="logement_surface" id="logement_surface" step="1" min="9" placeholder="09 m²" required>
                        </div>

                        <div class="vertical-bar"></div>

                        <div class="droite">
                            <label for="logement_nb_chambre">Nombre de chambres * :</label><br>
                            <input class="taille_champs_base" type="number" name="logement_nb_chambre" id="logement_nb_chambre" placeholder="2" step="1" min="0" required>
                        </div>
                    </div>
                    <hr>
                    <div class="aligner">
                        <div class="gauche">
                            <label for="logement_nb_lit">Nombre de lits * :</label><br>
                            <input class="taille_champs_base" type="number" name="logement_nb_lit" id="logement_nb_lit" placeholder="2" step="1" min="1" required>
                        </div>

                        <div class="vertical-bar"></div>

                        <div class="droite">
                            <label for="logement_nb_lit_double">Nombre de lit doubles * :</label><br>
                            <input class="taille_champs_base" type="number" name="logement_nb_lit_double" id="logement_nb_lit_double" placeholder="1" step="1" min="0" required>
                        </div>
                    </div>
                    <hr>
                    <div class="aligner">
                        <div class="gauche">
                            <label for="logement_nb_salle_de_bain">Nombre de salle de bain * :</label><br>
                            <input class="taille_champs_base" type="number" name="logement_nb_salle_de_bain" id="logement_nb_salle_de_bain" placeholder="1" step="1" min="0" required>
                        </div>

                        <div class="vertical-bar"></div>

                        <divc class="droite">
                            <label for="logement_nb_pers_sup">Nombre de personnes dans le logement * :</label><br>
                            <input class="taille_champs_base" type="number" name="logement_nb_pers_sup" id="logement_nb_pers_sup" placeholder="4" step="1" min="1" required>
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
                                <label for="logement_photo">Image principale du logement * :</label><br>
                                <div class="prev_image">
                                    <input class="champs_fichiers" type="file" name="logement_photo" id="logement_photo" accept=".jpg, .jpeg, .png" required><br>
                                    <img id="previewImage" src="#" alt="Aperçu de l'image" style="display:none; max-width: 100%; max-height: 100px;">
                                </div>
                            </div>
                        </div>
                </div>
                <script>
                    document.getElementById('logement_photo').addEventListener('change', function(event) {
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

                    <!-- création des checkbox -->
                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_linge" id="choix_linge" onclick="checkboxClicked()" <?php echo isset($_SESSION['choix_linge']) && $_SESSION['choix_linge'] ? 'checked' : ''; ?>>
                        <label for="choix_linge">Linge : </label>
                        <input class="taille_champs_base" type="number" name="charges_add_linge" id="charges_add_linge" min = "1" step="0.01" onclick="checkboxClicked()" <?php echo isset($_SESSION['choix_linge']) && $_SESSION['choix_linge'] ? '' : 'disabled'; ?> placeholder="00">
                        <p>€</p>
                        <br>
                    </div>


                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_menage" id="choix_menage" onclick="checkboxClicked()" <?php echo isset($_SESSION['choix_menage']) && $_SESSION['choix_menage'] ? 'checked' : ''; ?>><br>
                        <label for="choix_menage">Ménage : </label>
                        <input class="taille_champs_base" type="number" name="charges_add_menage" id="charges_add_menage"  min = "1" step="0.01" onclick="checkboxClicked()" <?php echo isset($_SESSION['choix_menage']) && $_SESSION['choix_menage'] ? '' : 'disabled'; ?> placeholder="00">
                        <p>€</p>
                        <br>

                    </div>



                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_transport" id="choix_transport" onclick="checkboxClicked()" <?php echo isset($_SESSION['choix_transport']) && $_SESSION['choix_transport'] ? 'checked' : ''; ?>><br>
                        <label for="choix_transport">Transport :</label>
                        <input class="taille_champs_base" type="number" name="charges_add_transport" id="charges_add_transport"  min = "1" step="0.01" <?php echo isset($_SESSION['charges_add_transport']) && $_SESSION['charges_add_transport'] ? '' : 'disabled'; ?> placeholder="00">
                        <p>€</p>
                        <br>

                    </div>


                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_animaux" id="choix_animaux" onclick="checkboxClicked()" <?php echo isset($_SESSION['choix_animaux']) && $_SESSION['choix_animaux'] ? 'checked' : ''; ?>><br>
                        <label for="choix_animaux">Animaux domestiques :</label>
                        <input class="taille_champs_base" type="number" name="charges_add_prix_sup_animaux" id="charges_add_prix_sup_animaux"  min = "1" step="0.01" <?php echo isset($_SESSION['choix_animaux']) && $_SESSION['choix_animaux'] ? '' : 'disabled'; ?> placeholder="00"><p>€</p>
                        <br>

                    </div>



                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_personne_sup" id="choix_personne_sup" onclick="checkboxClicked()" <?php echo isset($_SESSION['choix_personne_sup']) && $_SESSION['choix_personne_sup'] ? 'checked' : ''; ?>><br>
                        <label for="choix_personne_sup">Personnes supplémentaires :</label>
                        <input class="taille_champs_base" type="number" name="charges_add_prix_sup_personne" id="charges_add_prix_sup_personne"  min = "1" step="0.01" <?php echo isset($_SESSION['choix_personne_sup']) && $_SESSION['choix_personne_sup'] ? '' : 'disabled'; ?> placeholder="00">
                        <p>€</p>
                        <br>

                    </div>



                    <!-- Script qui permet de rendre la saisie possible des prix si le service est disponnible --> 
                    <script>
                        /* en cas de besoin d'enlever la saise de certain carac et de laisser seulement les chiffres et le point
                        function fullNumber(event) {
                            var inputElement = event.target;
                            var inputValue = inputElement.value;

                            // Remplacer tout caractère non autorisé par une chaîne vide, sauf les chiffres et le point
                            var cleanedValue = inputValue.replace(/[^0-9.]/g, '');

                            // Mettre à jour la valeur de l'élément d'entrée
                            inputElement.value = cleanedValue;
                        }

                        document.getElementById("charges_add_menage").addEventListener("input", fullNumber);
                        document.getElementById("charges_add_linge").addEventListener("input", fullNumber);
                        document.getElementById("charges_add_transport").addEventListener("input", fullNumber);
                        document.getElementById("charges_add_prix_sup_animaux").addEventListener("input", fullNumber);
                        document.getElementById("charges_add_prix_sup_personne").addEventListener("input", fullNumber);
                        document.getElementById("logement_prix_nuit_base").addEventListener("input", fullNumber);
                        document.getElementById("charges_add_taxe_sejour").addEventListener("input", fullNumber);
                        document.getElementById("logement_surface").addEventListener("input", fullNumber);
                        document.getElementById("logement_nb_chambre").addEventListener("input", fullNumber);
                        document.getElementById("logement_nb_lit").addEventListener("input", fullNumber);
                        document.getElementById("logement_nb_lit_double").addEventListener("input", fullNumber);
                        document.getElementById("logement_nb_salle_de_bain").addEventListener("input", fullNumber);
                        document.getElementById("logement_nb_pers_sup").addEventListener("input", fullNumber);
                        */

                        document.addEventListener('DOMContentLoaded', function() {
                            var choixLinge = document.getElementById('choix_linge');
                            var chargesAddLinge = document.getElementById('charges_add_linge');
                            var choixMenage = document.getElementById('choix_menage');
                            var chargesAddMenage = document.getElementById('charges_add_menage');
                            var choixNavette = document.getElementById('choix_transport');
                            var chargesAddNavette = document.getElementById('charges_add_transport');
                            var choixAnimaux = document.getElementById('choix_animaux');
                            var chargesAddPrixSupAnimaux = document.getElementById('charges_add_prix_sup_animaux');
                            var choixPersonneSup = document.getElementById('choix_personne_sup');
                            var chargesAddPrixSupPersonne = document.getElementById('charges_add_prix_sup_personne');

                            choixLinge.addEventListener('change', function() {
                                chargesAddLinge.disabled = !this.checked;
                            });

                            choixMenage.addEventListener('change', function() {
                                chargesAddMenage.disabled = !this.checked;
                            });

                            choixNavette.addEventListener('change', function() {
                                chargesAddNavette.disabled = !this.checked;
                            });

                            choixAnimaux.addEventListener('change', function() {
                                chargesAddPrixSupAnimaux.disabled = !this.checked;
                            });

                            choixPersonneSup.addEventListener('change', function() {
                                chargesAddPrixSupPersonne.disabled = !this.checked;
                            });
                        });
                    </script>

                    
                
                </div>
            </section>
            <section>
                <h3 class="titre_section">Équipements</h3>
                <div class="section_form">
                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_tv" id="choix_tv" onclick="checkboxClicked()"><br>
                        <label for="choix_tv">TV</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_machine_a_laver" id="choix_mal" onclick="checkboxClicked()"><br>
                        <label for="choix_mal">Machine à laver</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_lave_vaiselle" id="choix_lv" onclick="checkboxClicked()"><br>
                        <label for="choix_lv">Lave-vaiselle</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_wifi" id="choix_wifi" onclick="checkboxClicked()"><br>
                        <label for="choix_wifi">Wi-Fi</label>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="titre_section">Installations</h3>
                <div class="section_form">
                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_climatisation" id="choix_clim" onclick="checkboxClicked()"><br>
                        <label for="choix_clim">Climatisation</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_piscine" id="choix_piscine" onclick="checkboxClicked()"><br>
                        <label for="choix_piscine">Piscine</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_jacuzzi" id="choix_jacuzzi" onclick="checkboxClicked()"><br>
                        <label for="choix_jacuzzi">Jacuzzi</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_hammam" id="choix_hammam" onclick="checkboxClicked()"><br>
                        <label for="choix_hammam">Hammam</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_sauna" id="choix_sauna" onclick="checkboxClicked()"><br>
                        <label for="choix_sauna">Sauna</label>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="titre_section">Aménagements</h3>
                <div class="section_form">
                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_jardin" id="choix_jardin" onclick="checkboxClicked()"><br>
                        <label for="choix_jardin">Jardin</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_balcon" id="choix_balcon" onclick="checkboxClicked()"><br>
                        <label for="choix_balcon">Balcon</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_terrasse" id="choix_terrasse" onclick="checkboxClicked()"><br>
                        <label for="choix_terrasse">Terrasse</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_parking_prive" id="choix_pprive" onclick="checkboxClicked()"><br>
                        <label for="choix_pprive">Parking privé</label>
                    </div>

                    <div class="check_form">
                        <input class="taille_checkbox" type="checkbox" name="choix_parking_public" id="choix_ppublic" onclick="checkboxClicked()"><br>
                        <label for="choix_ppublic">Parking public à proximité</label>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="titre_section">Photos Complémentaires</h3>
                <div class="section_form">
                    <label for="photo_complementaire">Les photos à sélectionner :</label><br>
                    <div class="prev_image">
                        <input class="champs_fichiers" type="file" name="photo_complementaire[]" id="photo_complementaire" accept=".jpg, .jpeg, .png" multiple><br>
                        <div id="previewImagesContainer"></div>
                    </div>
                </div>
            </section>


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


                // Script qui permet de limiter le nombre de fichier séléectionné à 6 
                    function verifierNombreDeFichiers() {
                        var input = document.getElementById('photo_complementaire');
                        var files = input.files;

                        if (files.length > 6) {
                            alert("Vous ne pouvez sélectionner que jusqu'à 6 fichiers.");
                            return false; // Empêche l'envoi du formulaire
                        }

                        return true; // Le formulaire est envoyé si tout est correct
                    }
                    // bloquer les caratères afin d'éviter d'avoir des prix trop élevé (max 9999)
        function prix_max(event){
                    var maxLength = 4; 
                    if (this.value.length > maxLength) {
                        this.value = this.value.slice(0, maxLength);
                    }
                }

                document.getElementById("logement_prix_nuit_base").addEventListener("input", prix_max);

                // bloquer les caratères afin d'éviter d'avoir des informations cohérentes (max 99)
                function nb_max2(event){
                    var maxLength = 2; 
                    if (this.value.length > maxLength) {
                        this.value = this.value.slice(0, maxLength);
                    }
                }

                // sur le logement
                document.getElementById("logement_nb_chambre").addEventListener("input", nb_max2);
                document.getElementById("logement_nb_lit").addEventListener("input", nb_max2);
                document.getElementById("logement_nb_lit_double").addEventListener("input", nb_max2);
                document.getElementById("logement_nb_salle_de_bain").addEventListener("input", nb_max2);


                // bloquer les caratères afin d'éviter d'avoir des informations cohérentes (max 9)
                function nb_max1(event){
                    var maxLength = 1; 
                    if (this.value.length > maxLength) {
                        this.value = this.value.slice(0, maxLength);
                    }
                }

                document.getElementById("logement_nb_pers_sup").addEventListener("input", nb_max1);

                // bloquer les caratères afin d'éviter d'avoir des informations cohérentes (max 999)
                function nb_max_3(event){
                    var maxLength = 3; 
                    if (this.value.length > maxLength) {
                        this.value = this.value.slice(0, maxLength);
                    }
                }

                document.getElementById("logement_surface").addEventListener("input", nb_max3);

                // bloquer la taxe sur le logement
                function prix_taxe(event){
                    var maxLength = 5; 
                    if (this.value.length > maxLength) {
                        this.value = this.value.slice(0, maxLength);
                    }
                }

                document.getElementById("charges_add_taxe_sejour").addEventListener("input", prix_taxe)
                document.getElementById("charges_add_linge").addEventListener("input", prix_taxe);
                document.getElementById("charges_add_menage").addEventListener("input", prix_taxe);
                document.getElementById("charges_add_transport").addEventListener("input", prix_taxe);
                document.getElementById("charges_add_prix_sup_animaux").addEventListener("input", prix_taxe);
                document.getElementById("charges_add_prix_sup_personne").addEventListener("input", prix_taxe);
                </script>

                </div>
            </section>

        <br>
        <!-- permet de centrer les élément au centre de la page -->
        <span class="center">
            <input class="button_form" type="submit" value="Prévisualiser">
        </span>
        </form>
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