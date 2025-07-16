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
    // Pagination and search
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id) {
        $sql = "SELECT * FROM products WHERE id=$id";
        $res = $conn->query($sql);
        $products = [];
        if ($row = $res->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode([
            'products' => $products,
            'total' => count($products),
            'page' => 1,
            'limit' => 1
        ]);
        exit();
    }
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
    $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
    $offset = ($page - 1) * $limit;
    $where = '';
    if ($search !== '') {
        $where = "WHERE product_name LIKE '%$search%' OR description LIKE '%$search%' OR category LIKE '%$search%'";
    }
    
    $total_sql = "SELECT COUNT(*) as total FROM products $where";
    $total_res = $conn->query($total_sql);
    $total = $total_res->fetch_assoc()['total'];
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            $where 
            ORDER BY p.id DESC LIMIT $limit OFFSET $offset";
    $res = $conn->query($sql);
    $products = [];
    while ($row = $res->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode([
        'products' => $products,
        'total' => (int)$total,
        'page' => $page,
        'limit' => $limit
    ]);
    exit();
}

if ($method === 'POST') {
    try {
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : 0;

        $product_name = sanitize($_POST['product_name'] ?? '');
        $price = sanitize($_POST['price'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $category_id = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
        $weight = sanitize($_POST['weight'] ?? '');
        $country = sanitize($_POST['country'] ?? '');
        $media = '';

        // Handle file upload
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            validateFile($_FILES['media']);
            
            $ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));
            $filename = uniqid('media_') . '.' . $ext;
            $target = UPLOAD_DIR . $filename;
            
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['media']['tmp_name'], $target)) {
                $media = $target;
            } else {
                throw new Exception('Erreur lors du téléchargement du fichier');
            }
        }

        if ($id > 0) {
            // Update product
            $updateMediaSQL = $media ? ", media='$media'" : '';
            $categorySQL = $category_id ? ", category_id = $category_id" : ", category_id = NULL";
            $sql = "UPDATE products SET product_name='$product_name', price='$price', description='$description', weight='$weight', country='$country' $updateMediaSQL $categorySQL WHERE id=$id";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'updated' => true, 'message' => 'Produit mis à jour avec succès']);
            } else {
                throw new Exception('Erreur lors de la mise à jour: ' . $conn->error);
            }
        } else {
            // Insert product
            $categorySQL = $category_id ? ", category_id" : "";
            $categoryValue = $category_id ? ", $category_id" : "";
            $sql = "INSERT INTO products (product_name, price, media, description, weight, country $categorySQL) 
                    VALUES ('$product_name', '$price', '$media', '$description', '$weight', '$country' $categoryValue)";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'id' => $conn->insert_id, 'message' => 'Produit ajouté avec succès']);
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
        // Parse input
        parse_str(file_get_contents('php://input'), $_PUT);
        $id = (int)($_PUT['id'] ?? 0);
        $product_name = sanitize($_PUT['product_name'] ?? '');
        $price = sanitize($_PUT['price'] ?? '');
        $description = sanitize($_PUT['description'] ?? '');
        $category = sanitize($_PUT['category'] ?? '');
        $weight = sanitize($_PUT['weight'] ?? '');
        $country = sanitize($_PUT['country'] ?? '');
        
        $sql = "UPDATE products SET product_name='$product_name', price='$price', description='$description', category='$category', weight='$weight', country='$country' WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Produit mis à jour avec succès']);
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
        
        // Get media file to delete
        $sql = "SELECT media FROM products WHERE id=$id";
        $result = $conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            if ($row['media'] && file_exists($row['media'])) {
                unlink($row['media']);
            }
        }
        
        $sql = "DELETE FROM products WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Produit supprimé avec succès']);
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