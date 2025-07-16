<?php
require_once 'config.php';

session_start();

// Fonction de connexion
function login($username, $password) {
    try {
        $conn = getDBConnection();
        $username = sanitize($username);
        
        $sql = "SELECT id, username, password, role, is_active FROM admin_users WHERE username = '$username' AND is_active = 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Créer la session
                $_SESSION[ADMIN_SESSION_KEY] = $user['username'];
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['login_time'] = time();
                
                // Mettre à jour la dernière connexion
                $update_sql = "UPDATE admin_users SET last_login = NOW() WHERE id = " . $user['id'];
                $conn->query($update_sql);
                
                // Logger l'activité
                logActivity($user['id'], 'login', 'admin_users', $user['id'], null, ['username' => $username]);
                
                return ['success' => true, 'user' => $user];
            }
        }
        
        return ['success' => false, 'message' => 'Nom d\'utilisateur ou mot de passe incorrect'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Erreur de connexion: ' . $e->getMessage()];
    }
}

// Fonction de déconnexion
function logout() {
    if (isset($_SESSION['admin_id'])) {
        logActivity($_SESSION['admin_id'], 'logout', 'admin_users', $_SESSION['admin_id'], null, null);
    }
    
    session_destroy();
    return ['success' => true, 'message' => 'Déconnexion réussie'];
}

// Fonction de vérification de session
function checkSession() {
    if (!isset($_SESSION[ADMIN_SESSION_KEY]) || !isset($_SESSION['login_time'])) {
        return false;
    }
    
    // Vérifier le timeout de session
    if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
        logout();
        return false;
    }
    
    // Mettre à jour le temps de connexion
    $_SESSION['login_time'] = time();
    
    return true;
}

// Fonction de logging d'activité
function logActivity($user_id, $action, $table_name = null, $record_id = null, $old_values = null, $new_values = null) {
    try {
        $conn = getDBConnection();
        
        $user_id = (int)$user_id;
        $action = sanitize($action);
        $table_name = $table_name ? sanitize($table_name) : 'NULL';
        $record_id = $record_id ? (int)$record_id : 'NULL';
        $old_values_json = $old_values ? "'" . $conn->real_escape_string(json_encode($old_values)) . "'" : 'NULL';
        $new_values_json = $new_values ? "'" . $conn->real_escape_string(json_encode($new_values)) . "'" : 'NULL';
        $ip_address = sanitize($_SERVER['REMOTE_ADDR'] ?? '');
        
        $sql = "INSERT INTO activity_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address) 
                VALUES ($user_id, '$action', $table_name, $record_id, $old_values_json, $new_values_json, '$ip_address')";
        
        $conn->query($sql);
    } catch (Exception $e) {
        // Log silencieux en cas d'erreur
        error_log("Erreur de logging: " . $e->getMessage());
    }
}

// Gestion des requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'login':
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
                exit();
            }
            
            $result = login($username, $password);
            echo json_encode($result);
            break;
            
        case 'logout':
            $result = logout();
            echo json_encode($result);
            break;
            
        case 'check_session':
            $is_valid = checkSession();
            echo json_encode(['valid' => $is_valid]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
    exit();
}

// Redirection si déjà connecté
if (checkSession()) {
    header('Location: index.php');
    exit();
}
?>