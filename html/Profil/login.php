<?php
session_start();
$headerVisiteur = '
<header>
        <div class="burger">
            <a href="#" id="openBtn">
                <span class="burger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>
        </div>
        <img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png" class="image_logo">
        <nav>
            <div id="mySidenav" class="sidenav">
                <a id="closeBtn" href="#" class="close">×</a>
                <ul>
                    <li id="accueil">
                        <a class="nav_button" data-element-id="accueil" href="../index.php">
                            <span class="icon house"></span>
                            <span id="txtAccueil">Accueil</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li id="se_connecter">
                        <a class="nav_button" data-element-id="se_connecter" href="../Profil/login.php">
                            <span class="icon account"></span>
                            <span>Se connecter</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>';
$_SESSION['headerVisiteur'] = $headerVisiteur;

$headerProprietaire = '<header>
<div class="burger">
    <a href="#" id="openBtn">
        <span class="burger-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>
</div>
<img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png" class="image_logo">
<nav>
    <div id="mySidenav" class="sidenav">
        <a id="closeBtn" href="#" class="close">×</a>
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
            <li id="reservations">
                <a class="nav_button" data-element-id="reservations" href="../Reservations/reservation_client.php">
                    <span class="icon reservationIcone"></span>
                    <span>Réservations</span>
                </a>
            </li>
            <li id="calendrier">
                <a class="nav_button" data-element-id="calendrier" href="../Calendrier/calendrier.php">
                    <span class="icon calendar"></span>
                    <span>Calendrier</span>
                </a>
            </li>
        </ul>
        <ul>
            <li id="profil">
                <a class="nav_button" data-element-id="profil" href="../Profil/profil.php">
                    <span class="icon account"></span>
                    <span>Profil</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
</header>';
$_SESSION['headerProprietaire'] = $headerProprietaire;

$headerClient = '<header>
<div class="burger">
    <a href="#" id="openBtn">
        <span class="burger-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>
</div>
<img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png" class="image_logo">
<nav>
    <div id="mySidenav" class="sidenav">
        <a id="closeBtn" href="#" class="close">×</a>
        <ul>
            <li id="accueil">
                <a class="nav_button" data-element-id="accueil" href="../index.php">
                    <span class="icon house"></span>
                    <span id="txtAccueil">Accueil</span>
                </a>
            </li>
            <li id="reservations">
                <a class="nav_button" data-element-id="reservations" href="../Reservations/reservation_client.php">
                    <span class="icon reservationIcone"></span>
                    <span>Réservations</span>
                </a>
            </li>
            <li id="calendrier">
                <a class="nav_button" data-element-id="calendrier" href="../Calendrier/calendrier.php">
                    <span class="icon calendar"></span>
                    <span>Calendrier</span>
                </a>
            </li>
        </ul>
        <ul>
            <li id="profil">
                <a class="nav_button" data-element-id="profil" href="../Profil/profil.php">
                    <span class="icon account"></span>
                    <span>Profil</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
</header>';
$_SESSION['headerClient'] = $headerClient;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alhaiz Breizh</title>
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">
    <script defer src="../assets/index.js"></script>


    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/login.css">
</head>
<body>
  
<!--HEADER-->

<?php

if (isset($_SESSION['user_type']) == false || $_SESSION['user_type'] =='visiteur'){
    $_SESSION['user_type']='visiteur';
    echo $_SESSION['headerVisiteur'];
} else if ($_SESSION['user_type'] == 'client'){
    echo $_SESSION['headerClient'];
} else {
    echo $_SESSION['headerProprietaire'];
}
?>

<!--MAIN-->

    <main>
        <img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png">

        <form action="../Profil/verif_login.php" method="post">

        <div class="container_connexion">
            <div class="container_login">
                <label for="login">Nom d'utilisateur</label>
                <input type="text" name="login" id="login" required>
            </div>

            <div class="container_mdp">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" required>
            </div>

            <a href="mdp_oublie_1.php" class="mdp_oublie">Mot de passe oublié</a>

            <input type="submit" value="Connexion" class="button_form">
        </div>

        </form>

        <a href="choix_inscription.php" class="create_account">Créer un compte</a>
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
