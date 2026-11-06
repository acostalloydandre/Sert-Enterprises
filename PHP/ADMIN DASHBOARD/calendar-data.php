<?php
/**
 * SERT Enterprise - Dedicated Calendar Data Layer
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

// Calendar Month / Year Navigation Controls
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');

$currentMonthName = date('F', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
$currentMonthYearHeading = $currentMonthName . ' ' . $currentYear;
$todayToolbarLabel = 'Today, ' . date('F j, Y');

$totalDaysInMonth = (int)date('t', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
$firstDayOfWeek = (int)date('w', strtotime("$currentYear-$currentMonth-01"));

// Navigation link calculations for previous/next month buttons
$prevMonth = $currentMonth - 1;
$prevYear = $currentYear;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $currentMonth + 1;
$nextYear = $currentYear;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

$calendarEvents = [];

if ($pdo) {
    try {
        // Fetch calendar events or project milestones mapped by date
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
    } catch (PDOException $e) {
        $calendarEvents = [];
    }
}

// Helper function for legend dots
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