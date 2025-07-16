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

// Fonction de validation des données
function validateProductData($data) {
    $errors = [];
    
    if (empty($data['product_name'])) {
        $errors[] = 'Le nom du produit est requis';
    }
    
    if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
        $errors[] = 'Le prix doit être un nombre positif';
    }
    
    if (empty($data['description'])) {
        $errors[] = 'La description est requise';
    }
    
    return $errors;
}

// Gestion des requêtes GET
if ($method === 'GET') {
    // Récupération d'un produit spécifique
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = (int)$_GET['id'];
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = $id";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc();
            sendResponse(true, ['products' => [$product]]);
        } else {
            sendResponse(false, null, 'Produit non trouvé');
        }
    }
    
    // Récupération des statistiques
    if ($action === 'get_stats') {
        try {
            // Statistiques des produits
            $sql = "SELECT COUNT(*) as total_products FROM products";
            $result = $conn->query($sql);
            $total_products = $result->fetch_assoc()['total_products'];
            
            // Statistiques des commandes (simulation)
            $total_orders = 0;
            $total_revenue = 0;
            
            // Statistiques des clients (simulation)
            $total_customers = 0;
            
            sendResponse(true, [
                'stats' => [
                    'total_products' => $total_products,
                    'total_orders' => $total_orders,
                    'total_customers' => $total_customers,
                    'total_revenue' => $total_revenue
                ]
            ]);
        } catch (Exception $e) {
            sendResponse(false, null, 'Erreur lors du calcul des statistiques: ' . $e->getMessage());
        }
    }
    
    // Récupération de l'activité récente
    if ($action === 'get_recent_activity') {
        try {
            $sql = "SELECT p.product_name, p.created_at, 'product_added' as type 
                    FROM products p 
                    ORDER BY p.created_at DESC 
                    LIMIT 5";
            $result = $conn->query($sql);
            
            $activities = [];
            while ($row = $result->fetch_assoc()) {
                $activities[] = [
                    'title' => 'Nouveau produit ajouté',
                    'description' => 'Produit "' . $row['product_name'] . '" ajouté au catalogue',
                    'time' => date('d/m/Y H:i', strtotime($row['created_at'])),
                    'icon' => 'fa-plus-circle',
                    'color' => 'success'
                ];
            }
            
            sendResponse(true, ['activities' => $activities]);
        } catch (Exception $e) {
            sendResponse(false, null, 'Erreur lors du chargement de l\'activité récente');
        }
    }
    
    // Récupération de tous les produits (avec pagination et recherche)
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 15;
    $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
    $offset = ($page - 1) * $limit;
    
    try {
        $where_conditions = [];
        $params = [];
        
        if (!empty($search)) {
            $where_conditions[] = "(p.product_name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
            $search_param = "%$search%";
            $params = [$search_param, $search_param, $search_param];
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' OR ', $where_conditions) : '';
        
        // Compter le total
        $count_sql = "SELECT COUNT(*) as total FROM products p LEFT JOIN categories c ON p.category_id = c.id $where_clause";
        $stmt = $conn->prepare($count_sql);
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }
        $stmt->execute();
        $total_result = $stmt->get_result();
        $total = $total_result->fetch_assoc()['total'];
        
        // Récupérer les produits
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                $where_clause 
                ORDER BY p.id DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)) . 'ii', ...$params, $limit, $offset);
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        sendResponse(true, [
            'products' => $products,
            'total' => (int)$total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ]);
        
    } catch (Exception $e) {
        sendResponse(false, null, 'Erreur lors de la récupération des produits: ' . $e->getMessage());
    }
}

// Gestion des requêtes POST (ajout/modification)
if ($method === 'POST') {
    try {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        
        // Validation des données
        $product_data = [
            'product_name' => $_POST['product_name'] ?? '',
            'price' => $_POST['price'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category_id' => isset($_POST['category_id']) && !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'weight' => $_POST['weight'] ?? '',
            'country' => $_POST['country'] ?? '',
            'stock' => isset($_POST['stock']) ? (int)$_POST['stock'] : 0
        ];
        
        $validation_errors = validateProductData($product_data);
        if (!empty($validation_errors)) {
            sendResponse(false, null, 'Erreurs de validation: ' . implode(', ', $validation_errors));
        }
        
        // Traitement du fichier média
        $media_path = '';
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            try {
                validateFile($_FILES['media']);
                
                $ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));
                $filename = uniqid('media_') . '.' . $ext;
                $target = UPLOAD_DIR . $filename;
                
                if (!is_dir(UPLOAD_DIR)) {
                    mkdir(UPLOAD_DIR, 0777, true);
                }
                
                if (move_uploaded_file($_FILES['media']['tmp_name'], $target)) {
                    $media_path = $target;
                } else {
                    throw new Exception('Erreur lors du téléchargement du fichier');
                }
            } catch (Exception $e) {
                sendResponse(false, null, 'Erreur lors du traitement du fichier: ' . $e->getMessage());
            }
        }
        
        if ($id > 0) {
            // Mise à jour du produit
            $update_fields = [
                "product_name = ?",
                "price = ?",
                "description = ?",
                "weight = ?",
                "country = ?",
                "stock = ?"
            ];
            
            $params = [
                $product_data['product_name'],
                $product_data['price'],
                $product_data['description'],
                $product_data['weight'],
                $product_data['country'],
                $product_data['stock']
            ];
            
            if ($product_data['category_id']) {
                $update_fields[] = "category_id = ?";
                $params[] = $product_data['category_id'];
            } else {
                $update_fields[] = "category_id = NULL";
            }
            
            if ($media_path) {
                $update_fields[] = "media = ?";
                $params[] = $media_path;
            }
            
            $sql = "UPDATE products SET " . implode(', ', $update_fields) . " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            
            if ($stmt->execute()) {
                sendResponse(true, null, 'Produit mis à jour avec succès');
            } else {
                throw new Exception('Erreur lors de la mise à jour: ' . $stmt->error);
            }
        } else {
            // Ajout d'un nouveau produit
            $sql = "INSERT INTO products (product_name, price, media, description, category_id, weight, country, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sdsssssi', 
                $product_data['product_name'],
                $product_data['price'],
                $media_path,
                $product_data['description'],
                $product_data['category_id'],
                $product_data['weight'],
                $product_data['country'],
                $product_data['stock']
            );
            
            if ($stmt->execute()) {
                sendResponse(true, ['id' => $conn->insert_id], 'Produit ajouté avec succès');
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
            sendResponse(false, null, 'ID de produit invalide');
        }
        
        // Récupérer le fichier média avant suppression
        $sql = "SELECT media FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Supprimer le fichier média s'il existe
            if ($row['media'] && file_exists($row['media'])) {
                unlink($row['media']);
            }
        }
        
        // Supprimer le produit
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            sendResponse(true, null, 'Produit supprimé avec succès');
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