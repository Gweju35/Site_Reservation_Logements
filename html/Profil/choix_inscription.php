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
                <a class="nav_button" data-element-id="reservations" href="../Reservations/reservations_proprio.php">
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
    <link rel="stylesheet" href="../assets/pages_css/choix_inscription.css">

</head>

<body>

    <?php
    if ($_SESSION['user_type'] == 'visiteur') {
        echo $_SESSION['headerVisiteur'];
    } else if ($_SESSION['user_type'] == 'client') {
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
                <h2>Choix d'inscription</h2>
            </div>
        </div>

        <div class="choix">
            <div>
                <div class="inscription">
                    <h2>Inscrivez-vous en tant que :</h2>
                </div>

                <a href="creation_compte_client.php" class="choix_client">
                        <img src="../assets/ressources/images/dev_client.svg" alt="image devenir client">
                        <div class="texte">
                            <h3>Client</h3>
                            <p>Rejoignez notre communauté de voyageurs et ouvrez la porte à un monde de découvertes.
                                Créez un compte client pour accéder à des milliers de logements uniques, des expériences
                                inoubliables et des rencontres exceptionnelles. Planifiez votre prochain voyage dès
                                maintenant !</p>
                        </div>
                </a>

                <div class="ligne">
                    <div class="trait"></div>
                    <div class="ou">ou</div>
                    <div class="trait"></div>
                </div>

                <a href="creation_compte_proprio.php" class="choix_client">
                        <img src="../assets/ressources/images/dev_proprietaire.svg" alt="image devenir proprio">
                        <div class="texte">
                            <h3>Propriétaire</h3>
                            <p>Devenez propriétaire sur notre plateforme et découvrez une nouvelle façon de maximiser vos
                                revenus en partageant votre espace. Créez un compte dès maintenant pour offrir un accueil chaleureux
                                aux voyageurs du monde entier et leur faire découvrir notre belle Bretagne !</p>
                        </div>
                </a>
            </div>
        </div>



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