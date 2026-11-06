<?php
/**
 * SERT Enterprise - Dashboard Data Layer
 * Connects to MySQL using PDO and fetches dynamic data with 0 and text fallbacks.
 */
date_default_timezone_set('Asia/Manila');

// --- 1. DATABASE CONFIGURATION ---
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

// --- 2. SAFE DEFAULT VALUES ---
$newOrdersCount = 0;
$pendingQuotesCount = 0;
$activeProjectsCount = 0;
$pendingSiteVisitsCount = 0;

$attentionItems = [];
$activeProjects = [];
$recentOrders = [];
$messages = [];
$calendarEvents = [];

if ($pdo) {
    // A. Summary Counts
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'Pending'");
        $row = $stmt->fetch();
        $newOrdersCount = isset($row['total']) ? (int)$row['total'] : 0;
    } catch (PDOException $e) { $newOrdersCount = 0; }

    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM custom_orders WHERE status = 'For quote'");
        $row = $stmt->fetch();
        $pendingQuotesCount = isset($row['total']) ? (int)$row['total'] : 0;
    } catch (PDOException $e) { $pendingQuotesCount = 0; }

    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM projects WHERE status = 'Ongoing'");
        $row = $stmt->fetch();
        $activeProjectsCount = isset($row['total']) ? (int)$row['total'] : 0;
    } catch (PDOException $e) { $activeProjectsCount = 0; }

    // Replaced scheduled activities count with Pending Site Visits query
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM calendar_events WHERE event_type LIKE '%site visit%' OR phase LIKE '%site visit%'");
        $row = $stmt->fetch();
        $pendingSiteVisitsCount = isset($row['total']) ? (int)$row['total'] : 0;
    } catch (PDOException $e) { $pendingSiteVisitsCount = 0; }

    // B. Priority Tasks / Attention Items
    try {
        $attentionItems = $pdo->query("SELECT symbol, symbol_class, title, meta, status_text, status_class, url FROM attention_items LIMIT 5")->fetchAll();
    } catch (PDOException $e) { $attentionItems = []; }

    // C. Active Projects Table Data
    try {
        $activeProjects = $pdo->query("SELECT project_id, project_name, customer_name, service_type, progress, deadline, status, risk_class FROM projects LIMIT 5")->fetchAll();
    } catch (PDOException $e) { $activeProjects = []; }

    // D. Recent Orders Table Data
    try {
        $recentOrders = $pdo->query("SELECT order_id, customer_name, order_type, order_date, amount, status, status_class FROM orders ORDER BY order_date DESC LIMIT 5")->fetchAll();
    } catch (PDOException $e) { $recentOrders = []; }

    // E. Recent Messages
    try {
        $messages = $pdo->query("SELECT sender_name, message_snippet, time_sent, is_unread, initials, avatar_class, url FROM messages ORDER BY time_sent DESC LIMIT 3")->fetchAll();
    } catch (PDOException $e) { $messages = []; }

    // F. Calendar Events
    try {
        $stmt = $pdo->query("SELECT event_date, project_name, project_id, client_name, event_type, phase, progress FROM calendar_events");
        while ($row = $stmt->fetch()) {
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
    } catch (PDOException $e) { $calendarEvents = []; }
}

// --- 3. ADDITIONAL DYNAMIC COUNTS FOR SUBTEXTS ---
$onTrackCount = 0;
$atRiskCount = 0;
$ordersLastWeekCount = 0;

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT status, COUNT(*) as total FROM projects GROUP BY status");
        while ($row = $stmt->fetch()) {
            if (strtolower($row['status']) === 'on track' || strtolower($row['status']) === 'approved') {
                $onTrackCount += (int)$row['total'];
            } else if (strtolower($row['status']) === 'at risk' || strtolower($row['status']) === 'delayed') {
                $atRiskCount += (int)$row['total'];
            }
        }
    } catch (PDOException $e) { 
        $onTrackCount = 0; 
        $atRiskCount = 0; 
    }

    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $row = $stmt->fetch();
        $ordersLastWeekCount = isset($row['total']) ? (int)$row['total'] : 0;
    } catch (PDOException $e) { 
        $ordersLastWeekCount = 0; 
    }
}

// --- 4. BUILD SUMMARY CARDS ARRAY WITH PENDING SITE VISITS CARD ---
$summaryCards = [
    [
        'icon' => '▤', 'icon_color' => 'orange', 'tag' => 'Needs review', 'tag_class' => 'tag-orange',
        'label' => 'New Orders', 'value' => $newOrdersCount, 
        'foot' => $ordersLastWeekCount > 0 ? '<span class="trend">+' . $ordersLastWeekCount . '</span> since last week' : 'No new orders last week'
    ],
    [
        'icon' => '▧', 'icon_color' => 'purple', 'tag' => 'Awaiting quote', 'tag_class' => 'tag-purple',
        'label' => 'Pending Quotes', 'value' => $pendingQuotesCount, 
        'foot' => 'Custom orders to review'
    ],
    [
        'icon' => '▣', 'icon_color' => 'green', 'tag' => 'Ongoing', 'tag_class' => 'tag-green',
        'label' => 'Active Projects', 'value' => $activeProjectsCount, 
        'foot' => '<span class="trend">' . $onTrackCount . '</span> on track · ' . $atRiskCount . ' at risk'
    ],
    [
        'icon' => '◷', 'icon_color' => 'blue', 'tag' => 'Action required', 'tag_class' => 'tag-blue',
        'label' => 'Pending Site Visits', 'value' => $pendingSiteVisitsCount, 
        'foot' => 'Awaiting scheduling & confirmation'
    ]
];

// --- 5. AUTOMATED CALENDAR GENERATION LOGIC ---
$currentYear = date('Y');
$currentMonth = date('m');
$currentMonthName = date('F');
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