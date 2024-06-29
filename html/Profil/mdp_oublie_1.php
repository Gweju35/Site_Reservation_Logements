<?php
session_start();
$headerVisiteur = '<header>
<img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png">
<h3>Alhaiz-Breizh</h3>
<nav>
    <ul>
    <li id="accueil">
                <a class="nav_button" data-element-id="accueil" href="../index.php">
                    <span class="icon house"></span>
                    <span id="txtAccueil">Accueil</span>
                </a>
            </li>
            <li id="se_connecter">
                <a class="nav_button" data-element-id="se_connecter" href="../Profil/login.php">
                    <span class="icon account"></span>
                    <span>Se connecter</span>
                </a>
            </li>
    </ul>
</nav>
</header>';
$_SESSION['headerVisiteur'] = $headerVisiteur;
$headerProprietaire = '<header>
<img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png">
<h3>Alhaiz-Breizh</h3>
<nav>
    <ul>
        <li id="accueil">
            <a class="nav_button" data-element-id="accueil" href="../index.php">
                <span class="icon house"></span>
                <span id="txtAccueil">Accueil</span>
            </a>
        </li>
        <li id="mes_logements">
            <a class="nav_button" data-element-id="mes_logements" href="../Logements/logement.php">
                <span class="icon bed"></span>
                <span>Mes logements</span>
            </a>
        </li>
        <li id="calendrier">
            <a class="nav_button" data-element-id="calendrier" href="../Calendrier/calendrier.php">
                <span class="icon calendar"></span>
                <span>Calendrier</span>
            </a>
        </li>
        <li id="messagerie">
            <a class="nav_button" data-element-id="messagerie" href="../Messagerie/messagerie.php">
                <span class="icon mail"></span>
                <span>Messagerie</span>
            </a>
        </li>
        <li id="profil">
            <a class="nav_button" data-element-id="profil" href="../Profil/profil.php">
                <span class="icon account"></span>
                <span>Profil</span>
            </a>
        </li>
    </ul>
</nav>
</header>';
$_SESSION['headerProprietaire'] = $headerProprietaire;

$headerClient = '<header>
    <img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png">
    <h3>Alhaiz-Breizh</h3>
    <nav>
        <ul>
            <li id="accueil">
                <a class="nav_button" data-element-id="accueil" href="../index.php">
                    <span class="icon house"></span>
                    <span id="txtAccueil">Accueil</span>
                </a>
            </li>
            
            <li id="reservations">
                <a class="nav_button" data-element-id="reservations" href="../Reservations/reservations.php">
                    <span class="icon bed"></span>
                    <span>Reservations</span>
                </a>
            </li>
            
            <li id="calendrier">
                <a class="nav_button" data-element-id="calendrier" href="../Calendrier/calendrier.php">
                    <span class="icon calendar"></span>
                    <span>Calendrier</span>
                </a>
            </li>
            
            <li id="messagerie">
                <a class="nav_button" data-element-id="messagerie" href="../Messagerie/messagerie.php">
                    <span class="icon mail"></span>
                    <span>Messagerie</span>
                </a>
            </li>
            
            <li id="profil">
                <a class="nav_button" data-element-id="profil" href="../Profil/profil.php">
                    <span class="icon account"></span>
                    <span>Profil</span>
                </a>
            </li>
        </ul>
    </nav>
    </header>';
$_SESSION['headerClient'] = $headerClient;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alhaiz Breizh</title>
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">

    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/mdp_oublie_1.css">
    <script defer src="../assets/index.js"></script>
</head>

<body>

    <!------------------HEADER------------------>

    <?php
    if (isset($_SESSION['user_type']) == false) {
        echo $_SESSION['headerVisiteur'];
    } else if ($_SESSION['user_type'] == 'client') {
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
                <h2>Mot de passe oublié :</h2>
            </div>
        </div>

        <div class="middle">
            <div class="conteneur_mdp_oublie">
                <div class="bleu">
                    <div class="bleu_h2">
                        <h2>Modifier votre mot de passe</h2>
                    </div>
                </div>

                <div class="blanc">
                    <p>Un lien permettant de réinitialiser votre mot de passe va être envoyé à l’adresse e-mail
                        associée à votre compte.</p>
                    <div class="mail">
                        <label for="adresse_mail">Adresse e-mail</label>
                        <input type="text" name="adresse_mail" id="adresse_mail" placeholder="@" required>
                    </div>
                    <input type="submit" value="Envoyer l’e-mail" class="button_form">
                </div>

            </div>
        </div>

    </main>

    <footer>
        <div class="footer">&copy ALHaIZ Breizh</div>
    </footer>

</body>

</html>