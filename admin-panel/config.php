<?php
// Configuration du panel admin
define('ADMIN_TITLE', 'Panel Admin - Gestion Boutique');
define('SHOP_URL', 'https://roaring-daffodil-97228d.netlify.app');

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce');

// Configuration des uploads
define('UPLOAD_DIR', '../uploads/');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg']);

// Configuration de sécurité
define('SESSION_TIMEOUT', 3600); // 1 heure
define('ADMIN_SESSION_KEY', 'admin_username');

// Fonction de connexion à la base de données
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception('Erreur de connexion à la base de données: ' . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Fonction de validation de session admin
function validateAdminSession() {
    session_start();
    if (!isset($_SESSION[ADMIN_SESSION_KEY])) {
        header('Location: signin.html');
        exit();
    }
    return $_SESSION[ADMIN_SESSION_KEY];
}

// Fonction de nettoyage des données
function sanitize($str) {
    $conn = getDBConnection();
    return $conn->real_escape_string(trim($str));
}

// Fonction de validation des fichiers
function validateFile($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erreur lors du téléchargement du fichier');
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('Fichier trop volumineux (max ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)');
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        throw new Exception('Type de fichier non autorisé');
    }
    
    return true;
}
?>