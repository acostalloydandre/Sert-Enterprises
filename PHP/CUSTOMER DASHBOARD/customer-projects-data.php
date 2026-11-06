<?php
/**
 * SERT Enterprise - Customer Project Tracker Data Layer
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
$clientProjects = [];
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

        // Fetch all projects for this customer
        $projStmt = $pdo->prepare("SELECT * FROM projects WHERE customer_id = ? ORDER BY deadline ASC");
        $projStmt->execute([$clientId]);
        $clientProjects = $projStmt->fetchAll();

    } catch (PDOException $e) {
        $clientProjects = [];
    }
}
?>