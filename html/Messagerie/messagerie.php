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
     
    <script defer src="../assets/index.js"></script>
    
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



