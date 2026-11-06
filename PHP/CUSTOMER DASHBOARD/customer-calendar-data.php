<?php
/**
 * SERT Enterprise - Customer Calendar Data Layer
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
$customerInfo = ['customer_name' => 'Valued Client'];
$calendarEvents = [];

$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

if ($pdo) {
    try {
        $custStmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
        $custStmt->execute([$clientId]);
        $fetched = $custStmt->fetch();
        if ($fetched) {
            $customerInfo['customer_name'] = $fetched['customer_name'] ?? ($fetched['name'] ?? 'Valued Client');
        }

        // Fetch client orders and project deadlines for the calendar grid
        $eventStmt = $pdo->prepare("SELECT order_id as id, order_type as title, order_date as event_date, 'Order' as type FROM orders WHERE customer_id = ? 
                                    UNION 
                                    SELECT project_id as id, project_name as title, deadline as event_date, 'Deadline' as type FROM projects WHERE customer_id = ?");
        $eventStmt->execute([$clientId, $clientId]);
        $rawEvents = $eventStmt->fetchAll();

        foreach ($rawEvents as $ev) {
            if (!empty($ev['event_date'])) {
                $dateKey = date('Y-m-d', strtotime($ev['event_date']));
                $calendarEvents[$dateKey][] = $ev;
            }
        }

    } catch (PDOException $e) {
        $calendarEvents = [];
    }
}
?>