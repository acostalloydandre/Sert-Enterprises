<?php
/**
 * SERT Enterprise - Premade Orders Data Layer
 */
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

$clients = [];
$selectedClientOrders = [];
$selectedClientName = $_GET['client'] ?? null;
$totalOrdersCount = 0; // Initialize badge count

if ($pdo) {
    try {
        // 1. Get total count of premade orders for the sidebar badge
        $countStmt = $pdo->query("SELECT COUNT(*) FROM orders");
        $totalOrdersCount = $countStmt->fetchColumn();
    } catch (PDOException $e) {
        $totalOrdersCount = 0;
    }

    try {
        // 2. Fetch unique clients for the left directory list
        $clientsStmt = $pdo->query("SELECT DISTINCT customer_name, COUNT(order_id) as total_orders FROM orders GROUP BY customer_name ORDER BY customer_name ASC");
        $clients = $clientsStmt->fetchAll();
    } catch (PDOException $e) {
        $clients = [];
    }

    // 3. DEFAULT BEHAVIOR: If no client is clicked yet, grab the customer of the latest order
    if (!$selectedClientName && !empty($clients)) {
        try {
            $latestOrderStmt = $pdo->query("SELECT customer_name FROM orders ORDER BY order_date DESC LIMIT 1");
            $latestOrder = $latestOrderStmt->fetch();
            if ($latestOrder) {
                $selectedClientName = $latestOrder['customer_name'];
            }
        } catch (PDOException $e) {
            $selectedClientName = $clients[0]['customer_name'] ?? null;
        }
    }

    // 4. Fetch orders for the selected (or default latest) client
    if ($selectedClientName) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_name = ? ORDER BY order_date DESC");
            $stmt->execute([$selectedClientName]);
            $selectedClientOrders = $stmt->fetchAll();
        } catch (PDOException $e) {
            $selectedClientOrders = [];
        }
    }
}
?>