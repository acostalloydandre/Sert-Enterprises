<?php
/**
 * SERT Enterprise - Customer Portal Data Layer
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

$customerInfo = [
    'customer_name' => 'Valued Client',
    'email' => 'client@sertenterprise.com',
    'company' => 'SERT Partner'
];
$clientProjects = [];
$clientOrders = [];
$pendingOrders = [];
$unpaidDownpayments = [];
$siteVisits = [];
$calendarEvents = [];

if ($pdo) {
    try {
        // Fetch customer profile
        $custStmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
        $custStmt->execute([$clientId]);
        $fetchedCustomer = $custStmt->fetch();
        
        if ($fetchedCustomer) {
            $customerInfo['customer_name'] = $fetchedCustomer['customer_name'] ?? ($fetchedCustomer['name'] ?? 'Valued Client');
            $customerInfo['email'] = $fetchedCustomer['email'] ?? '';
            $customerInfo['company'] = $fetchedCustomer['company'] ?? 'Client Partner';
        }

        // Fetch active projects
        $projStmt = $pdo->prepare("SELECT * FROM projects WHERE customer_id = ? ORDER BY deadline ASC");
        $projStmt->execute([$clientId]);
        $clientProjects = $projStmt->fetchAll();

        // Fetch orders history
        $ordStmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
        $ordStmt->execute([$clientId]);
        $clientOrders = $ordStmt->fetchAll();

        // Fetch pending orders
        $pendStmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? AND (status = 'Pending' OR status = 'For quote') ORDER BY order_date DESC");
        $pendStmt->execute([$clientId]);
        $pendingOrders = $pendStmt->fetchAll();

        // Fetch projects or orders with unpaid downpayment status
        $downStmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? AND (downpayment_status = 'Unpaid' OR payment_status = 'Unpaid Downpayment' OR status = 'Awaiting Downpayment') ORDER BY order_date DESC");
        $downStmt->execute([$clientId]);
        $unpaidDownpayments = $downStmt->fetchAll();

        // Fetch upcoming Site Visits for this client
        $siteStmt = $pdo->prepare("SELECT * FROM calendar_events WHERE (event_type LIKE '%site visit%' OR phase LIKE '%site visit%') AND project_id IN (SELECT project_id FROM projects WHERE customer_id = ?) ORDER BY event_date ASC");
        $siteStmt->execute([$clientId]);
        $siteVisits = $siteStmt->fetchAll();

        // Fetch all calendar events for tooltips
        $calStmt = $pdo->prepare("SELECT event_date, project_name, project_id, client_name, event_type, phase, progress FROM calendar_events WHERE project_id IN (SELECT project_id FROM projects WHERE customer_id = ?)");
        $calStmt->execute([$clientId]);
        while ($row = $calStmt->fetch()) {
            $date = $row['event_date'];
            $calendarEvents[$date][] = [
                "name" => $row['project_name'],
                "id" => $row['project_id'],
                "client" => $row['client_name'],
                "type" => $row['event_type'],
                "phase" => $row['phase'],
                "progress" => (int)$row['progress']
            ];
        }

    } catch (PDOException $e) {
        $clientProjects = [];
        $clientOrders = [];
        $pendingOrders = [];
        $unpaidDownpayments = [];
        $siteVisits = [];
        $calendarEvents = [];
    }
}

$currentYear = date('Y');
$currentMonth = date('m');
$currentMonthYearHeading = date('F Y');
$todayToolbarLabel = 'Today, ' . date('F j');
$totalDaysInMonth = (int)date('t');
$currentDay = (int)date('j');
$firstDayOfWeek = (int)date('w', strtotime("$currentYear-$currentMonth-01"));

if (!function_exists('getDotClass')) {
    function getDotClass($phase) {
        $phaseLower = strtolower($phase);
        if (strpos($phaseLower, 'installation') !== false) return 'green-dot';
        if (strpos($phaseLower, 'planning') !== false) return 'orange-dot';
        if (strpos($phaseLower, 'design') !== false) return 'purple-dot';
        if (strpos($phaseLower, 'develop') !== false) return 'blue-dot';
        if (strpos($phaseLower, 'delivery') !== false) return 'gold-dot';
        if (strpos($phaseLower, 'site visit') !== false) return 'red-dot';
        return 'green-dot';
    }
}
?>