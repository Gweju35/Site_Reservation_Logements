<?php
session_start();
// Connexion à la base de données
include ('../../libs/connect_params.php');

try {
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les logements du propriétaire
    $sql = "SELECT logement_id,logement_accroche FROM alhaiz_breizh._logement WHERE logement_compte_id_proprio = :id_compte_proprio";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_compte_proprio', $_SESSION['user_id']);
    $stmt->execute();

    $logements = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}

?>



    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Contient un calendrier connecté">
        <title>Alhaiz Breizh</title>
        <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">

        <link rel="stylesheet" href="../assets/main.css">

        <script src='https://cdnjs.cloudflare.com/ajax/libs/bacon.js/3.0.17/Bacon.min.js'></script>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.9/index.global.min.js"></script>


        <script defer src="./script.js"></script>


        <!-- jQuery Modal -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

    <link rel="stylesheet" href="../assets/pages_css/Calendrier/calendrier.css">

    <!-- <script src="../assets/index.js"></script> -->

    <script defer src="./abo_modal.js"></script>

</head>

<body>

    <!------------------HEADER------------------>

    <?php
    if (isset ($_SESSION['user_type']) == false) {
        header("Location: ../Profil/login.php");
        exit();
    } else if ($_SESSION['user_type'] == 'client') {
        echo $_SESSION['headerClient'];
    } else {
        echo $_SESSION['headerProprietaire'];
    }
    $logementAccroche = '';
    ?>
    <main>
        <div class="all_thing_upper">
            <div class="choix_logement">
                <label for="choixLogement">Choisissez le Calendrier d'un logement :</label>
                <select id="choixLogement" name="choixLogement">

                    <?php

                    if ($logements) {
                        foreach ($logements as $logement) { ?>
                            <option value="<?php echo $logement['logement_id'] ?>">
                                <?php echo $logement['logement_accroche'] ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>

                <div id="ex1" class="modal">
                    <a href="#" rel="modal:close">
                        <button id="btnAddIndispo" class="button_form">Ajouter Indisponibilité</button>
                    </a>
                    <a href="#" rel="modal:close">
                        <button id="btnSupprimer" class="button_form">Supprimer Indisponibilité</button>
                    </a>

                    <a href="#" rel="modal:close">
                        <button id="btnAddPeriode" class="button_form">Ajouter une période prix</button>
                    </a>

                    <a href="#" rel="modal:close">
                        <button id="btnSupprimerPeriode" class="button_form">Supprimer une période prix</button>
                    </a>


                </div>
                <div id="ex2" class="modal">
                    <h2>Génerer votre abonnement au calendrier</h2>
                    <form id="formulaire_abo" onsubmit="return validerFormulaire()">

                        <select id="choixLogement_abo" name="choixLogement_abo">

                            <?php

                            if ($logements) {
                                foreach ($logements as $logement) { ?>
                                    <option value="<?php echo $logement['logement_id'] ?>">
                                        <?php echo $logement['logement_accroche'] ?>
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <br>
                        <label for="date_debut">Date de début :</label>
                        <input type="date" id="date_debut" name="date_debut" required>
                        <br>
                        <label for="date_fin">Date de fin :</label>
                        <input type="date" id="date_fin" name="date_fin" required>
                        <br>

                        <h3>Type d'événements :</h3>

                        <div class="check_form">
                            <input class="taille_checkbox" type="checkbox" id="checkbox1" name="checkbox1" value="reservation">
                            <label for="checkbox1">Réservation</label>
                        </div>
                        <br>

                        <div class="check_form">
                            <input class="taille_checkbox" type="checkbox" id="checkbox2" name="checkbox2" value="devis">
                            <label for="checkbox2">Devis</label>
                        </div>
                        <br>

                        <div class="check_form">
                            <input class="taille_checkbox" type="checkbox" id="checkbox3" name="checkbox3" value="raison_perso">
                            <label for="checkbox3">Raison personnelle</label>
                        </div>

                        <br>

                        <input type="submit" id="envoyer" value="Créer" class="button_form">
                    </form>

                </div>

            </div>
            <div class="box_btn">
                <a href="#ex1" rel="modal:open">
                    <button class="button_form">Modification</button>
                </a>

                <a href="../API/gestion_cles_calendrier.php">
                    <button class="button_form">Liste abonnements</button>
                </a>
                
                <a href="#ex2" rel="modal:open">
                    <button class="button_form" id="dl_calendar">
                        <div class="div_btn">
                            <svg class="icon_btn_calendar_dl" fill="#F5E3D3" width="2.5em" height="2.5em"
                                viewBox="0 0 32 32" version="1.1">
                                <title>abonnement_calendrier</title>
                                <path
                                    d="M28 4.75h-0.75v-2.75c0-0.69-0.56-1.25-1.25-1.25s-1.25 0.56-1.25 1.25v0 2.75h-17.5v-2.75c0-0.69-0.56-1.25-1.25-1.25s-1.25 0.56-1.25 1.25v0 2.75h-0.75c-1.794 0.002-3.248 1.456-3.25 3.25v19.998c0.002 1.794 1.456 3.248 3.25 3.25h24c1.794-0.001 3.249-1.456 3.25-3.25v-19.998c-0.002-1.794-1.456-3.248-3.25-3.25h-0zM4 7.25h24c0.414 0 0.75 0.336 0.75 0.75v2.75h-25.5v-2.75c0.001-0.414 0.336-0.749 0.75-0.75h0zM28 28.748h-24c-0.414-0-0.75-0.336-0.75-0.75v-14.748h25.5v14.748c-0 0.414-0.336 0.75-0.75 0.75v0zM20.262 19.957h-3.012v-3.012c0-0.69-0.56-1.25-1.25-1.25s-1.25 0.56-1.25 1.25v0 3.012h-3.013c-0.69 0-1.25 0.56-1.25 1.25s0.56 1.25 1.25 1.25v0h3.013v3.014c0 0.69 0.56 1.25 1.25 1.25s1.25-0.56 1.25-1.25v0-3.014h3.012c0.69 0 1.25-0.56 1.25-1.25s-0.56-1.25-1.25-1.25v0z" />
                            </svg>

                        </div>


                    </button>
                </a>

            </div>
        </div>





        <div id="calendar"></div>
        <!-- Ajouter le bouton -->

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