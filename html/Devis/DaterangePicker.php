<?php
include('../../libs/connect_params.php');
session_start();
?>

<?php
    // Informations de connexion à la base de données
    
    try {
        //Connexion à la base de données avec PDO
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);              

        $sql = "SELECT ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':logement_id', $_SESSION['logement_id']);
        $stmt->execute();

    } catch (PDOException $e) {
        echo "<p>Erreur de base de données : " . $e->getMessage() . "</p>";
    }
?>

<head>
    <title>Alhaiz Breizh</title>
    <link rel="icon" type="image/x-icon" href="./assets/ressources/images/logo.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/main.css">
    <link rel="stylesheet" href="../assets/pages_css/Devis/demandeDeDevis.css">
    <script src="../assets/index.js"></script>


   
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />




</head>
<body>
<label for="dateArr">Date d'arrivée* :</label>
<input type="text" id="dateArr" name="dateArr" required>
<br>


<label for="dateDep">Date de départ* :</label>
<input type="text" id="dateDep" name="dateDep" required>
<br>

<script>
    $(function () {
        const indisponibilites = <?php echo $indisponibilites_json ?>;

        const dateArrInput = $('input[name="dateArr"]');
        const dateDepInput = $('input[name="dateDep"]');
        dateArrInput.daterangepicker({
            singleDatePicker: true,
            locale: {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Appliquer",
                "cancelLabel": "Annuler",
                "fromLabel": "De",
                "toLabel": "à",
                "customRangeLabel": "Personnalisé",
                "weekLabel": "S",
                "daysOfWeek": ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
                "monthNames": [
                    "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
                    "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
                ],
                "firstDay": 1
            },
            minDate: moment(),
            isInvalidDate: function (date) {
                // Vérifier si la date est indisponible
                return indisponibilites.some(indispo =>
                    date.isBetween(moment(indispo.start), moment(indispo.end), null, '[]')
                );
            }
        });

        dateDepInput.daterangepicker({
            singleDatePicker: true,
            locale: {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Appliquer",
                "cancelLabel": "Annuler",
                "fromLabel": "De",
                "toLabel": "à",
                "customRangeLabel": "Personnalisé",
                "weekLabel": "S",
                "daysOfWeek": ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
                "monthNames": [
                    "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
                    "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
                ],
                "firstDay": 1
            },
            minDate: moment(),
            isInvalidDate: function (date) {
                // Vérifier si la date est indisponible
                return indisponibilites.some(indispo =>
                    date.isBetween(moment(indispo.start), moment(indispo.end), null, '[]')
                );
            }
        });



        // Attacher la fonction de vérification aux événements de changement pour les deux inputs
        dateArrInput.on('apply.daterangepicker', checkDates);
        dateDepInput.on('apply.daterangepicker', checkDates);
    });

</script>

</body>

