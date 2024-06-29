<?php
session_start();

// Vérifiez si l'utilisateur est authentifié
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'proprietaire' || isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'client') {
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

    // Récupérer les informations du logement en fonction de l'ID
    $sql = "SELECT * FROM alhaiz_breizh._proprietaire WHERE compte_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $compte = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$compte) {
        // Redirigez l'utilisateur vers son profil s'il n'a pas la permission
        header("Location: ../Profil/profil.php");
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
    <script defer src="../assets/index.js"></script>

    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/modif_compte_proprio.css">

    <style>
        .error-text {
            color: red;
        }
    </style>
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
    <!--Flèche en haut à gauche-->
    <main>
        <div class="conteneur_titre_back">
            <a href="javascript:history.back()">
                <div class="cercle_retour"><img src="../assets/ressources/icons/left-arrow.svg" alt="icone back" /></div>
            </a>
            <div class="titre_page">
                <h2>Modifier le compte</h2>
            </div>
        </div>

        <form action="../Profil/traitement_modif_compte_proprietaire.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="compte_id" value="<?php echo $compte['compte_id']; ?>">
            <!--Changement du texte selon la civilité-->
            <?php
            if ($compte['compte_civilite'] == "M") {
            ?>
                <div class="champ_inscription2">
                    <label for="compte_civilité">Civilité :</label>
                    <input type="text" id="compte_civilité" name="compte_civilité" value="Monsieur" readonly>
                </div>
            <?php
            } else if ($compte['compte_civilite'] == "Mme") {
            ?>
                <div class="champ_inscription2">
                    <label for="compte_civilité">Civilité :</label>
                    <input type="text" id="compte_civilité" name="compte_civilité" value="Madame" readonly>
                </div>
            <?php
            } else if ($compte['compte_civilite'] == "Autre") {
            ?>
                <div class="champ_inscription2">
                    <label for="compte_civilité">Civilité :</label>
                    <input type="text" id="compte_civilité" name="compte_civilité" value="Madame" readonly>
                </div>
            <?php
            }
            ?>

            <!--Nom et prénom-->
            <div>
                <div class="champ_inscription2">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value=<?php echo $compte['compte_nom']; ?> readonly>
                    <hr>
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" value=<?php echo $compte['compte_prenom']; ?> readonly>
                </div>
            </div>

            <!--Date de naissance-->
            <div class="champ_inscription2">
                <label for="date_naissance">Date de naissance :</label>
                <input type="date" id="date_naissance" name="date_naissance" value=<?php echo $compte['compte_date_naissance']; ?> readonly>
            </div>

            <!--Numéro de téléphone-->
            <div class="champ_inscription">
                <label for="compte_telephone" id="label_tel">Numéro de téléphone * :</label>
                <input type="text" id="telephone" name="compte_telephone" required value=<?php echo $compte['compte_telephone']; ?>>
                <p id="affiche_invalide"></p>
            </div>

            <?php
            $adresse = explode(", ", $compte['compte_adresse']);
            ?>
            <!--Adresse domicile-->
            <div>
                <div class="champ_inscription3">
                    <label for="adresse">Adresse de domicile * :</label>
                    <input type="text" id="adresse" name="adresse" value="<?php echo $adresse[0]; ?>" placeholder="Adresse" maxlength="55" required>
                    <hr>
                    <label for="ville">Ville * :</label>
                    <input type="text" id="ville" name="ville" value="<?php echo $adresse[1]; ?>" placeholder="Ville" maxlength="40" required>
                    <hr>
                    <label for="code_postal" id="label_code_postal">Code postal * :</label>
                    <input type="text" id="code_postal" name="code_postal" value="<?php echo $adresse[2]; ?>" placeholder="Code postal" required>
                </div>
            </div>

            <!--Adresse mail-->
            <div class="champ_inscription">
                <label for="compte_email" id="label_mail">Adresse e-mail * :</label>
                <input type="email" id="compte_email" name="compte_email" required maxlength="50" value="<?php echo $compte['compte_email']; ?>">
            </div>

            <!--Pseudonyme-->
            <div class="champ_inscription2">
                <label for="compte_pseudo">Pseudonyme :</label>
                <input type="text" id="compte_pseudo" name="compte_pseudo" value=<?php echo $compte['compte_pseudo']; ?> readonly>
            </div>

            <!--Mot de passe-->
            <div class="champ_inscription2">
                <label for="compte_mdp">Mot de passe :</label>
                <input type="password" id="compte_mdp" name="compte_mdp" value=<?php echo $compte['compte_mdp']; ?> readonly>
            </div>

            <!--Photo recto-->
            <div class="champ_inscription" id="photo_recto">
                <label for="proprietaire_photo_recto_new">Photo recto de la carte d'identité (PNG, JPG ou JPEG uniquement) :</label>
                <input type="file" id="proprietaire_photo_recto_new" name="proprietaire_photo_recto_new" accept=".png, .jpeg, .jpg">
                <input type="hidden" name="proprietaire_photo_recto_old" id="proprietaire_photo_recto_old" value="<?php echo $compte['proprietaire_photo_recto'] ?>">
            </div>

            <!--Photo verso-->
            <div class="champ_inscription" id="photo_verso">
                <label for="proprietaire_photo_verso_new">Photo verso de la carte d'identité (PNG, JPG ou JPEG uniquement) :</label>
                <input type="file" id="proprietaire_photo_verso_new" name="proprietaire_photo_verso_new" accept=".png, .jpeg, .jpg">
                <input type="hidden" name="proprietaire_photo_verso_old" id="proprietaire_photo_verso_old" value="<?php echo $compte['proprietaire_photo_verso'] ?>">
            </div>

            <!--Langue parlée-->
            <div class="champ_inscription" id="langue_parlee">
                <label for="proprietaire_langue_parlee">Langue parlées :</label>
                <input type="text" id="proprietaire_langue_parlee" name="proprietaire_langue_parlee" maxlength="20" value="<?php echo $compte['proprietaire_langue_parlee'] ?>">
            </div>

            <!--Iban-->
            <div class="champ_inscription">
                <label for="proprietaire_iban" id="label_iban">Iban * :</label>
                <input type="text" id="proprietaire_iban" name="proprietaire_iban" maxlength="16" value="<?php echo $compte['proprietaire_iban']; ?>" required>
            </div>

            <!--Photo de profil-->
            <div class="champ_inscription" id="photo_de_profil">
                <div class="cercle_photo_profil">
                    <img class="photo_de_profil" src="<?php echo $_SESSION['user_photo_profil']; ?>" alt="Photo de Profil">
                </div>
                <label for="compte_photo_profil">Photo de profil (PNG, JPG ou JPEG uniquement) :</label><br>
                <div class="prev_image">
                    <input class="champs_fichiers" type="file" name="photo_profil_new" id="photo_profil_new" accept=".jpg, .jpeg, .png"><br>
                    <img id="previewImage" src="#" alt="Aperçu de l'image" style="display:none; max-width: 100%; max-height: 100px;">
                    <input type="hidden" name="compte_photo_profil_old" id="compte_photo_profil_old" value="<?php echo $_SESSION['user_photo_profil'] ?>"><br>
                </div>
            </div>

            <script>
                document.getElementById('photo_profil_new').addEventListener('change', function(event) {
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

            <!--Bouton confirmer-->
            <div class="popup" id="popup1">
                <div class="overlay"></div>
                <div class="content">
                    <h1>Compte modifié</h1>
                    <div class="bouton_confirmer">
                        <input type="submit" value="Continuer" class="button_form" id="confirmerBtn">
                    </div>
                </div>
            </div>
        </form>
        <div class="bouton_confirmer">
            <button onclick="submitForm()" class="button_form">Confirmer </button>
        </div>

        <script>
            // Fonction pour bloquer la saisie de caractères non numériques et 10 chiffres max

            function allowOnlyNumbers_tel(event) {
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

                // Limiter la saisie à 10 chiffres
                if (inputElement.value.length > 13) {
                    inputElement.value = inputElement.value.slice(0, 13);
                }

                // Formater le numéro avec un espace après chaque deux chiffres
                var formattedNumber = inputElement.value.replace(/(\d{2})(?=\d)/g, '$1 ');
                inputElement.value = formattedNumber;
            }

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

            // Fonction pour bloquer la saisie des chiffres et caractères spéciaux sauf é et è
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

            document.getElementById("telephone").addEventListener("input", allowOnlyNumbers_tel);
            document.getElementById("code_postal").addEventListener("input", allowOnlyNumbers_code);
            document.getElementById("ville").addEventListener("input", blockInvalidCharacters);
            document.getElementById("proprietaire_langue_parlee").addEventListener("input", blockInvalidCharacters);

            // Affiche dynamiquement quand le numéro de téléphone est valide
            function checkTelephoneNumber() {
                var telephoneInput = document.getElementById("telephone");
                var telephoneStrengthMessage = document.getElementById("affiche_invalide");
                var telephone = telephoneInput.value;

                // Vérification si le champ est vide
                if (telephone.length === 0) {
                    // Si le champ est vide, n'afficher aucun message
                    telephoneStrengthMessage.textContent = "";
                    return;
                }

                //Conditions de validations du numéro de téléphone
                var without_space = /^\d{2} \d{2} \d{2} \d{2} \d{2}$/.test(telephone);
                var with_space = /^\d{2} \d{2} \d{2} \d{2} \d{2}\s*$/.test(telephone);

                // Vérification des conditions
                var isValid = without_space && with_space;

                // Affichage du message de force du numéro de téléphone
                if (!isValid) {
                    telephoneStrengthMessage.textContent = "Numéro de téléphone invalide.";
                    telephoneStrengthMessage.style.color = "red";
                } else {
                    telephoneStrengthMessage.textContent = "";
                }
            }

            // Fonction qui permet d'afficher dynamiquement si il y a des erreurs dans les input
            function erreur(labelId, condition) {
                var label = document.getElementById(labelId);

                if (!condition()) {
                    label.classList.add('error-text'); // Ajoute la classe pour la couleur rouge
                } else {
                    label.classList.remove('error-text'); // Retire la classe pour la couleur rouge
                }
            }

            document.getElementById('telephone').addEventListener('input', function() {
                erreur('label_tel', tel);
            });

            document.getElementById('code_postal').addEventListener('input', function() {
                erreur('label_code_postal', code);
            });

            document.getElementById('compte_email').addEventListener('input', function() {
                erreur('label_mail', mail);
            });

            document.getElementById('proprietaire_iban').addEventListener('input', function() {
                erreur('label_iban', iban);
            });

            function submitForm() {

                if (!tel()) {
                    alert("Votre numéro de téléphone n'est pas valide");
                    return;
                }
                if (!code()) {
                    alert("Votre code postal n'est pas valide");
                    return;
                }
                if (!mail()) {
                    alert("Votre mail n'est pas valide");
                    return;
                }
                if (!iban()) {
                    alert("Votre IBAN n'est pas valide");
                    return;
                }
                if (!isFormValid()) {
                    alert("Veuillez remplir tous les champs requis avant de confirmer.");
                    return;
                }
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
                document.getElementById("popup1").classList.add("active");
            }

            function isFormValid() {
                var requiredFields = document.querySelectorAll('[required]');
                for (var i = 0; i < requiredFields.length; i++) {
                    if (!requiredFields[i].value) {
                        return false;
                    }
                }
                return true;
            }

            function tel() {
                var tel = document.getElementById('telephone').value;
                var pattern = /^\d{2} \d{2} \d{2} \d{2} \d{2}$/;

                if (!pattern.test(tel)) {
                    return false;
                }
                return true;
            }

            function code() {
                var code = document.getElementById('code_postal').value;
                var pattern = /^(?!00|99|98|97|96)\d{5}$/;

                if (!pattern.test(code)) {
                    return false;
                }
                return true;
            }

            function mail() {
                var mail = document.getElementById('compte_email').value;
                var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

                if (!pattern.test(mail)) {
                    return false;
                }
                return true;
            }

            function iban() {
                var iban = document.getElementById('proprietaire_iban').value;
                var pattern = /^[A-Z]{2}\d{14}$/;

                if (!pattern.test(iban)) {
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