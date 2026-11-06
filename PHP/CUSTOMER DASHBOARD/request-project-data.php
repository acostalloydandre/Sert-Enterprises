<?php
/**
 * SERT Enterprise - Project Request Data Layer & Handler
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

$clientId = $_SESSION['customer_id'] ?? 1; 
$successMessage = '';
$errorMessage = '';

$customerInfo = ['customer_name' => 'Valued Client'];
if ($pdo) {
    try {
        $custStmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
        $custStmt->execute([$clientId]);
        $fetched = $custStmt->fetch();
        if ($fetched) {
            $customerInfo['customer_name'] = $fetched['customer_name'] ?? ($fetched['name'] ?? 'Valued Client');
        }
    } catch (PDOException $e) {}
}

// Allowed Official Service Categories
$allowedServices = [
    'Large Format Printing',
    'Panaflex Signage',
    'Acrylic Works',
    'Metal Works',
    'Wood Works',
    'Window Blinds',
    'Carpets',
    'Office Furniture & Partitions'
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    $projectName = trim($_POST['project_name'] ?? '');
    $serviceType = $_POST['service_type'] ?? '';
    $targetDate = $_POST['target_date'] ?? ''; 
    $dimensions = trim($_POST['dimensions'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 1);
    $notes = trim($_POST['notes'] ?? '');
    
    $filePath = null;

    if (empty($projectName)) {
        $errorMessage = "Please provide a project or inquiry title.";
    } elseif (!in_array($serviceType, $allowedServices)) {
        $errorMessage = "Please select a valid service category.";
    } else {
        // Handle File Upload if provided
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['attachment']['tmp_name'];
            $fileName = $_FILES['attachment']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['ai', 'psd', 'pdf', 'png', 'jpg', 'jpeg', 'zip'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadFileDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                
                $newFileName = 'proj_' . time() . '_' . mt_rand(1000, 9999) . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $filePath = 'uploads/' . $newFileName;
                }
            } else {
                $errorMessage = "Invalid file type. Allowed formats: AI, PSD, PDF, PNG, JPG, ZIP.";
            }
        }
    }

    if (empty($errorMessage) && $pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO custom_orders (customer_id, project_name, service_type, deadline, dimensions, quantity, notes, attachment_path, status, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending Approval', NOW())");
            $stmt->execute([$clientId, $projectName, $serviceType, $targetDate, $dimensions, $quantity, $notes, $filePath]);
            
            $successMessage = "Project request submitted successfully! Our team will review your specifications and adjust the timeline upon quotation approval.";
        } catch (PDOException $e) {
            try {
                $fallbackStmt = $pdo->prepare("INSERT INTO orders (customer_id, order_type, amount, status, order_date) VALUES (?, ?, '₱0.00', 'Pending Approval', NOW())");
                $fallbackStmt->execute([$clientId, $serviceType . ' - ' . $projectName]);
                $successMessage = "Project request submitted successfully!";
            } catch (PDOException $ex) {
                $errorMessage = "Database error: Could not save request.";
            }
        }
    }
}
?>