<?php
/**
 * SERT Enterprise - Customers Directory Data Layer (Registered Only)
 */
date_default_timezone_set('Asia/Manila');

$host = 'localhost';$dbname = 'sert_enterprise';
$username = 'root';$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username,$password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {$pdo = null;
}

$selectedCustomerId =$_GET['customer_id'] ?? null;
$customersList = [];$customerDetails = null;
$customerProjects = [];

if ($pdo) {
    try {
        // STRICT FILTER: Only fetch customers with a verified system account (has_account = 1)
        $query = "SELECT * FROM customers WHERE has_account = 1 ORDER BY customer_name ASC";
        $stmt =$pdo->prepare($query);$stmt->execute();
        $customersList =$stmt->fetchAll();
    } catch (PDOException $e) {$customersList = [];
    }

    if ($selectedCustomerId) {
        try {
            $stmt =$pdo->prepare("SELECT * FROM customers WHERE customer_id = ? AND has_account = 1");
            $stmt->execute([$selectedCustomerId]);
            $customerDetails =$stmt->fetch();

            if ($customerDetails) {
                $projStmt =$pdo->prepare("SELECT project_id, project_name, order_type, current_phase, progress, status, deadline FROM projects WHERE customer_name = ? ORDER BY deadline DESC");
                $projStmt->execute([$customerDetails['customer_name']]);
                $customerProjects =$projStmt->fetchAll();
            }
        } catch (PDOException $e) {$customerDetails = null;
            $customerProjects = [];
        }
    }
}
?>