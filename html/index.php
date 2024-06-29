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
        <a id="logoSite" href="/index.php" tabindex="-1">
        <img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png" class="image_logo">
        </a>
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
<a id="logoSite" href="/index.php" tabindex="-1">
        <img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png" class="image_logo">
        </a>
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
<a id="logoSite" href="/index.php" tabindex="-1"a>
        <img alt="Logo de Alhaiz-Breizh" src="../assets/ressources/images/logo.png" class="image_logo">
        </a>
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

    <link rel="stylesheet" href="./assets/main.css">
    <link rel="stylesheet" href="./assets/pages_css/accueil.css">
    <link rel="stylesheet" href="./assets/pages_css/map.css">

    <script defer src="../assets/index.js"></script>

    <!--Code leaflet map-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"
        integrity="sha512-+k1pnlgt4F1H8L7t3z95o3/KO+o78INEcXTbnoJQ/F2VqDVhWoaiVml/OEHv9HsVgxUaVW+IbiZPUJQfF/YxZw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="../assets/filtres.js" defer></script>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

    <script src="./assets/map.js" defer></script>
    <link rel="stylesheet" href="./assets/pages_css/map.css">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- les étoiles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>

    <!------------------HEADER------------------>

    <?php

    if (isset($_SESSION['user_type']) == false || $_SESSION['user_type'] == 'visiteur') {
        $_SESSION['user_type'] = 'visiteur';
        echo $_SESSION['headerVisiteur'];
    } else if ($_SESSION['user_type'] == 'client') {
        echo $_SESSION['headerClient'];
    } else {
        echo $_SESSION['headerProprietaire'];
    }
    ?>

    <!------------------MAIN------------------>
    <div class="background"></div>
    <main>


        <?php

        include ('../libs/connect_params.php');

        try {
            $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Récupérer les logements du propriétaire
            $sql = "SELECT * FROM alhaiz_breizh._logement WHERE logement_statut_ligne = true";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $logements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
        }
        $i = 0;
        ?>

        <section id="contenuPage" class="affichage1">




            <div class="container2">
                <div class="iconFiltres" id="iconBtnFiltres">
                    <p>Filtres</p>
                    <img src="./assets/ressources/icons/filtre.svg" alt="icone filtres">
                </div>
                <?php
                if ($_SESSION['user_type'] == 'visiteur') { ?>
                    <a class="bouton_devenir_client" href="Profil/creation_compte_client.php">
                        <div class="bouton_devenir_client-container">
                            <span><img src="../assets/ressources/images/dev_client.svg" alt="image devenir client"></span>
                            <p id="txt_devenir_client">VOUS AVEZ ENVIE DE DEVENIR CLIENT ?</p>
                        </div>
                    </a>
                <?php } ?>

                <div class="iconCarte" id="iconBtnCarte">
                    <p>Carte</p>
                    <img src="./assets/ressources/icons/map.svg" alt="icone carte">
                </div>
            </div>


            <div id="divListeLogements">
                <ul id="listeLogements" class="liste_logement"></ul>
            </div>

            <script>
                
                
                function moy_note_logement() {
                    let tableauAvis = {};
                    $.ajax({
                        url: 'traitementAvis.php',
                        method: 'POST',
                        async: false,
                        data: {},
                        success: function (response) {
                            var result = JSON.parse(response)

                            var test = result.events

                            test.forEach(avis => {
                                tableauAvis[avis.avis_id_logement] = avis.moyenne_notes;
                            });
                            //console.log('EVVVEEENTS',tableauAvis)
                        },
                        error: function (xhr, status, error) {
                            console.error('Erreur', error);
                        }
                    })
                    return tableauAvis
                }

                



                
                document.addEventListener('DOMContentLoaded', function () {
                    var radios = document.querySelectorAll('input[type="radio"][name="tri"]');

                    //Pour décocher les boutons radios
                    radios.forEach(function (radio) {
                        radio.addEventListener('click', function () {
                            if (this.classList.contains('checked')) {
                                this.checked = false;
                                this.classList.remove('checked');
                            } else {
                                this.classList.add('checked');
                            }

                            filtrerLogements();
                        });
                    });
                });

                document.addEventListener('DOMContentLoaded', function () {
                    // Sélectionnez vos éléments de sliders
                    var rangeMin = document.querySelector('.range-min');
                    var rangeMax = document.querySelector('.range-max');

                    // Ajoutez des écouteurs d'événements pour détecter les changements de valeur sur les sliders
                    rangeMin.addEventListener('change', filtrerLogements);
                    rangeMax.addEventListener('change', filtrerLogements);

                    // Chargez le script slider.js
                    var scriptElement = document.createElement('script');
                    scriptElement.src = 'slider.js';
                    document.body.appendChild(scriptElement);
                });


                document.addEventListener('DOMContentLoaded', function () {
                    // Sélectionnez vos éléments input-min et input-max
                    var inputMin = document.querySelector('.input-min');
                    var inputMax = document.querySelector('.input-max');

                    // Ajoutez des écouteurs d'événements pour détecter les changements de valeur sur ces éléments
                    inputMin.addEventListener('input', filtrerLogements);
                    inputMax.addEventListener('input', filtrerLogements);
                });



            </script>


            <div id="mySidenavFiltres" class="sidenavFiltres">
                <form method="post" enctype="multipart/form-data">
                <button id="btnResetFilter" class="button_form" onclick="resetFilter()">Tout effacer</button>
                    <h2 class="rubrique_filtre">Tris :</h2>
                    <div class="filtre&tri">
                        <div class="divFiltres">
                            <h3 class="titre_filtre">Prix :</h3>
                            <div class="section_filtre">
                                <label>
                                    <input onclick="filtrerLogements()" type="radio" name="tri" value="croissant">
                                    Croissant
                                </label>
                                <label>
                                    <input onclick="filtrerLogements()" type="radio" name="tri" value="decroissant">
                                    Décroissant
                                </label>
                            </div>
                        </div>

                        <div class="divFiltres">
                            <h3 class="titre_filtre">Note :</h3>
                            <div class="section_filtre">
                                <label>
                                    <input onclick="filtrerLogements()" type="radio" name="tri" value="note-">
                                    Croissante
                                </label>
                                <label>
                                    <input onclick="filtrerLogements()" type="radio" name="tri" value="note+">
                                    Décroissante
                                </label>
                            </div>
                        </div>


                        <h2 class="rubrique_filtre">Filtres :</h2>
                        <section>
                            <div class="divFiltres">
                                <h3 class="titre_filtre">Prix :</h3>
                                <div class="section_filtre">

                                    <div class="price-input">
                                        <div class="field">
                                            <label>Min</label>
                                            <input type="number" class="input-min" value="0" name="prix_min">
                                            <label>Max</label>
                                            <input type="number" class="input-max" value="500" name="prix_max">
                                        </div>
                                    </div>
                                    <div class="slider">
                                        <div class="progress"></div>
                                    </div>
                                    <div class="range-input">
                                        <input type="range" class="range-min" min="0" max="500" value="0">
                                        <input type="range" class="range-max" min="0" max="500" value="500">

                                    </div>
                                </div>
                            </div>
                            <script src="slider.js"></script>
                        </section>

                        <section>
                            <div class="divFiltres">
                                <h3 class="titre_filtre">Personnes :</h3>
                                <div class="ligne_filtre">
                                    <label>Nombre</label>
                                    <input oninput="filtrerLogements()" type="number" name="pers_min">
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="divFiltres">
                                <h4 class="titre_filtre">Departement :</h4>
                                <div class="section_filtre">
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_cotedarmor">
                                        <label for="choix_cotedarmor">Cote d'Armor</label>
                                    </div>
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_finistere">
                                        <label for="choix_finistere">Finistère</label>
                                    </div>
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_illeetvilaine">
                                        <label for="choix_illeetvilaine">Ille et Vilaine</label>
                                    </div>
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_morbihan">
                                        <label for="choix_morbihan">Morbihan</label>
                                    </div>
                                </div>
                            </div>
                        </section>


                        <section>
                            <div class="divFiltres">
                                <h4 class="titre_filtre">Equipement :</h4>
                                <div class="section_filtre">
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_tv">
                                        <label for="choix_tv">TV</label>
                                    </div>
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_machine_a_laver">
                                        <label for="choix_machine_a_laver">Machine à laver</label>
                                    </div>
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_lave_vaiselle">
                                        <label for="choix_lave_vaiselle">Lave vaisselle</label>
                                    </div>
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_wifi">
                                        <label for="choix_wifi">Wifi</label>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="divFiltres">
                                <h4 class="titre_filtre">Service :</h4>
                                <div class="section_filtre">
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_linge">
                                        <label for="choix_linge">Linge</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_menage">
                                        <label for="choix_menage">Menage</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_transport">
                                        <label for="choix_transport">Transport</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_animaux">
                                        <label for="choix_animaux">Animaux</label>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="divFiltres">
                                <h4 class="titre_filtre">Installation :</h4>
                                <div class="section_filtre">
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_climatisation">
                                        <label for="choix_climatisation">Climatisation</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_piscine">
                                        <label for="choix_piscine">Piscine</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_jacuzzi">
                                        <label for="choix_jacuzzi">Jacuzzi</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_hammam">
                                        <label for="choix_hammam">Hammam</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_sauna">
                                        <label for="choix_sauna">Sauna</label>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="divFiltres">
                                <h4 class="titre_filtre">Aménagement :</h4>
                                <div class="section_filtre">
                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_jardin">
                                        <label for="choix_jardin">Jardin</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_balcon">
                                        <label for="choix_balcon">Balcon</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_terrasse">
                                        <label for="choix_terrasse">Terrasse</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_parking_prive">
                                        <label for="choix_parking_prive">Parking prive</label>
                                    </div>

                                    <div class="check_formfilter check_form_white">
                                        <input onclick="filtrerLogements()" class="taille_checkbox" type="checkbox"
                                            name="choix_parking_public">
                                        <label for="choix_parking_public">Parking public</label>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                </form>
                <a href="#" id="openBtnFiltres">
                    <div class="menuFiltres">
                        <p>×</p>
                    </div>
                </a>
            </div>

            <div id="mySidenavCarte" class="sidenavCarte">
                
                    <div id="map"></div>
                    <a href="#" id="openBtnCarte">
                        <div class="menuCarte">
                            <p>×</p>
                        </div>
                    </a>
                
            </div>

        </section>





    </main>

    <footer>
        <div class="footer">&copy ALHaIZ Breizh
            <a href="https://www.iubenda.com/privacy-policy/12300064"
                class="iubenda-white iubenda-noiframe iubenda-embed iubenda-noiframe "
                title="Politique de confidentialité ">Politique de confidentialité</a>
            <script type="text/javascript">
                (function (w, d) {
                    var loader = function () {
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