<?php
/**
 * SERT Enterprise - Products Data Layer (Seller Central Style)
 */
date_default_timezone_set('Asia/Manila');

$host = 'localhost';
$dbname = 'sert_enterprise';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    $pdo = null;
}

$categories = [
    'All Categories',
    'Large Format Printing',
    'Panaflex Signage',
    'Acrylic Works',
    'Metal Works',
    'Wood Works',
    'Window Binds',
    'Carpets',
    'Office Furniture & Partitions'
];

$selectedCategory = $_GET['category'] ?? 'All Categories';
$selectedProductId = $_GET['product_id'] ?? null;
$successMessage = '';
$errorMessage = '';

// Handle Form Submission (Add / Update Product)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_product') {
        $productId = $_POST['product_id'] ?? null;
        $productName = trim($_POST['product_name'] ?? '');
        $category = $_POST['category'] ?? '';
        $dimensions = trim($_POST['dimensions'] ?? '');
        $height = trim($_POST['height'] ?? '');
        $price = trim($_POST['price'] ?? 0);
        $status = $_POST['status'] ?? 'Active';
        $description = trim($_POST['description'] ?? '');
        $materials = trim($_POST['materials'] ?? '');

        // Image Upload Handling
        $imagePath = $_POST['existing_image'] ?? '';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['product_image']['tmp_name'];
            $fileName = $_FILES['product_image']['name'];
            $uploadDir = '../../Assets/Products/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = time() . '_' . preg_replace("/[^a-zA-Z0-9._]/", "", $fileName);
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagePath = 'Assets/Products/' . $newFileName;
            }
        }

        if (!empty($productName) && !empty($category)) {
            if ($productId) {
                // Update existing product
                $stmt = $pdo->prepare("UPDATE products SET product_name = ?, category = ?, dimensions = ?, height = ?, materials = ?, price = ?, status = ?, description = ?, image_path = ? WHERE product_id = ?");
                $stmt->execute([$productName, $category, $dimensions, $height, $materials, $price, $status, $description, $imagePath, $productId]);
                $successMessage = "Product successfully updated!";
                $selectedProductId = $productId;
            } else {
                // Insert new product
                $stmt = $pdo->prepare("INSERT INTO products (product_name, category, dimensions, height, materials, price, status, description, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$productName, $category, $dimensions, $height, $materials, $price, $status, $description, $imagePath]);
                $successMessage = "New product successfully listed!";
                $selectedProductId = $pdo->lastInsertId();
            }
        } else {
            $errorMessage = "Product Name and Category are required fields.";
        }
    }
}

// Fetch products list for the directory
$productsList = [];
$productDetails = null;
$totalProductsCount = 0;

if ($pdo) {
    try {
        $totalProductsCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    } catch (PDOException $e) {
        $totalProductsCount = 0;
    }

    try {
        if ($selectedCategory === 'All Categories') {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY product_id DESC");
            $productsList = $stmt->fetchAll();
        } else {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY product_id DESC");
            $stmt->execute([$selectedCategory]);
            $productsList = $stmt->fetchAll();
        }
    } catch (PDOException $e) {
        $productsList = [];
    }

    // Default to the first product if none is selected
    if (!$selectedProductId && !empty($productsList)) {
        $selectedProductId = $productsList[0]['product_id'];
    }

    if ($selectedProductId === 'new') {
        $productDetails = [
            'product_id' => '',
            'product_name' => '',
            'category' => $selectedCategory !== 'All Categories' ? $selectedCategory : 'Large Format Printing',
            'dimensions' => '',
            'height' => '',
            'price' => '',
            'status' => 'Active',
            'description' => '',
            'image_path' => ''
        ];
    } else if ($selectedProductId) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
            $stmt->execute([$selectedProductId]);
            $productDetails = $stmt->fetch();
        } catch (PDOException $e) {
            $productDetails = null;
        }
    }
}
?>