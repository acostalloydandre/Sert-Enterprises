<?php
/**
 * SERT Enterprise - Custom Orders Data Layer
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
    'All',
    'Large Format Printing',
    'Panaflex Signage',
    'Acrylic Works',
    'Metal Works',
    'Wood Works',
    'Window Binds',
    'Carpets',
    'Office Furniture & Partitions'
];

$selectedCategory = $_GET['category'] ?? 'All';
$selectedClientName = $_GET['client'] ?? null;
$clients = [];
$selectedClientCustomOrders = [];
$totalCustomOrdersCount = 0;

if ($pdo) {
    try {
        // Total count for sidebar badge
        $totalCustomOrdersCount = $pdo->query("SELECT COUNT(*) FROM custom_orders")->fetchColumn();
    } catch (PDOException $e) {
        $totalCustomOrdersCount = 0;
    }

    try {
        // Fetch clients based on category filter
        if ($selectedCategory === 'All') {
            $stmt = $pdo->query("SELECT DISTINCT customer_name, COUNT(custom_order_id) as total_requests FROM custom_orders GROUP BY customer_name ORDER BY customer_name ASC");
            $clients = $stmt->fetchAll();
        } else {
            $stmt = $pdo->prepare("SELECT DISTINCT customer_name, COUNT(custom_order_id) as total_requests FROM custom_orders WHERE category = ? GROUP BY customer_name ORDER BY customer_name ASC");
            $stmt->execute([$selectedCategory]);
            $clients = $stmt->fetchAll();
        }
    } catch (PDOException $e) {
        $clients = [];
    }

    // Default to the first client if none is selected
    if (!$selectedClientName && !empty($clients)) {
        $selectedClientName = $clients[0]['customer_name'];
    }

    // Fetch custom order records for the active client
    if ($selectedClientName) {
        try {
            if ($selectedCategory === 'All') {
                $stmt = $pdo->prepare("SELECT * FROM custom_orders WHERE customer_name = ? ORDER BY request_date DESC");
                $stmt->execute([$selectedClientName]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM custom_orders WHERE customer_name = ? AND category = ? ORDER BY request_date DESC");
                $stmt->execute([$selectedClientName, $selectedCategory]);
            }
            $selectedClientCustomOrders = $stmt->fetchAll();
        } catch (PDOException $e) {
            $selectedClientCustomOrders = [];
        }
    }
}
?>