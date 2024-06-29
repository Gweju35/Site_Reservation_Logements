<?php
session_start();

// Détruire la session

session_destroy();



// Redirigez l'utilisateur vers la page de login ou toute autre page appropriée
header("Location: ../Profil/login.php"); // Remplacez "login.php" par la page vers laquelle vous souhaitez rediriger après la déconnexion
exit();
?>
