<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

try {
    $conn = getDBConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id) {
        // Get specific category
        $sql = "SELECT * FROM categories WHERE id = $id";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $category = $result->fetch_assoc();
            echo json_encode(['category' => $category]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Catégorie non trouvée']);
        }
    } else {
        // Get all categories with product count
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id 
                GROUP BY c.id 
                ORDER BY c.name ASC";
        $result = $conn->query($sql);
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        echo json_encode(['categories' => $categories]);
    }
    exit();
}

if ($method === 'POST') {
    try {
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : 0;
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;
        $sort_order = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($name)) {
            throw new Exception('Le nom de la catégorie est requis');
        }
        
        // Check if category name already exists
        $check_sql = "SELECT id FROM categories WHERE name = '$name'";
        if ($id > 0) {
            $check_sql .= " AND id != $id";
        }
        $check_result = $conn->query($check_sql);
        if ($check_result->num_rows > 0) {
            throw new Exception('Une catégorie avec ce nom existe déjà');
        }
        
        if ($id > 0) {
            // Update category
            $parent_sql = $parent_id ? ", parent_id = $parent_id" : ", parent_id = NULL";
            $sql = "UPDATE categories SET name = '$name', description = '$description', sort_order = $sort_order, is_active = $is_active $parent_sql WHERE id = $id";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => 'Catégorie mise à jour avec succès']);
            } else {
                throw new Exception('Erreur lors de la mise à jour: ' . $conn->error);
            }
        } else {
            // Insert category
            $parent_sql = $parent_id ? ", parent_id" : "";
            $parent_value = $parent_id ? ", $parent_id" : "";
            $sql = "INSERT INTO categories (name, description, sort_order, is_active $parent_sql) 
                    VALUES ('$name', '$description', $sort_order, $is_active $parent_value)";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'id' => $conn->insert_id, 'message' => 'Catégorie ajoutée avec succès']);
            } else {
                throw new Exception('Erreur lors de l\'ajout: ' . $conn->error);
            }
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

if ($method === 'PUT') {
    try {
        parse_str(file_get_contents('php://input'), $_PUT);
        $id = (int)($_PUT['id'] ?? 0);
        $name = sanitize($_PUT['name'] ?? '');
        $description = sanitize($_PUT['description'] ?? '');
        $parent_id = isset($_PUT['parent_id']) && $_PUT['parent_id'] !== '' ? (int)$_PUT['parent_id'] : null;
        $sort_order = isset($_PUT['sort_order']) ? (int)$_PUT['sort_order'] : 0;
        $is_active = isset($_PUT['is_active']) ? 1 : 0;
        
        if (!$id || empty($name)) {
            throw new Exception('ID et nom de catégorie requis');
        }
        
        $parent_sql = $parent_id ? ", parent_id = $parent_id" : ", parent_id = NULL";
        $sql = "UPDATE categories SET name = '$name', description = '$description', sort_order = $sort_order, is_active = $is_active $parent_sql WHERE id = $id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Catégorie mise à jour avec succès']);
        } else {
            throw new Exception('Erreur lors de la mise à jour: ' . $conn->error);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

if ($method === 'DELETE') {
    try {
        parse_str(file_get_contents('php://input'), $_DELETE);
        $id = (int)($_DELETE['id'] ?? 0);
        
        if (!$id) {
            throw new Exception('ID de catégorie requis');
        }
        
        // Check if category has products
        $check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = $id";
        $check_result = $conn->query($check_sql);
        $product_count = $check_result->fetch_assoc()['count'];
        
        if ($product_count > 0) {
            throw new Exception("Impossible de supprimer cette catégorie car elle contient $product_count produit(s)");
        }
        
        // Check if category has subcategories
        $check_sql = "SELECT COUNT(*) as count FROM categories WHERE parent_id = $id";
        $check_result = $conn->query($check_sql);
        $subcategory_count = $check_result->fetch_assoc()['count'];
        
        if ($subcategory_count > 0) {
            throw new Exception("Impossible de supprimer cette catégorie car elle contient $subcategory_count sous-catégorie(s)");
        }
        
        $sql = "DELETE FROM categories WHERE id = $id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Catégorie supprimée avec succès']);
        } else {
            throw new Exception('Erreur lors de la suppression: ' . $conn->error);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

// OPTIONS preflight
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
exit();