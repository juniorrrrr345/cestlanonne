<?php
require_once 'auth.php';

// Effectuer la déconnexion
$result = logout();

// Rediriger vers la page de connexion
header('Location: signin.html');
exit();
?> 