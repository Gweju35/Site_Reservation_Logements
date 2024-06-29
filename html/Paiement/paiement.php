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

    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/Paiement/paiement.css">
    <script defer src="../assets/index.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('date_expiration').addEventListener('input', function (e) {
                // Obtenez la valeur actuelle de l'input
                let inputValue = e.target.value;

                // Supprimez tous les caractères non numériques
                inputValue = inputValue.replace(/\D/g, '');

                // Ajoutez le caractère "/" après les 2 premiers chiffres
                if (inputValue.length > 2) {
                    inputValue = inputValue.slice(0, 2) + '/' + inputValue.slice(2);
                }

                // Mettez à jour la valeur de l'input
                e.target.value = inputValue;
            });
        });
    </script>
</head>

<body>

    <!-- HEADER -->
    <?php
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'client') {
        echo $_SESSION['headerClient'];
    } else {
        echo $_SESSION['headerProprietaire'];
    }
    ?>

    <!-- MAIN -->
    <main>

        <?php
        // Assurez-vous que $_POST['devis_id'] est défini avant de l'utiliser
        if (isset($_POST['devis_id'])) {
            $_SESSION['devis_id'] = $_POST['devis_id'];
        } else {
            // Redirigez l'utilisateur vers la page de devis s'il n'a pas sélectionné de devis
            header("Location: ../Devis/devis_du_client.php");
            exit();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'client') {
            $user_id = $_SESSION['user_id'];
        } else {
            // Redirigez l'utilisateur vers la page de login s'il n'est pas authentifié ou s'il n'est pas un client
            header("Location: ../Profil/login.php");
            exit();
        }

        include('../../libs/connect_params.php');
        // Établir une connexion à la base de données
        try {
            $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Exécuter une requête pour obtenir le montant à payer depuis la base de données
            $client_id = $user_id; // Utilisez l'ID du client authentifié
            $query = "SELECT d.devis_id, d.devis_prix_ttc, c.compte_nom, c.compte_prenom 
                  FROM alhaiz_breizh._devis AS d
                  JOIN alhaiz_breizh._client AS c ON d.devis_id_compte_client = c.compte_id
                  WHERE d.devis_id_compte_client = :client_id AND d.devis_id = :devis_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':devis_id', $_SESSION['devis_id'], PDO::PARAM_INT);
            $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
            $stmt->execute();

            $devisInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
        ?>

        <div class="conteneur_titre_back">
            <a href="../Devis/devis_du_client.php">
                <div class="cercle_retour"><img src="../assets/ressources/icons/left-arrow.svg" alt="icone back" /></div>
            </a>
            <div class="titre_page">
                <h2>Paiement</h2>
            </div>
        </div>

        <div class="conteneur_paiement">
            <div class="entete">
                <h2>Renseignez votre carte de paiement</h2>
                <div class="trait"></div>
                <img src="../assets/ressources/icons/Logos_paiement.svg" alt="icone paiement" class="icone_paiement">
            </div>

            <form method="post" action="../Paiement/traitement_paiement.php">
                <input type="hidden" name="devis_id" value="<?php echo $_SESSION['devis_id']; ?>">

                <div class="paiement">

                    <div class="titre_form">
                        <h2>Votre carte bancaire</h2>
                        <img src="../assets/ressources/icons/cb.svg" alt="carte bancaire" class="logo_cb">
                    </div>

                    <div class="champ">
                        <label for="num_carte">Numéro de carte *</label>
                        <input type="text" name="num_carte" id="num_carte" maxlength="19" placeholder='**** **** **** ****' required>
                    </div>

                    <div class="alignement">
                        <div class="champ expiration">
                            <label for="date_expiration">Date d'expiration *</label>
                            <input type="text" name="date_expiration" id="date_expiration" placeholder="MM/AA" maxlength="5" required>
                        </div>

                        <div class="champ crypto">
                            <label for="cvv">Cryptogramme *</label>
                            <input type="text" name="cvv" id="cvv" placeholder="CVV"  required>
                        </div>
                    </div>

                    <div class="champ">
                        <label for="nom_titulaire">Nom du titulaire *</label>
                        <input type="text" name="nom_titulaire" id="nom_titulaire" placeholder="Prénom Nom" required>
                    </div>

                </div>

                <div class="check_form">
                    <input class="taille_checkbox" type="checkbox" name="accepte_conditions" id="accepte_conditions" required>
                    <label for="accepte_conditions">J’accepte les conditions générales de vente</label>
                </div>

                <div class="popup" id="popup1">
                    <div class="overlay"></div>
                    <div class="content">
                        <h1><b>Logement payé</b></h1>
                        <div class="bouton_confirmer">
                            <input type="submit" value="Continuer" class="button_form" id="confirmerBtn">
                        </div>
                    </div>
                </div>
            </form>

            <div class="bouton_confirmer">
                <button onclick="submitForm()" class="button_form">
                    <?php echo "Payer " . round($_POST['prix_devis'], 2) . '€'; ?>
                </button>
            </div>

            <div class="security">
                <img src="../assets/ressources/icons/Cadenas.svg" alt="security" class="cadenas">
                <p>Paiement 100% sécurisé</p>
            </div>
        </div>

        <script>
            // Fonction pour bloquer la saisie de caractères non numériques et 12 chiffres max
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

                // Limiter la saisie à 10 chiffres
                if (inputElement.value.length > 22) {
                    inputElement.value = inputElement.value.slice(0, 22);
                }

                // Formater le numéro avec un espace après chaque deux chiffres
                var formattedNumber = inputElement.value.replace(/(\d{4})(?=\d)/g, '$1 ');
                inputElement.value = formattedNumber;
            }

            document.getElementById("num_carte").addEventListener("input", allowOnlyNumbers_code);

            // Fonction pour bloquer la saisie de caractères non numériques et 3 chiffres max
            function allowOnlyNumbers_cvv(event) {
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

                // Limiter la saisie à 3 chiffres
                if (inputElement.value.length > 3) {
                    inputElement.value = inputElement.value.slice(0, 3);
                }
            }

            document.getElementById("cvv").addEventListener("input", allowOnlyNumbers_cvv);

            function submitForm() {
                if (!all()) {
                    alert("Tous les champs ne sont pas saisis");
                    return;
                }
                if (!numeroCarte()) {
                    alert("Numéro de carte invalide");
                    return;
                }
                if (!verifDate()) {
                    alert("La date d'expiration de la carte est expirée");
                    return;
                }
                if (!crypto()) {
                    alert("Cryptogramme invalide");
                    return;
                }
                if (!nomTitu()) {
                    alert("Nom du titulaire invalide");
                    return;
                }
                if (!document.getElementById('accepte_conditions').checked) {
                    alert("Acceptez les CGV");
                    return;
                }
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
                document.getElementById("popup1").classList.add("active");
            }

            function verifDate() {
                
            }


            function all() {
                var requiredFields = document.querySelectorAll('[required]');
                for (var i = 0; i < requiredFields.length; i++) {
                    if (!requiredFields[i].value.trim()) {
                        return false;
                    }
                }
                return true;
            }

            function numeroCarte() {
                var num_carte = document.getElementById('num_carte').value;
                var pattern = /^\d{4} \d{4} \d{4} \d{4}$/; // Expression régulière pour le format "XXXX XXXX XXXX XXXX"

                return pattern.test(num_carte);
            }

            function verifDate() {
                var dateInput = document.getElementById('date_expiration').value;

                // Vérifier le format MM/AA
                var pattern = /^(0[1-9]|1[0-2])\/(2[4-9]|2[4-9])$/;

                if (!pattern.test(dateInput)) {
                    return false;
                }

                return true;
            }

            function crypto() {
                var crypto = document.getElementById('cvv').value;
                var pattern = /^\d{3}$/;

                return pattern.test(crypto);
            }

            function nomTitu() {
                var nom_titulaire = document.getElementById('nom_titulaire').value;
                var pattern = /^[a-zA-Z\s]+$/;

                return pattern.test(nom_titulaire);
            }
        </script>

    </main>

    <!-- FOOTER -->
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
