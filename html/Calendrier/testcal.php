<?php
session_start();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alhaiz Breizh</title>
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">

    <link rel="stylesheet" href="../assets/main.css">
   
    <script src='https://cdnjs.cloudflare.com/ajax/libs/bacon.js/3.0.17/Bacon.min.js'></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.9/index.global.min.js"></script>

    
    <script src="./script.js"></script>
     
    <!-- <script src="../assets/index.js"></script> -->
    
</head>
<body>

<!------------------HEADER------------------>

<?php
if (isset($_SESSION['user_type']) == false){
    header("Location: ../Profil/login.php");
    exit();
} else if ($_SESSION['user_type'] == 'client'){
    echo $_SESSION['headerClient'];
} else {
    echo $_SESSION['headerProprietaire'];
}
?>
<main>
    <h1>Test Calendrier </h1>
    <button id="btnAddIndispo">Ajouter Indisponibilité</button>
    <button id="btnSupprimer">Supprimer Indisponibilité</button>
    <div id="calendar"></div>
    <!-- Ajouter le bouton -->
    
</main>


</body>

</html>