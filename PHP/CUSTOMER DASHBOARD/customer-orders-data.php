<?php
/**
 * SERT Enterprise - Customer Orders Data Layer
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
$currentFilter = $_GET['status'] ?? 'all'; // 'all', 'Pending Approval', 'Pending Site Visit', 'Pending Quotation', 'Pending Downpayment'
$customerOrders = [];
$customerInfo = ['customer_name' => 'Valued Client'];

if ($pdo) {
    try {
        // Fetch client details
        $custStmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
        $custStmt->execute([$clientId]);
        $fetched = $custStmt->fetch();
        if ($fetched) {
            $customerInfo['customer_name'] = $fetched['customer_name'] ?? ($fetched['name'] ?? 'Valued Client');
        }

        // Base query for client orders
        $query = "SELECT * FROM orders WHERE customer_id = ?";
        $params = [$clientId];

        // Apply status filter matching admin categories
        if ($currentFilter === 'Pending Approval') {
            $query .= " AND status = 'Pending Approval'";
        } elseif ($currentFilter === 'Pending Site Visit') {
            $query .= " AND status = 'Pending Site Visit'";
        } elseif ($currentFilter === 'Pending Quotation') {
            $query .= " AND (status = 'Pending Quotation' OR status = 'For quote')";
        } elseif ($currentFilter === 'Pending Downpayment') {
            $query .= " AND (status = 'Pending Downpayment' OR downpayment_status = 'Unpaid')";
        }

        $query .= " ORDER BY order_date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $customerOrders = $stmt->fetchAll();

    } catch (PDOException $e) {
        $customerOrders = [];
    }
}
?>