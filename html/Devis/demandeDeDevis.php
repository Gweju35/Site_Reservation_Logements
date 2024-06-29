<?php
include('../../libs/connect_params.php');
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
    <link rel="stylesheet" href="../assets/pages_css/Devis/demandeDeDevis.css">
    <link rel="stylesheet" href="test.css">
    <script defer src="../assets/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

</head>
<body>

<!------------------HEADER------------------>

<?php
if ($_SESSION['user_type'] == 'client'){
    echo $_SESSION['headerClient'];
} else {
    echo $_SESSION['headerProprietaire'];
}
?>

<!------------------MAIN------------------>

<?php
    // Informations de connexion à la base de données
    
    try {
        //Connexion à la base de données avec PDO
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);              

        $sql = "SELECT service_linge, service_menage, service_transport, charges_taxe_sejour, logement_prix_nuit_base, 
        charges_linge, charges_menage, charges_transport, charges_animaux, charges_personne_sup, logement_personne_max, service_animaux_domestique, service_personne_sup FROM alhaiz_breizh._logement 
        WHERE logement_id = :logement_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':logement_id', $_SESSION['logement_id']);
        $stmt->execute();


        $logement = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($logement) {
            $service_linge = $logement['service_linge'];
            $service_menage = $logement['service_menage'];
            $service_navette = $logement['service_transport'];
            $service_animaux_domestique = $logement['service_animaux_domestique'];
            $service_personne_sup = $logement['service_personne_sup'];

            $charges_taxe_sejour = $logement['charges_taxe_sejour'];
            $logement_prix_nuit_base = $logement['logement_prix_nuit_base'];

            $prixLinge = $logement['charges_linge'];
            $prixMenage = $logement['charges_menage'];
            $prixNavette = $logement['charges_transport'];
            $prixAnimaux = $logement['charges_animaux'];
            $prixPersonneSup = $logement['charges_personne_sup'];

            $personneMax = $logement['logement_personne_max'];
        }

        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        $_SESSION['charges_taxe_sejour'] = $charges_taxe_sejour;
        $_SESSION['logement_prix_nuit_base'] = $logement_prix_nuit_base;
        $_SESSION['max_pers'] = $personneMax;
        $_SESSION['devis_statut'] = "En attente";
        $_SESSION['prixLinge'] = $logement['charges_linge'];
        $_SESSION['prixMenage'] = $logement['charges_menage'];
        $_SESSION['prixtransport'] = $logement['charges_transport'];

    } catch (PDOException $e) {
        echo "<p>Erreur de base de données : " . $e->getMessage() . "</p>";
    }
    ?>
    
<!---------------Début demande de devis--------------->
<main>
<!---------------Flèche de retour--------------->

<div class="formulaire">
<div class="conteneur_titre_back">   
            <a href="javascript:history.back()">
                <div class="cercle_retour"><img src="../assets/ressources/icons/left-arrow.svg" alt="icone back" /></div>
            </a>
        <div class="demande">
            <h2>Demande de Devis</h2>
    </div>
</div>
    <form method="post" enctype="multipart/form-data" action="../Devis/traitementDevis.php">

<!---------------Calendrier--------------->
<div class="sousForm">
<h3>Dates du Séjour</h3>
</div>
    <div class="contenuForm">
    <!-- <label for="dateArr">Date d'arrivée* :</label>
    <input type="date" id="dateArr" name="dateArr" required> -->
   
    <label>Date d'arrivée : <input type="text" id="dateArr" name ="dateArr"></label  >
    
    <script>
        $.ajax({
            url: 'traitement_date.php',
            method: 'POST',
            success: function(response) {
            try{  
                
                var first_deb = false;
                // Analyser la réponse JSON
                var result = JSON.parse(response);
                console.log(result);
                var date_arr_select = false;
                var date_selectionnee;
                var first_date;
                var dateArr;
                dateArr = $( "#dateArr" ).datepicker({
                    altField: "#dateArr",
                    closeText: 'Fermer',
                    prevText: 'Précédent',
                    nextText: 'Suivant',
                    currentText: 'Aujourd\'hui',
                    monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                    monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
                    dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
                    dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
                    dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                    weekHeader: 'Sem.',
                    dateFormat: 'dd/mm/yy',
                    minDate: +0,
                    onSelect: function(dateText, inst) {
                        // Récupérer la date sélectionnée par l'utilisateur
                        date_selectionnee = $("#dateArr").datepicker('getDate');
                        console.log("Date sélectionnée par l'utilisateur :", date_selectionnee);
                        date_arr_select = true;
                        first_deb = false;
                        first_date = null;
                        
                        var an,jour;

                        an = jQuery.datepicker.formatDate('yy-mm-dd',date_selectionnee);

                        dd= new Date(an);
                        jj=dd.getDate();

                        dd.setDate(jj+1)

                        jour = dd.toISOString().slice(0,10);
                        ff15 = new Date(jour);
                        console.log(ff15);

                        // Mettre à jour la propriété minDate du deuxième datepicker
                        $("#dateDep").datepicker('option', 'minDate', ff15);

                    },
                    beforeShowDay: function(date){
                        var string =jQuery.datepicker.formatDate('yy-mm-dd',date);
                        var ok = result.events.some(function(event){return event.start === string});
                        
                        var tablejour = [];

                        for (var i = 0; i < result.events.length; i++) {
                            var jour = new Date(result.events[i].start);
                            // Vérifiez si la date de l'événement est égale à la date actuelle
                            if (jour.toDateString() === date.toDateString()) {
                                tablejour.push(result.events[i]);
                            }
                        }

                        if(ok == true && (tablejour.length < 2 && tablejour[0].etat ==='fin')){
                            return [true,"",""];
                        }else{
                            return [!ok,"",""];
                        }
                          
                    },   
                }).val();


                var dateDep;
                var ancienok = false;

                dateDep = $( "#dateDep" ).datepicker({
                    altField: "#dateDep",
                    closeText: 'Fermer',
                    prevText: 'Précédent',
                    nextText: 'Suivant',
                    currentText: 'Aujourd\'hui',
                    monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                    monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
                    dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
                    dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
                    dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                    weekHeader: 'Sem.',
                    dateFormat: 'dd/mm/yy',
                    minDate: +0,
                    onSelect: function(dateText, inst) {
                        // Récupérer la date sélectionnée par l'utilisateur
                        var userSelectedDate = $("#dateDep").datepicker('getDate');
                        console.log("Date sélectionnée par l'utilisateur :", userSelectedDate);

                        // Mettre à jour la propriété minDate du deuxième datepicker
                        $("#dateArr").datepicker('option', 'maxDate', userSelectedDate);

                    },
                    beforeShowDay: function(date){
                        if(date < date_selectionnee){
                            return[false,"",""];
                        }else if( date_arr_select == true){
                            var string =jQuery.datepicker.formatDate('yy-mm-dd',date);
                            console.log(date_selectionnee.toDateString() + " con de date v2 " + date.toDateString());
                            if(first_deb == false && date_selectionnee.toDateString() < date.toDateString() ){
                                for (var i = 0; i < result.events.length; i++) {
                                    var jour = new Date(result.events[i].start);
                                    console.log(jour);
                                    // Vérifiez si la date de l'événement est égale à la date actuelle
                                    if (jour.toDateString() == date.toDateString() && result.events[i].etat == 'debut') {
                                        first_deb = true;
                                        first_date = result.events[i].start;
                                        console.log("ici " + first_date);
                                    }
                                }
                                if (first_deb == false){
                                    var ok = result.events.some(function(event){return event.start === string});
                                    if(ok == true){
                                        if(ancienok == false){
                                            ancienok = true;
                                            console.log("1 j'active " + date.toDateString());
                                            return [ok, "",""]; 
                                        }else{
                                            console.log("2 je désactive " + date.toDateString());
                                            return [!ok, "",""];
                                        }
                                    }else{
                                        ancienok = false;
                                        console.log("3 j'active " + date.toDateString());
                                        return [!ok, "",""];
                                    } 
                                }else{
                                    console.log("4 j'active " + date.toDateString());
                                    return [true, "",""];
                                }
                            }else{
                                console.log(string + "con de date " + first_date);
                                if(string > first_date){
                                    console.log("5 je désactive " + date.toDateString());
                                    return [false,"",""];
                                }else{
                                    var ok = result.events.some(function(event){return event.start === string});
                                    if(ok == true){
                                        if(ancienok == false){
                                            ancienok = true;
                                            console.log("6 j'active " + date.toDateString());
                                            return [ok, "",""]; 
                                        }else{
                                            console.log("7 je desactive " + date.toDateString());
                                            return [!ok, "",""];
                                        }
                                    }else{
                                        ancienok = false;
                                        console.log("8 j'active " + date.toDateString());
                                        return [!ok, "",""];
                                    } 
                                }
                            }
                        }else{
                            var string =jQuery.datepicker.formatDate('yy-mm-dd',date);
                            var ok = result.events.some(function(event){return event.start === string});
                            if(ok == true){
                                if(ancienok == false){
                                    ancienok = true;
                                    console.log("9 je met en active" + date.toDateString());
                                    return [ok, "",""]; 
                                }else{
                                    console.log("10 je met en desactive" + date.toDateString());
                                    return [!ok, "",""];
                                }
                            }else{
                                ancienok = false;
                                console.log("11 je met en active" + date.toDateString());
                                return [!ok, "",""];
                            } 
                        }
                    }
                }).val();               

            } catch (error) {
                console.error('Erreur lors de l\'analyse de la réponse JSON :', error);
            }
        },
        error: function(xhr, status, error) {
        console.error('Erreur AJAX :', status, error);
        }            
        });

        
        
    </script>
    <br>
<label>Date de départ : <input type="text" id="dateDep" name="dateDep"></label>
    <br>
</div>
<!---------------Nombre personne--------------->
<div class="sousForm">

    <h3>Modalités</h3>
</div>
    <div class="contenuForm">

    <label for="nbr_accueillis">Nombre de personnes* :</label>
    <!--<input type="text" id="nbr_accueillis" name="nbr_accueillis" pattern=\d{2} placeholder="Max 99" required>-->

    <select id="nbr_accueillis" name="nbr_accueillis" required>
        <?php
        for ($cpt=1; $cpt <= $personneMax; $cpt++) { ?>
            <option value="<?php echo $cpt ?>"><?php echo $cpt?></option>
        <?php } ?>
    </select>
    <br />

<!---------Nombre de personne sup------------->
    <?php if ($service_personne_sup == false) { ?>
        <label for="pers_en_plus">Nombre de personnes supplémentaires :</label>
            <?php echo "Personne supplémentaire non accpetée";
        } else { ?>
        <label for="pers_en_plus">Nombre de personnes supplémentaires :</label>
            <select id="pers_en_plus" name="pers_en_plus" required>
                <?php
                    for( $i = 0 ; $i <= $personneMax ;$i++){?>
                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php } ?>
            </select>
            <input type="hidden" name="prix_pers_sup" value="<?php echo $prixPersonneSup ?>" />
        <?php } ?>
    <br />
<!---------------Animaux--------------->
        <?php if ($service_animaux_domestique == false) { ?>
            <label for="nbr_animaux">Animaux :</label>
            
            <?php echo "Les animaux ne sont pas autorisés dans ce logement !";
        } else { ?>
            <label for="nbr_animaux">Animaux :</label>
            <select id="nbr_animaux" name="nbr_animaux" required>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6+</option>
                
            </select>

            <input type="hidden" name="prix_animaux" value="<?php echo $prixAnimaux ?>" />
            <?php } ?>

    
    <br />
</div>
<!---------------Service--------------->
<div class="sousForm">

    <?php
        if($service_linge==true ||$service_menage==true || $service_navette==true){
    ?>
                <h3>Services</h3>
        </div>
                <div class="contenuForm">

    <?php

        if($service_linge==true){
    ?>    
                <div class="check_form">
                <label for="linge">Linge :</label>
                <input type="checkbox" class="taille_checkbox" id="linge" name="linge" value="true" onclick="checkboxClickedLinge()"/>
                <input type="hidden" name="charges_linge" value="<?php echo $logement['charges_linge']; ?>" />
                <br />
                </div>

                
    <?php
        }
        if($service_menage==true){
    ?>
                <div class="check_form">
                <label for="menage">Ménage :</label>
                <input type="checkbox" class="taille_checkbox" id="menage" name="menage" value="true" onclick="checkboxClickedMenage()"/>
                <input type="hidden" name="charges_menage" value="<?php echo $logement['charges_menage']; ?>" />
                <br />
                </div>
                

    <?php  
        }
        if($service_navette==true){
    ?>
                <div class="check_form">
                <label for="navette">Navette :</label>
                <input type="checkbox" class="taille_checkbox" id="navette" name="navette" value="true" onclick="checkboxClickedNavette()"/>
                <input type="hidden" name="charges_transport" value="<?php echo $logement['charges_transport']; ?>" />
                <br />
                </div>
            
    
    <?php
        }}
    ?>



<!--Récupération des valeurs des checkbox-->
<script>
    function checkboxClickedLinge() {
        var checkBox = document.getElementById("navette");
        if (checkBox.checked == true) {
            $_SESSION['valeurNavette']=true;
        } else {
            $_SESSION['valeurNavette']=false;
        }
    }

    function checkboxClickedMenage() {
        var checkBox = document.getElementById("menage");
        if (checkBox.checked == true) {
            $_SESSION['valeurMenage']=true;
        } else {
            $_SESSION['valeurMenage']=false;
        }
    }

    function checkboxClickedNavette() {
        var checkBox = document.getElementById("linge");
        if (checkBox.checked == true) {
            $_SESSION['valeurLinge']=true;
        } else {
            $_SESSION['valeurLinge']=false;
        }
    }
</script>
</div>
<!-- -------------Envoi du formulaire--------------->

        <div class="popup" id="popup1">
            <div class="overlay"></div>
            <div class="content">
                <h1><b>Demande de devis envoyée</b></h1>
                <div class="bouton_confirmer">
                    <input type="submit" value="Continuer" class="button_form" id="confirmerBtn">
                </div>
            </div>
        </div>
        </form>
        <div class="center">
            <button onclick="submitForm()" class="button_form">Confirmer</button>
        </div>
    </form> 
    </div>
    <script>
        function submitForm() {
            var dateArr = document.getElementById("dateArr").value;
            var dateDep = document.getElementById("dateDep").value;

            if(dateArr === "") {
                alert("La date d'arrivée n'est pas sélectionnée");
                return; // Arrête la fonction ici si la date d'arrivée n'est pas sélectionnée
            }

            if(dateDep === "") {
                alert("La date de départ n'est pas sélectionnée");
                return; // Arrête la fonction ici si la date de départ n'est pas sélectionnée
            }

            // Si tout est correct, continuer avec l'envoi du formulaire
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
            document.getElementById("popup1").classList.add("active");
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
