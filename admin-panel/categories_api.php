<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Gestion des requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $conn = getDBConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Fonction de réponse JSON
function sendResponse($success, $data = null, $message = '') {
    $response = ['success' => $success];
    if ($data !== null) $response = array_merge($response, $data);
    if ($message) $response['message'] = $message;
    echo json_encode($response);
    exit();
}

// Fonction de validation des données de catégorie
function validateCategoryData($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Le nom de la catégorie est requis';
    }
    
    if (strlen($data['name']) > 255) {
        $errors[] = 'Le nom de la catégorie ne peut pas dépasser 255 caractères';
    }
    
    return $errors;
}

// Gestion des requêtes GET
if ($method === 'GET') {
    // Récupération d'une catégorie spécifique
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = (int)$_GET['id'];
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $category = $result->fetch_assoc();
            sendResponse(true, ['categories' => [$category]]);
        } else {
            sendResponse(false, null, 'Catégorie non trouvée');
        }
    }
    
    // Récupération de toutes les catégories
    if ($action === 'get_all') {
        try {
            $sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
            $result = $conn->query($sql);
            
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            
            sendResponse(true, ['categories' => $categories]);
        } catch (Exception $e) {
            sendResponse(false, null, 'Erreur lors de la récupération des catégories: ' . $e->getMessage());
        }
    }
    
    // Récupération des catégories avec pagination
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 15;
    $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
    $offset = ($page - 1) * $limit;
    
    try {
        $where_conditions = ['is_active = 1'];
        $params = [];
        
        if (!empty($search)) {
            $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
            $search_param = "%$search%";
            $params = [$search_param, $search_param];
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Compter le total
        $count_sql = "SELECT COUNT(*) as total FROM categories $where_clause";
        $stmt = $conn->prepare($count_sql);
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }
        $stmt->execute();
        $total_result = $stmt->get_result();
        $total = $total_result->fetch_assoc()['total'];
        
        // Récupérer les catégories
        $sql = "SELECT * FROM categories $where_clause ORDER BY sort_order ASC, name ASC LIMIT ? OFFSET ?";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)) . 'ii', ...$params, $limit, $offset);
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        sendResponse(true, [
            'categories' => $categories,
            'total' => (int)$total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ]);
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Erreur lors de la récupération des catégories: ' . $e->getMessage());
    }
}

// Gestion des requêtes POST (ajout/modification)
if ($method === 'POST') {
    try {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        
        // Validation des données
        $category_data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'parent_id' => isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'sort_order' => isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0,
            'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1
        ];
        
        $validation_errors = validateCategoryData($category_data);
        if (!empty($validation_errors)) {
            sendResponse(false, null, 'Erreurs de validation: ' . implode(', ', $validation_errors));
        }
        
        if ($id > 0) {
            // Mise à jour de la catégorie
            $sql = "UPDATE categories SET name = ?, description = ?, parent_id = ?, sort_order = ?, is_active = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssiiii', 
                $category_data['name'],
                $category_data['description'],
                $category_data['parent_id'],
                $category_data['sort_order'],
                $category_data['is_active'],
                $id
            );
            
            if ($stmt->execute()) {
                sendResponse(true, null, 'Catégorie mise à jour avec succès');
            } else {
                throw new Exception('Erreur lors de la mise à jour: ' . $stmt->error);
            }
        } else {
            // Ajout d'une nouvelle catégorie
            $sql = "INSERT INTO categories (name, description, parent_id, sort_order, is_active) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssiii', 
                $category_data['name'],
                $category_data['description'],
                $category_data['parent_id'],
                $category_data['sort_order'],
                $category_data['is_active']
            );
            
            if ($stmt->execute()) {
                sendResponse(true, ['id' => $conn->insert_id], 'Catégorie ajoutée avec succès');
            } else {
                throw new Exception('Erreur lors de l\'ajout: ' . $stmt->error);
            }
        }
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Erreur: ' . $e->getMessage());
    }
}

// Gestion des requêtes DELETE
if ($method === 'DELETE') {
    try {
        parse_str(file_get_contents('php://input'), $_DELETE);
        $id = isset($_DELETE['id']) ? (int)$_DELETE['id'] : 0;
        
        if ($id <= 0) {
            sendResponse(false, null, 'ID de catégorie invalide');
        }
        
        // Vérifier si la catégorie est utilisée par des produits
        $check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        
        if ($count > 0) {
            sendResponse(false, null, 'Impossible de supprimer cette catégorie car elle est utilisée par ' . $count . ' produit(s)');
        }
        
        // Vérifier si la catégorie a des sous-catégories
        $check_children_sql = "SELECT COUNT(*) as count FROM categories WHERE parent_id = ?";
        $stmt = $conn->prepare($check_children_sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $children_count = $result->fetch_assoc()['count'];
        
        if ($children_count > 0) {
            sendResponse(false, null, 'Impossible de supprimer cette catégorie car elle a ' . $children_count . ' sous-catégorie(s)');
        }
        
        // Supprimer la catégorie
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            sendResponse(true, null, 'Catégorie supprimée avec succès');
        } else {
            throw new Exception('Erreur lors de la suppression: ' . $stmt->error);
        }
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Erreur lors de la suppression: ' . $e->getMessage());
    }
}

// Méthode non autorisée
http_response_code(405);
sendResponse(false, null, 'Méthode non autorisée');
?>