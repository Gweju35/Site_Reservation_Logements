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
    <link rel="stylesheet" href="../assets/pages_css/creation_compte_proprio.css">


    <style>
        .error-text {
        color: red;
        }
    </style>

</head>

<body>

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
    
    <!--Flèche en haut à gauche-->
    <div class="conteneur_titre_back">   
            <a href="javascript:history.back()">
                <div class="cercle_retour"><img src="../assets/ressources/icons/left-arrow.svg" alt="icone back" /></div>
            </a>
        <div class="titre_page">
            <h2>Formulaire d'inscription</h2>
        </div>
    </div>    

    <form action="../Profil/traitement_creation_compte_proprio.php" method="post" enctype="multipart/form-data">

    <!--Civilité-->
    <div class="champ_inscription">
        <label for="civilite" id="label_civilite">Civilité * :</label>
        <select id="civilite" name="civilite" required >
            <option value="" disabled selected hidden>Civilité</option>
            <option value="M">Monsieur</option>
            <option value="Mme">Madame</option>
            <option value="Autre">Autre</option>
        </select>  
    </div>

    <!--Nom et prénom-->
    <div>
        <div class="champ_inscription2">
            <label for="nom" id="label_nom">Nom * :</label>
            <input type="text" id="nom" name="nom" placeholder="Nom" maxlength="30" required>
            <hr>
            <label for="prenom" id="label_prenom">Prénom * :</label>
            <input type="text" id="prenom" name="prenom" placeholder="Prénom" maxlength="30" required>
        </div>
    </div>

    <!--Date de naissance--> 
    <div class="champ_inscription">
        <label for="date_naissance" id="label_date_naissance">Date de naissance * :</label>
        <input type="date" id="date_naissance" name="date_naissance" required>
        <p id="affiche_invalide_date"></p>
    </div>
    <!--Numéro de téléphone-->
    <div class="champ_inscription">
        <label for="compte_telephone" id="label_tel">Numéro de téléphone * :</label>
        <input type="text" id="compte_telephone" name="compte_telephone" required  placeholder="00 00 00 00 00"  oninput="checkTelephoneNumber()">
        <p id="telStrength"> </p>
    </div>

    <!--Adresse domicile-->
    <div class="champ_inscription3">
        <label for="adresse" id="label_adresse">Adresse de domicile * :</label>
        <input type="text" id="adresse" name="adresse" placeholder="Adresse" maxlength="55" required>
        <hr>
        <label for="ville">Ville * :</label>
        <input type="text" id="ville" name="ville" placeholder="Ville" maxlength="40" required>
        <hr>
        <label for="code_postal" id="label_code_postal">Code postal * :</label>
        <input type="text" id="code_postal" name="code_postal" placeholder="Code postal" required>
    </div>

    <!--Adresse mail-->
    <div class="champ_inscription">
        <label for="compte_email" id="label_email">Adresse e-mail * :</label>
        <input type="email" id="email" name="compte_email" placeholder="votreadresse@gmail.com" maxlength="50" required>
    </div>            

    <!--Pseudonyme-->
    <div class="champ_inscription">
        <label for="compte_pseudo" id="label_pseudo">Pseudonyme * :</label>
        <input type="text" id="pseudo" name="compte_pseudo" placeholder="votre_pseudo" maxlength="30" required>
    </div>

    <!--Mot de passe-->
    <div class="champ_inscription">
        <label for="mdp" id="label_mdp">Mot de passe * : </label>
        <input type="password" id="mdp" name="mdp"  oninput="checkPassword()" maxlength="30" required><br>
        <p id="passwordStrength"></p>
    </div>

    <!--Photo recto-->
    <div class="champ_inscription" id="photo_recto">
        <label for="photo_recto">Photo recto de la carte d'identité (PNG, JPG ou JPEG uniquement) * :</label>
        <input type="file" id="photo_recto" name="photo_recto" accept=".png, .jpeg, .jpg" required>
    </div>

    <!--Photo verso-->
    <div class="champ_inscription" id="photo_verso">
        <label for="photo_verso">Photo verso de la carte d'identité (PNG, JPG ou JPEG uniquement) * :</label>
        <input type="file" id="photo_verso" name="photo_verso" accept=".png, .jpeg, .jpg" required>
    </div>

    <!--Langue parlée-->
    <div class="champ_inscription" id="langue_parlee">
        <label for="proprietaire_langue_parlee">Langues parlées (20 caractères max) :</label>
        <input type="text" id="proprietaire_langue_parlee" name="proprietaire_langue_parlee" placeholder="Fran, ang, ..." maxlength="20">
    </div>

    <!--Iban-->
    <div class="champ_inscription">
        <label for="proprietaire_iban" id="label_iban">IBAN * :</label>
        <input type="text" id="proprietaire_iban" name="proprietaire_iban" maxlength="16" placeholder="FRXX XXXX XXXX XXXX" required>
    </div>

    <!--Photo de profil-->
    <div class="champ_inscription" id="photo_de_profil">
        <label for="photo_profil">Photo de profil (PNG, JPG ou JPEG uniquement) :</label>
        <div class="prev_image">
            <input class="champs_fichiers" type="file" name="photo_profil" id="photo_profil" accept=".jpg, .jpeg, .png"><br>
            <img id="previewImage" src="#" alt="Aperçu de l'image" style="display:none; max-width: 100%; max-height: 100px;">
        </div>
    </div>

    <script>
        document.getElementById('photo_de_profil').addEventListener('change', function(event) {
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
            <h1>Compte créé</h1>
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

            function OnlyCharacters(event) {
                var inputElement = event.target;
                var inputValue = inputElement.value;

                // Vérifier chaque caractère de l'entrée
                for (var i = 0; i < inputValue.length; i++) {
                    var char = inputValue.charAt(i);

                    // Autoriser les lettres, é, è, ç
                    if (!/[A-Za-zéèç]/.test(char)) {
                        // Bloquer la saisie du caractère non autorisé
                        inputElement.value = inputValue.replace(char, '');

                        // Si l'espace est en premier caractère, le remplacer par une chaîne vide
                        if (i === 0 && char === ' ') {
                            inputElement.value = inputValue.slice(1);
                        }
                    }
                }
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

        // Fonction pour bloquer la saisie de caractères non numériques et 10 chiffres max
        function allowOnlyNumbers_tel(event) {
            var inputElement = event.target;
            var inputValue = inputElement.value;

            // Vérifier chaque caractère de l'entrée
            for (var i = 0; i < inputValue.length; i++) {
                var char = inputValue.charAt(i);
                console.log(char);
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

        // Ajouter des gestionnaires d'événements aux champs de texte
        document.getElementById("nom").addEventListener("input", blockInvalidCharacters);
        document.getElementById("prenom").addEventListener("input", blockInvalidCharacters);
        document.getElementById("ville").addEventListener("input", blockInvalidCharacters);
        document.getElementById("proprietaire_langue_parlee").addEventListener("input", OnlyCharacters);

        document.getElementById("compte_telephone").addEventListener("input", allowOnlyNumbers_tel);

        document.getElementById("code_postal").addEventListener("input", allowOnlyNumbers_code);
        

        function checkPassword() {
            var passwordInput = document.getElementById("mdp");
            var passwordStrengthMessage = document.getElementById("passwordStrength");
            var password = passwordInput.value;

            // Vérification si le champ est vide
            if (password.length === 0) {
                // Si le champ est vide, n'afficher aucun message
                passwordStrengthMessage.textContent = "";
                return;
            }

            // Conditions de validation du mot de passe
            var hasUpperCase = /[A-Z]/.test(password);
            var hasLowerCase = /[a-z]/.test(password);
            var hasDigit = /\d/.test(password);
            var hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            var isLengthValid = password.length >= 8;

            // Vérification de chaque condition
            var isValid = hasUpperCase && hasLowerCase && hasDigit && hasSpecialChar && isLengthValid;

            // Affichage du message de force du mot de passe
            if (!isValid) {
                passwordStrengthMessage.textContent = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                passwordStrengthMessage.style.color = "red";
            }else{
                passwordStrengthMessage.textContent = "";
            }                
        }

        function checkTelephoneNumber() {
            var telephoneInput = document.getElementById("compte_telephone");
            var telephoneStrengthMessage = document.getElementById("telStrength");
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

            // Affiche dynamiquement quand la date de naissance est valide
            function checkDateOfBirth() {
                var dateOfBirthInput = document.getElementById("date_naissance");
                var dateOfBirthMessage = document.getElementById("affiche_invalide_date");

                // Récupération de la date de naissance
                var dateOfBirth = new Date(dateOfBirthInput.value);
                var currentDate = new Date();

                // Calcul de l'âge
                var age = currentDate.getFullYear() - dateOfBirth.getFullYear();

                // Vérification si la personne a au moins 18 ans
                if (age < 18 || (age === 18 && currentDate.getMonth() < dateOfBirth.getMonth()) || (age === 18 && currentDate.getMonth() === dateOfBirth.getMonth() && currentDate.getDate() < dateOfBirth.getDate())) {
                    dateOfBirthMessage.textContent = "Vous devez avoir au moins 18 ans pour vous inscrire.";
                    dateOfBirthMessage.style.color = "red";
                } else {
                    dateOfBirthMessage.textContent = "";
                }

                //Vérification si la personne ne met pas une année de naissance erroné
                if (age > 124) {
                    dateOfBirthMessage.textContent = "Vous ne pouvez pas avoir plus de 100 ans, je ne suis pas dupe.";
                    dateOfBirthMessage.style.color = "red";
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

            // Ajoutez un écouteur d'événements pour déclencher la vérification lors de la modification de la date de naissance
            document.getElementById("date_naissance").addEventListener("change", checkDateOfBirth);
            document.getElementById('nom').addEventListener('input', function() {
                erreur('label_nom', nom);
            });

            document.getElementById('prenom').addEventListener('input', function() {
                erreur('label_prenom', prenom);
            });

            document.getElementById('date_naissance').addEventListener('input', function() {
                erreur('label_date_naissance', age);
            });

            document.getElementById('compte_telephone').addEventListener('input', function() {
                erreur('label_tel', tel);
            });

            document.getElementById('code_postal').addEventListener('input', function() {
                erreur('label_code_postal', code_post);
            });

            document.getElementById('email').addEventListener('input', function() {
                erreur('label_email', mail);
            });

            document.getElementById('pseudo').addEventListener('input', function() {
                erreur('label_pseudo', pseudo);
            });

            document.getElementById('mdp').addEventListener('input', function() {
                erreur('label_mdp', mdp);
            });

            document.getElementById('adresse').addEventListener('input', function() {
                erreur('label_adresse', adresse);
            });

            document.getElementById('proprietaire_iban').addEventListener('input', function() {
                erreur('label_iban', iban);
            });

        function submitForm() {
            if (!isFormValid()) {
                alert("Veuillez remplir tous les champs requis avant de confirmer.");
                return;
            } 
            if (!nom()) {
                alert("Votre nom n'est pas valide");
                return;
            } 
            if (!prenom()) {
                alert("Votre prénom n'est pas valide");
                return;
            } 
            if (!age()) {
                alert("Votre âge n'est pas valide, il faut avoir 18 ans");
                return;
            } 
            if (!tel()) {
                alert("Votre numéro de téléphone n'est pas valide");
                return;
            } 
            if (!code_post()) {
                alert("Votre code postal n'est pas valide");
                return;
            }
            if (!mail()) {
                alert("Votre mail n'est pas valide");
                return;
            }
            if (!pseudo()) {
                alert("Votre pseudonyme n'est pas valide");
                return;
            } 
            if (!mdp()) {
                alert("Votre mot de passe n'est pas valide");
                return;
            } 
            if (!iban()) {
                alert("Votre IBAN n'est pas valide");
                return;
            }
            if (!adresse()) {
                alert("Caractères invalide dans l'adresse");
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

        function nom() {
            var nom = document.getElementById('nom').value;
            var pattern = /^[A-Za-zéè.ç',\- ]+$/;

            if (!pattern.test(nom)) {
                return false;
            }
            return true;
        }
        function prenom() {
            var prenom = document.getElementById('prenom').value;
            var pattern = /^[A-Za-zéè.ç',\- ]+$/;

            if (!pattern.test(prenom)) {
                return false;
            }
            return true;
        }
        function age() {
            var dob = new Date(document.getElementById('date_naissance').value);
            var today = new Date();
            var age = today.getFullYear() - dob.getFullYear();
            var m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            return age >= 18;
        }
        function tel() {
            var tel = document.getElementById('compte_telephone').value;
            var pattern = /^\d{2} \d{2} \d{2} \d{2} \d{2}$/;

            if (!pattern.test(tel)) {
                return false;
            }
            return true;
        }
        function code_post() {
            var code = document.getElementById('code_postal').value;
            var pattern = /^(?!00|99|98|97|96)\d{5}$/;

            if (!pattern.test(code)) {
                return false;
            }
            return true;
        }

        function mail() {
            var mail = document.getElementById('email').value;
            var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

            if (!pattern.test(mail)) {
                return false;
            }
            return true;
        }

        function pseudo() {
            var pseudo = document.getElementById('pseudo').value;
            var pattern = /^[A-Za-z0-9éè.ç_ ]+$/;

            if (!pattern.test(pseudo)) {
                return false;
            }
            return true;
        }

        function mdp() {
            var mdp = document.getElementById('mdp').value;
            var pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

            if (!pattern.test(mdp)) {
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

        function adresse(){
            var adresse = document.getElementById('adresse').value;
            var pattern = /^[A-Za-z0-9éèç\- ]+$/;

            if (!pattern.test(adresse)) {
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