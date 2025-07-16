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
    $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
    
    if ($product_id) {
        $sql = "SELECT * FROM product_prices WHERE product_id = $product_id ORDER BY currency, price_type";
        $result = $conn->query($sql);
        $prices = [];
        while ($row = $result->fetch_assoc()) {
            $prices[] = $row;
        }
        echo json_encode(['prices' => $prices]);
    } else {
        // Get all prices with product info
        $sql = "SELECT pp.*, p.product_name FROM product_prices pp 
                JOIN products p ON pp.product_id = p.id 
                ORDER BY p.product_name, pp.currency, pp.price_type";
        $result = $conn->query($sql);
        $prices = [];
        while ($row = $result->fetch_assoc()) {
            $prices[] = $row;
        }
        echo json_encode(['prices' => $prices]);
    }
    exit();
}

if ($method === 'POST') {
    try {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $price = sanitize($_POST['price'] ?? '');
        $currency = sanitize($_POST['currency'] ?? 'EUR');
        $price_type = sanitize($_POST['price_type'] ?? 'regular'); // regular, sale, wholesale
        $valid_from = sanitize($_POST['valid_from'] ?? '');
        $valid_until = sanitize($_POST['valid_until'] ?? '');
        
        if (!$product_id || !$price) {
            throw new Exception('Product ID and price are required');
        }
        
        // Check if price already exists for this product, currency and type
        $check_sql = "SELECT id FROM product_prices WHERE product_id = $product_id AND currency = '$currency' AND price_type = '$price_type'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            // Update existing price
            $row = $check_result->fetch_assoc();
            $price_id = $row['id'];
            $sql = "UPDATE product_prices SET price = '$price', valid_from = " . 
                   ($valid_from ? "'$valid_from'" : "NULL") . ", valid_until = " . 
                   ($valid_until ? "'$valid_until'" : "NULL") . " WHERE id = $price_id";
        } else {
            // Insert new price
            $sql = "INSERT INTO product_prices (product_id, price, currency, price_type, valid_from, valid_until) 
                    VALUES ($product_id, '$price', '$currency', '$price_type', " . 
                    ($valid_from ? "'$valid_from'" : "NULL") . ", " . 
                    ($valid_until ? "'$valid_until'" : "NULL") . ")";
        }
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Prix ajouté/mis à jour avec succès']);
        } else {
            throw new Exception('Erreur lors de la sauvegarde: ' . $conn->error);
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
        $price_id = (int)($_DELETE['id'] ?? 0);
        
        if (!$price_id) {
            throw new Exception('Price ID is required');
        }
        
        $sql = "DELETE FROM product_prices WHERE id = $price_id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Prix supprimé avec succès']);
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