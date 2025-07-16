<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$host = 'localhost';
$user = 'root'; // Change if needed
$pass = '';
$db = 'ecommerce';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

function sanitize($str) {
    global $conn;
    return $conn->real_escape_string($str);
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
        $where = "WHERE product_name LIKE '%$search%'";
    }
    $total_sql = "SELECT COUNT(*) as total FROM products $where";
    $total_res = $conn->query($total_sql);
    $total = $total_res->fetch_assoc()['total'];
    $sql = "SELECT * FROM products $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
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
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : 0;

    $product_name = sanitize($_POST['product_name'] ?? '');
    $price = sanitize($_POST['price'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $weight = sanitize($_POST['weight'] ?? '');
    $country = sanitize($_POST['country'] ?? '');
    $media = '';

    // Handle file upload
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('media_') . '.' . $ext;
        $target = 'uploads/' . $filename;
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        move_uploaded_file($_FILES['media']['tmp_name'], $target);
        $media = $target;
    }

    if ($id > 0) {
        // Update product
        $updateMediaSQL = $media ? ", media='$media'" : '';
        $sql = "UPDATE products SET product_name='$product_name', price='$price', description='$description', category='$category', weight='$weight', country='$country' $updateMediaSQL WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'updated' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $conn->error]);
        }
    } else {
        // Insert product
        $sql = "INSERT INTO products (product_name, price, media, description, category, weight, country) 
                VALUES ('$product_name', '$price', '$media', '$description', '$category', '$weight', '$country')";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $conn->error]);
        }
    }

    exit();
}


if ($method === 'PUT') {
    // Parse input
    parse_str(file_get_contents('php://input'), $_PUT);
    $id = (int)($_PUT['id'] ?? 0);
    $product_name = sanitize($_PUT['product_name'] ?? '');
    $price = sanitize($_PUT['price'] ?? '');
    $description = sanitize($_PUT['description'] ?? '');
    $category = sanitize($_PUT['category'] ?? '');
    $weight = sanitize($_PUT['weight'] ?? '');
    $country = sanitize($_PUT['country'] ?? '');
    // Media update not supported in PUT (use POST for new upload)
    $sql = "UPDATE products SET product_name='$product_name', price='$price', description='$description', category='$category', weight='$weight', country='$country' WHERE id=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
    exit();
}

if ($method === 'DELETE') {
    parse_str(file_get_contents('php://input'), $_DELETE);
    $id = (int)($_DELETE['id'] ?? 0);
    $sql = "DELETE FROM products WHERE id=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
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