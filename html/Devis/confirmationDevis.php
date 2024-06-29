<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/Devis/confirmationDevis.css">
    <script defer src="../assets/index.js"></script>
    <title>Alhaiz Breizh</title>
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">
</head>
<body>
<!------------------HEADER------------------>

<?php
if (isset($_SESSION['user_type']) == false){
    echo $_SESSION['headerVisiteur'];
} else if ($_SESSION['user_type'] == 'client'){
    echo $_SESSION['headerClient'];
} else {
    echo $_SESSION['headerProprietaire'];
}
?>

<!------------------MAIN------------------>
<main><div class="conteneur">
    <div class="message">
        <h2 class="confirmation">Demande de devis confirmée</h2>
        <p>Votre demande de devis a été confirmée avec succès. </p>
        <p>Nous vous remercions pour votre confiance.</p>
        <a href="../Logements/afficher_logement.php?logement_id=<?php echo $_SESSION['logement_id']?>">
            <button class="button_form">Retour sur le logement</button>
        </a>
    </div>
    </div>
</main>

    <footer>
        <div class="footer">&copy ALHaIZ Breizh</div>
    </footer>
</body>
</html>
