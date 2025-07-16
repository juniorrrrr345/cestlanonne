<?php
session_start();
require_once 'config.php';

// Logger l'activité de déconnexion si un utilisateur est connecté
if (isset($_SESSION['admin_id'])) {
    try {
        $conn = getDBConnection();
        $user_id = (int)$_SESSION['admin_id'];
        
        // Logger l'activité
        $sql = "INSERT INTO activity_logs (user_id, action, table_name, record_id, ip_address) 
                VALUES (?, 'logout', 'admin_users', ?, ?)";
        $stmt = $conn->prepare($sql);
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt->bind_param('iis', $user_id, $user_id, $ip_address);
        $stmt->execute();
    } catch (Exception $e) {
        // Log silencieux en cas d'erreur
        error_log("Erreur lors du logging de déconnexion: " . $e->getMessage());
    }
}

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire le cookie de session si il existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header('Location: signin.html');
exit();
?> 